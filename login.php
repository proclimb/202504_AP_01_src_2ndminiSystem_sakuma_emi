<?php
session_start();
require_once 'Db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $csrf  = $_POST['_csrf'] ?? '';

    if (!hash_equals($_SESSION['_csrf'] ?? '', $csrf)) {
        $error = '不正な操作です。';
    } elseif ($email &&  $_POST['birth_year'] && $_POST['birth_month'] && $_POST['birth_day']) {
        $birthDate = sprintf(
            '%04d-%02d-%02d',
            trim($_POST['birth_year'] ?? ''),
            trim($_POST['birth_month'] ?? ''),
            trim($_POST['birth_day'] ?? '')
        );
        $stmt = $pdo->prepare('SELECT id FROM user_base WHERE email = :email AND birth_date = :birthdate');
        $stmt->execute([':email' => $email, ':birthdate' => $birthDate]);
        $user = $stmt->fetch();
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['login_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'メールアドレスまたは生年月日が一致しません';
        }
    } else {
        $error = '必要な情報を正しい形式で入力してください';
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
        <form method="post">
            <h1 class="contact-title">ログイン情報入力</h1>
            <p>ログイン情報をご入力の上、「ログイン」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>生年月日<span>必須</span></label>
                    <!-- 年プルダウン -->
                    <div class="birth-selects" id="birth_date">
                        <select name="birth_year" class="form-control" id="birth_year">
                            <option value="">年</option>
                            <?php
                            $currentYear = (int)date('Y');
                            for ($y = $currentYear; $y >= 1900; $y--) :
                                $sel = (isset($_POST['birth_year'])
                                    && $_POST['birth_year'] == $y)
                                    ? ' selected' : ''; ?>
                                <option value="<?= $y ?>"
                                    <?= $sel ?>><?= $y ?>年</option>
                            <?php endfor ?>
                        </select>

                        <!-- 月プルダウン -->
                        <select name="birth_month" class="form-control" id="birth_month">
                            <option value="">月</option>
                            <?php
                            for ($m = 1; $m <= 12; $m++) :
                                $sel = (isset($_POST['birth_month'])
                                    && $_POST['birth_month'] == $m)
                                    ? ' selected' : ''; ?>
                                <option value="<?= $m ?>"
                                    <?= $sel ?>><?= $m ?>月</option>
                            <?php endfor ?>
                        </select>

                        <!-- 日プルダウン -->
                        <select name="birth_day" class="form-control" id="birth_day">
                            <option value="">日</option>
                            <?php
                            for ($d = 1; $d <= 31; $d++) :
                                $sel = (isset($_POST['birth_day'])
                                    && $_POST['birth_day'] == $d)
                                    ? ' selected' : ''; ?>
                                <option value="<?= $d ?>"
                                    <?= $sel ?>><?= $d ?>日</option>
                            <?php endfor ?>
                        </select>
                    </div>
                    <div>
                        <label>メールアドレス<span>必須</span></label>
                        <input
                            type="text"
                            name="email"
                            id="email"
                            placeholder="例）guest@example.com"
                            value="<?= htmlspecialchars($_POST['email']) ?>">
                        <?php if ($error): ?>
                            <div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
            <button type="submit">ログイン</button>
        </form>
    </div>
</body>

</html>