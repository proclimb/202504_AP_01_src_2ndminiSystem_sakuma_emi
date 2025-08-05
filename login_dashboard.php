<?php
//  1.DB接続情報、クラス定義の読み込み
require_once 'Db.php';
require_once 'login_User.php';
require_once 'login_Sort.php';      // ソート関連の処理と sortLink() 関数を定義
require_once 'login_Page.php';      // ページネーション関連の処理と paginationLinks() 関数を定義

// ---------------------------------------------
// 1. リクエストパラメータ取得・初期化
// ---------------------------------------------
$nameKeyword = '';
$sortBy      = $sortBy  ?? null;  // sort.php でセット済み
$sortOrd     = $sortOrd ?? 'asc'; // sort.php でセット済み
$page        = $page    ?? 1;     // page.php でセット済み

// 検索フォームで「検索」ボタンが押された場合
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_submit'])) {
    $nameKeyword = trim($_GET['search_name'] ?? '');
    // 検索時は常に1ページ目、ソートもリセット
    $sortBy  = null;
    $sortOrd = 'asc';
    $page    = 1;
} else {
    // 検索キーがある場合のみ受け取る
    $nameKeyword = trim($_GET['search_name'] ?? '');
    // ソートとページは sort.php / page.php により既にセット済み
}

// ---------------------------------------------
// 2. ページネーション用定数・総件数数取得
// ---------------------------------------------
$user  = new Login($pdo);
$totalCount = $user->countUsersWithKeyword($nameKeyword);

// 1ページあたりの表示件数
$limit = 10;

// ページネーション用パラメータを取得 (update $page, $offset, $totalPages)
list($page, $offset, $totalPages) = getPaginationParams($totalCount, $limit);

// ---------------------------------------------
// 3. 実際のユーザー一覧を取得
// ---------------------------------------------
$users = $user->fetchUsersWithKeyword(
    $nameKeyword,
    $sortBy,
    $sortOrd,
    $offset,
    $limit
);

// 3.html の描画
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mini System</title>
    <link rel="stylesheet" href="style_new.css">
</head>

<body>
    <div>
        <h1>mini System</h1>
    </div>
    <div>
        <h2>ユーザー一覧</h2>
    </div>
    <form method="get" action="login_dashboard.php" class="name-search-form" style="width:80%; margin: 20px auto;">
        <label for="search_name">名前で検索：</label>
        <input
            type="text"
            name="search_name"
            id="search_name"
            value="<?= htmlspecialchars($nameKeyword, ENT_QUOTES) ?>"
            placeholder="名前の一部を入力">
        <input type="submit" name="search_submit" value="検索">
        <a href="login_dashboard.php" style="margin-left: 32px;">[検索結果をリセット（全件表示）]</a>
        <button type="button" onclick="location.href='login_input.php'" style="float: right;margin-top: 0;">ユーザー登録</button>
    </form>

    <!-- 5. 検索結果件数表示（テーブルの左上へ置きたいので、幅80%・中央寄せして左寄せテキスト） -->
    <div class="result-count" style="width:80%; margin: 5px auto 0;">
        検索結果：<strong><?= $totalCount ?></strong> 件
    </div>

    <!-- 6. 一覧テーブル -->
    <table class="common-table">
        <tr>
            <th>編集</th>
            <!-- ② 名前 ソートリンク -->
            <th>
                <?= sortLink('name', '名前', $sortBy, $sortOrd, $nameKeyword) ?>
            </th>
            <!-- ③ 生年月日 ソートリンク -->
            <th>
                <?= sortLink('birth_date', '生年月日', $sortBy, $sortOrd, $nameKeyword) ?>
            </th>
            <!-- ① ログインID ソートリンク -->
            <th>
                <?= sortLink('login_id', 'ログインID', $sortBy, $sortOrd, $nameKeyword) ?>
            </th>
            <th>パスワード</th>
            <th>権限</th>
        </tr>

        <?php if (count($users) === 0): ?>
            <tr>
                <td colspan="11" style="text-align:center; padding:10px 0;">
                    該当するデータがありません。
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $val): ?>
                <tr>
                    <td>
                        <a href="login_edit.php?id=<?= htmlspecialchars($val['id'], ENT_QUOTES) ?>">編集</a>
                    </td>
                    <td><?= htmlspecialchars($val['name'], ENT_QUOTES); ?></td>
                    <td><?= date('Y年n月j日', htmlspecialchars(strtotime($val['birth_date']))); ?></td>
                    <td><?= htmlspecialchars($val['login_id'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($val['password'], ENT_QUOTES) ?></td>
                    <td><?= $val['user_permissions'] == '0' ? '一般' : ($val['user_permissions'] == '1' ? '管理者' : 'その他'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <!-- 7. ページネーション -->
    <?= paginationLinks($page, $totalPages, $nameKeyword, $sortBy, $sortOrd) ?>

    <!-- 8. 「TOPに戻る」ボタン -->
    <button type="button" onclick="location.href='index.php'">TOPに戻る</button>
</body>

</html>