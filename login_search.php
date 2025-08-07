<?php
//  1.DB接続情報、クラス定義の読み込み
require_once 'Db.php';
require_once 'login_User.php';

// ---------------------------------------------
// 1. リクエストパラメータ取得・初期化
// ---------------------------------------------

$user  = new Login($pdo);
$birthdate = sprintf('%04d-%02d-%02d', $_POST['birth_year'], $_POST['birth_month'], $_POST['birth_day']);

// ---------------------------------------------
// 3. 実際のユーザー一覧を取得
// ---------------------------------------------
$users = $user->searchByBirthday($birthdate);

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
        <h2>ユーザー検索結果</h2>
    </div>

    <div class="result-count" style="width:80%; margin: 5px auto 0;">
        生年月日：<?= $birthdate; ?>の検索結果
    </div>

    <!-- 6. 一覧テーブル -->
    <table class="common-table" style="width: 50%;">
        <tr>
            <th style="width: 35%;">お名前</th>
            <th style="width: 35%;">ログインID</th>
            <th style="width: 30%;">ログインID・パスワードの確認・変更</th>
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
                    <td><?= htmlspecialchars($val['name'], ENT_QUOTES); ?></td>
                    <td><?= htmlspecialchars(
                            mb_substr($val['login_id'], 0, 2, 'UTF-8')
                                . str_repeat('*', max(0, mb_strlen($val['login_id'], 'UTF-8') - 2)),
                            ENT_QUOTES
                        ) ?></td>
                    <td>
                        <a href="login_edit.php?id=<?= htmlspecialchars($val['id'], ENT_QUOTES) ?>">確認</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <button type="button" onclick="location.href='login.php'">ログイン画面に戻る</button>
</body>

</html>