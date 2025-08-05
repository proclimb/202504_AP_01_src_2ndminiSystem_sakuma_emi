<?php
//ユーザー情報のDB操作処理
class Login
{
    private $pdo;

    //DB接続情報
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ユーザ登録
    public function create($data)
    {
        $sql = "INSERT INTO
                    login_user (
                    name,
                    birth_date,
                    login_id,
                    password,
                    password_hash,
                    created_at
                    )
                VALUES (
                    :name,
                    :birth_date,
                    :login_id,
                    :password,
                    :password_hash,
                    now()
                    )";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'             => $data['name'],
            ':birth_date'       => $data['birth_year'] . "-" . $data['birth_month'] . "-" . $data['birth_day'],
            ':login_id'         => $data['login_id'],
            ':password'         => $data['password'],
            ':password_hash'    => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
    }

    // ユーザ更新
    public function update($data)
    {
        $sql = "UPDATE
                    login_user
                SET login_id = :login_id,
                    password = :password,
                    password_hash = :password_hash,
                    user_permissions = COALESCE(:user_permissions, user_permissions)
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':login_id'         => $data['login_id'],
            ':password'         => $data['password'],
            ':password_hash'    => password_hash($data['password'], PASSWORD_DEFAULT),
            ':user_permissions' => $data['user_permissions'],
            ':id'           => $data['id']
        ]);
    }

    // ユーザ検索(1件検索)
    public function findById($id)
    {
        $sql = "SELECT
                id,
                name,
                birth_date,
                login_id,
                password,
                password_hash,
                user_permissions
            FROM login_user
            WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // ユーザ削除
    public function delete($id)
    {
        $sql = "UPDATE
            login_user
            SET del_flag = 1
            WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }



    // ユーザ検索(キーワード検索、全件検索)
    // ＊システム開発演習Ⅰで、キーワード検索機能は実装しない
    public function search($keyword = '')
    {
        $sql = "SELECT
                id,
                name,
                birth_date,
                login_id,
                password,
                password_hash,
                user_permissions
            FROM login_user
            WHERE del_flag = 0
            ";

        if ($keyword) {
            $sql .= " AND name LIKE :keyword";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':keyword' => "%{$keyword}%"]);
        } else {
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ① キーワード検索後の「総件数」を返すメソッド
     *
     * @param string|null $keyword  名前の部分一致キーワード（空文字 or null は検索なし＝全件）
     * @return int                  マッチしたレコード数
     */
    public function countUsersWithKeyword(?string $keyword): int
    {
        $sql = "SELECT COUNT(*) AS cnt
                FROM login_user
                WHERE del_flag = 0
        ";
        $params = [];
        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " AND name LIKE :keyword ";
            $params[':keyword'] = '%' . trim($keyword) . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        if (isset($params[':keyword'])) {
            $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['cnt'];
    }


    /**
     * ② キーワード検索＋ソート＋ページネーションでユーザー一覧を取得する
     *
     * @param string|null $keyword   名前の部分一致キーワード（空文字 or null で全件）
     * @param string|null $sortBy    ソート対象カラム名: 'kana' or 'postal_code' or 'email'
     * @param string|null $sortOrder 'asc' or 'desc'
     * @param int         $offset    SQL OFFSET
     * @param int         $limit     SQL LIMIT
     * @return array                  取得したユーザー一覧（連想配列の行リスト）
     */
    public function fetchUsersWithKeyword(
        ?string $keyword,
        ?string $sortBy,
        ?string $sortOrder,
        int $offset,
        int $limit
    ): array {
        // 基本の SELECT 文（search() と同様の JOIN 構造）
        $sql = "SELECT
                    id,
                    name,
                    birth_date,
                    login_id,
                    password,
                    password_hash,
                    user_permissions
                FROM login_user
                WHERE del_flag = 0
        ";
        $params = [];

        // (1) キーワード検索 条件追加
        if ($keyword !== null && trim($keyword) !== '') {
            $sql .= " AND name LIKE :keyword ";
            $params[':keyword'] = '%' . trim($keyword) . '%';
        }

        // (2) ソート 条件追加
        $allowedSort = ['name', 'birth_date', 'login_id'];
        if ($sortBy !== null && in_array($sortBy, $allowedSort, true)) {
            $column = '';
            if ($sortBy === 'name') {
                $column = 'name';
            } elseif ($sortBy === 'birth_date') {
                $column = 'birth_date';
            } elseif ($sortBy === 'login_id') {
                $column = 'login_id';
            }
            $order = (strtolower($sortOrder) === 'desc') ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$column} {$order} ";
        } else {
            // デフォルト: u.id 昇順
            $sql .= " ORDER BY id ASC ";
        }

        // (3) LIMIT & OFFSET
        $sql .= " LIMIT :lim OFFSET :off ";

        $stmt = $this->pdo->prepare($sql);
        // バインド: キーワード
        if (isset($params[':keyword'])) {
            $stmt->bindValue(':keyword', $params[':keyword'], PDO::PARAM_STR);
        }
        // バインド: LIMIT, OFFSET
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function searchId($id)
    {
        $sql = "SELECT id
            FROM login_user
            WHERE login_id = ? AND del_flag = 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function searchByBirthday($birthdate)
    {
        $sql = "SELECT id,name,login_id
        FROM login_user
        WHERE birth_date=:birth_date AND del_flag=0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':birth_date' => $birthdate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
