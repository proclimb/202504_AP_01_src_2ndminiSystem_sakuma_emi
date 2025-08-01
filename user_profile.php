<?php
session_cache_limiter('none');
session_start();

if (!isset($_SESSION['login_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'Db.php';
require_once 'User.php';

$user = new User($pdo);


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
        <h2>マイページ
            <a href="logout.php" style="float: right;color: black;font-size: 0.8em;">[ログアウト]</a>
        </h2>
    </div>
    <div>
        <form action="edit.php" method="get">
            <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
            <h1 class="contact-title">登録内容確認</h1>
            <p>登録内容を修正したい場合は、「編集する」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>お名前</label>
                    <p><?= htmlspecialchars($_POST['name']) ?></p>
                </div>
                <div>
                    <label>ふりがな</label>
                    <p><?= htmlspecialchars($_POST['kana']) ?></p>
                </div>
                <div>
                    <label>性別</label>
                    <p><?php if ($_POST['gender_flag'] == '1') {
                            echo "男性";
                        } elseif ($_POST['gender_flag'] == '2') {
                            echo "女性";
                        } elseif ($_POST['gender_flag'] == '3') {
                            echo "その他";
                        } ?></p>
                </div>
                <div>
                    <label>生年月日</label>
                    <p><?= htmlspecialchars($_POST['birth_date']) ?></p>
                </div>
                <div>
                    <label>郵便番号</label>
                    <p><?= htmlspecialchars("〒"
                            . $_POST['postal_code']) ?></p>
                </div>
                <div>
                    <label>住所</label>
                    <p><?= htmlspecialchars($_POST['prefecture']
                            . $_POST['city_town']
                            . $_POST['building']) ?></p>
                </div>
                <div>
                    <label>電話番号</label>
                    <p><?= htmlspecialchars($_POST['tel']) ?></p>
                </div>
                <div>
                    <label>メールアドレス</label>
                    <p><?= htmlspecialchars($_POST['email']) ?></p>
                </div>
            </div>
            <button type="submit" name="submit">編集する</button>
        </form>
    </div>
</body>

</html>