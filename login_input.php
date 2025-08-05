<?php


//  1.DB接続情報、クラス定義の読み込み
require_once 'Db.php';
require_once 'login_User.php';

// 1.セッションの開始
session_cache_limiter('none');
session_start();

// 2.変数の初期化
// *$_POSTの値があるときは初期化しない
$error_message = [];

if (!empty($_POST)) {

    $user = new Login($pdo);

    if (empty($_POST['name'])) {
        $error_message['name'] = '名前が入力されていません';
    } elseif (mb_strlen($_POST['name']) > 50) {
        $error_message['name'] = '名前は50文字以内で入力してください';
    } elseif (preg_match('/[^ぁ-ゔ]/u', $_POST['name'])) {
        $error_message['name'] = '名前はひらがなで空白を入れずに入力してください';
    }


    if (empty($_POST['birth_year']) || empty($_POST['birth_month']) || empty($_POST['birth_day'])) {
        $error_message['birth_date'] = '生年月日が入力されていません';
    } elseif (checkdate($_POST['birth_year'] ?? '', $_POST['birth_month'] ?? '', $_POST['birth_day'] ?? '')) {
        $error_message['birth_date'] = '生年月日が正しくありません（存在しない日付です）';
    } else {
        $today = date("Y/m/d");
        $target_day = $_POST['birth_year'] . "/" . $_POST['birth_month'] . "/" . $_POST['birth_day'];
        if (strtotime($today) < strtotime($target_day)) {
            $error_message['birth_date'] = '生年月日が正しくありません（過去の日付を入力してください）';
        }
    }

    if (empty($_POST['login_id'])) {
        $error_message['login_id'] = 'ログインIDが入力されていません';
    } elseif (mb_strlen($_POST['login_id']) > 50 || mb_strlen($_POST['login_id']) < 5) {
        $error_message['login_id'] = 'ログインIDは5文字以上50文字以内で入力してください';
    } elseif (preg_match('/[^a-zA-Z0-9]/u', $_POST['login_id'])) {
        $error_message['login_id'] = '英数字以外が入力されています';
    } else {
        $same_id = $user->searchId($_POST['login_id']);
        if ($same_id) {
            $error_message['login_id'] = 'そのユーザーIDは既に使われています';
        }
    }

    if (empty($_POST['password'])) {
        $error_message['password'] = 'パスワードが入力されていません';
    } elseif (mb_strlen($_POST['password']) > 50 || mb_strlen($_POST['password']) < 5) {
        $error_message['password'] = 'パスワードは5文字以上50文字以内で入力してください';
    } elseif (preg_match('/[^a-zA-Z0-9]/u', $_POST['password'])) {
        $error_message['password'] = '英数字以外が入力されています';
    } elseif ($_POST['password'] !== $_POST['password_confirm']) {
        $error_message['password_confirm'] = 'パスワードが一致しません';
    }

    if (empty($error_message)) {
        if ($user->create($_POST)) {
            //登録成功
            echo "<script>alert('登録完了：ユーザー一覧に戻ります');
                window.location.href = 'login_dashboard.php'; </script>";
        } else {
            //登録失敗
            echo "<script>alert('登録失敗：もう一度やり直してください') </script>";
        }
    }
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
        <h2>ログインユーザー登録画面</h2>
    </div>
    <div>
        <form action="login_input.php" method="post" name="data">
            <h1 class="contact-title">登録内容入力</h1>
            <p>登録内容をご入力の上、「登録する」ボタンをクリックしてください。</p>
            <div>
                <div>
                    <label>お名前<span>必須</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="ひらがなで空白を入れずに入力してください"
                        value="<?= htmlspecialchars($_POST['name']) ?>">
                    <?php if (isset($error_message['name'])) : ?>
                        <div class="error-msg">
                            <?= htmlspecialchars($error_message['name']) ?></div>
                    <?php endif ?>
                </div>
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
                    <?php if (isset($error_message['birth_date'])) : ?>
                        <div class="error-msg2">
                            <?= htmlspecialchars($error_message['birth_date']) ?></div>
                    <?php endif ?>
                </div>
                <div>
                    <label>ログインID<span>必須</span></label>
                    <input
                        type="text"
                        name="login_id"
                        id="login_id"
                        placeholder="英数字で5文字以上50文字以内で設定してください"
                        value="<?= htmlspecialchars($_POST['login_id']) ?>">
                    <?php if (isset($error_message['login_id'])) : ?>
                        <div class="error-msg">
                            <?= htmlspecialchars($error_message['login_id']) ?></div>
                    <?php endif ?>
                </div>
                <div>
                    <label>パスワード<span>必須</span></label>
                    <input
                        type="text"
                        name="password"
                        id="password"
                        placeholder="英数字で5文字以上50文字以内で設定してください"
                        value="<?= htmlspecialchars($_POST['password']) ?>">
                    <?php if (isset($error_message['password'])) : ?>
                        <div class="error-msg">
                            <?= htmlspecialchars($error_message['password']) ?></div>
                    <?php endif ?>
                </div>
                <div>
                    <label>確認用パスワード<span>必須</span></label>
                    <input
                        type="text"
                        name="password_confirm"
                        id="password_confirm"
                        placeholder="確認のためもう一度パスワードを入力してください">
                    <?php if (isset($error_message['password_confirm'])) : ?>
                        <div class="error-msg">
                            <?= htmlspecialchars($error_message['password_confirm']) ?></div>
                    <?php endif ?>
                </div>
            </div>
            <button type="submit" id="confirmBtn">登録する</button>
            <button type="button" onclick="location.href='login_dashboard.php'">ユーザー一覧に戻る</button>
        </form>
    </div>
</body>

</html>