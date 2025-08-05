<?php
session_start();
require_once 'Db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'])) {
        $error = '不正な操作です。';
    } elseif (empty($_POST['login_id']) || empty($_POST['password'])) {
        $error = 'ログインIDまたはパスワードが入力されていません';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM login_user
        WHERE login_id = :login_id AND del_flag=0');
        $stmt->execute([':login_id' => $_POST['login_id']]);
        $user = $stmt->fetch();
        if (password_verify($_POST['password'], $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['user_permissions'] = $user['user_permissions'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'ログインIDまたはパスワードが一致しません';
        }
    }
}

if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
}
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
        <h2>ログイン画面</h2>
    </div>
    <div>
        <form action="login.php" method="post">
            <h1 class="contact-title">ログイン情報入力</h1>
            <p>ログイン情報をご入力の上、「ログイン」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>ログインID<span>必須</span></label>
                    <input
                        type="text"
                        name="login_id"
                        id="Login_id"
                        placeholder="ログインIDを入力してください"
                        value="<?= htmlspecialchars($_POST['login_id']) ?>">
                </div>
                <div>
                    <label>パスワード<span>必須</span></label>
                    <input
                        type="text"
                        name="password"
                        id="password"
                        placeholder="パスワードを入力してください">
                    <?php if ($error): ?>
                        <div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                </div>
            </div>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
            <button type="submit">ログイン</button>
        </form>
        <form action="login_search.php" method="post">
            <h1 class="contact-title">ログインIDまたはパスワードを忘れた場合はこちら</h1>
            <p>生年月日をご入力の上、「検索」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>生年月日<span>必須</span></label>
                    <!-- 年プルダウン -->
                    <div class="birth-selects" id="birth_date">
                        <select name="birth_year" class="form-control" id="birth_year">
                            <option value="">年</option>
                            <?php
                            $currentYear = (int)date('Y');
                            for ($y = $currentYear; $y >= 1900; $y--) : ?>
                                <option value="<?= $y ?>"><?= $y ?>年</option>
                            <?php endfor ?>
                        </select>

                        <!-- 月プルダウン -->
                        <select name="birth_month" class="form-control" id="birth_month">
                            <option value="">月</option>
                            <?php
                            for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>"><?= $m ?>月</option>
                            <?php endfor ?>
                        </select>

                        <!-- 日プルダウン -->
                        <select name="birth_day" class="form-control" id="birth_day">
                            <option value="">日</option>
                            <?php
                            for ($d = 1; $d <= 31; $d++) : ?>
                                <option value="<?= $d ?>"><?= $d ?>日</option>
                            <?php endfor ?>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit">検索</button>
        </form>
    </div>
</body>

</html>