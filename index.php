<!-- ** TOPページ の中身は html のみです -->
<!-- ** ファイルの拡張子は、.php ですが -->
<!-- ** PHPは、<?PHP ?> 以外の場所は、そのまま出力しますので -->
<!-- ** html の内容でも問題ありません -->
<?php
session_start();
if (!isset($_SESSION['login_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!-- ** ↓ここから入力して下さい -->
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
        <h2>TopPage
            <a href="logout.php" style="float: right;color: black;font-size: 0.8em;">[ログアウト]</a>
        </h2>

    </div>
    <div>
        <form action="input.php" method="post" name="form">
            <button type="submit">登録画面</button>
        </form>
        <form action="dashboard.php" method="post" name="form">
            <button type="submit">ダッシュボード</button>
        </form>
        <?php if ($_SESSION['user_permissions'] == 1): ?>
            <form action="Csvpreview.php" method="post" name="form">
                <button type="submit">住所マスタ更新</button>
            </form>
            <form action="login_dashboard.php" method="post" name="form">
                <button type="submit">ログインユーザー管理</button>
            </form>
        <?php else : ?>
            <form action="user_profile.php" method="post" name="form">
                <button type="submit">マイページ</button>
            </form>
        <?php endif ?>
    </div>
</body>

</html>