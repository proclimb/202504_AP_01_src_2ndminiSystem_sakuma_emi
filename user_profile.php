<?php
session_cache_limiter('none');
session_start();

require_once 'Db.php';
require_once 'login_User.php';

$user = new Login($pdo);


$id = $_SESSION['login_id'];
$_POST = $user->findById($id);

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
        <h2>マイページ</h2>
    </div>
    <div>
        <form action="login_edit.php" method="get">
            <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
            <h1 class="contact-title">登録内容確認</h1>
            <p>登録内容を修正したい場合は、「編集する」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>お名前</label>
                    <p><?= htmlspecialchars($_POST['name']) ?></p>
                </div>
                <div>
                    <label>生年月日</label>
                    <p><?= htmlspecialchars($_POST['birth_date']) ?></p>
                </div>
                <div>
                    <label>ログインID</label>
                    <p><?= htmlspecialchars($_POST['login_id']) ?></p>
                </div>
                <div>
                    <label>パスワード</label>
                    <p><?= htmlspecialchars($_POST['password']) ?></p>
                </div>
            </div>
            <button type="submit" name="submit">編集する</button>
            <button type="button" onclick="location.href='index.php'" class="button-back">TOPに戻る</button>
        </form>
    </div>
</body>

</html>