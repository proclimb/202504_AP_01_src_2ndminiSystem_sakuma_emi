<?php


//  1.DB接続情報、クラス定義の読み込み
require_once 'Db.php';
require_once 'login_User.php';

session_cache_limiter('none');
session_start();


// 2.変数の初期化
// *$_POSTの値があるときは初期化しない
$error_message = [];
$user = new Login($pdo);

if (empty($_POST)) {
    $id = $_GET['id'];
    $_POST = $user->findById($id);
} else {

    if (empty($_POST['login_id'])) {
        $error_message['login_id'] = 'ログインIDが入力されていません';
    } elseif (mb_strlen($_POST['login_id']) > 50 || mb_strlen($_POST['login_id']) < 5) {
        $error_message['login_id'] = 'ログインIDは5文字以上50文字以内で入力してください';
    } elseif (preg_match('/[^a-zA-Z0-9]/u', $_POST['login_id'])) {
        $error_message['login_id'] = '英数字以外が入力されています';
    } else {
        $same_id = $user->searchId($_POST['login_id']);
        if ($same_id['id'] != $_POST['id']) {
            if ($same_id) {
                $error_message['login_id'] = 'そのログインIDは既に使われています';
            }
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
        if ($user->update($_POST)) {
            //登録成功
            echo "<script> alert('更新完了：TOPに戻ります');
            window.location.href = 'index.php';</script>";
            exit();
        } else {
            //登録失敗
            echo "<script> alert('更新失敗：もう一度やり直してください') </script>";
        }
    }
}



// 4.html の描画
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
        <?php if (isset($_SESSION['login_id'])): ?>
            <h2>ログインユーザー更新・削除画面</h2>
        <?php else : ?>
            <h2>ログインID・パスワードの確認・変更画面</h2>
        <?php endif ?>
    </div>
    <div>
        <form action="login_edit.php" method="post" name="data" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
            <input type="hidden" name="old_login_id" value="<?php echo $_POST['old_login_id'] ?>">
            <?php if (isset($_SESSION['login_id'])): ?>
                <h1 class="contact-title">更新内容入力</h1>
                <p>更新内容をご入力の上、「更新」ボタンをクリックしてください。</p>
                <p>削除する場合は「削除」ボタンをクリックしてください。</p>
            <?php else : ?>
                <h1 class="contact-title">変更内容入力</h1>
                <p>変更するの場合は内容をご入力の上、「更新」ボタンをクリックしてください。</p>
                <p>変更せずログインする場合は、「ログイン画面へ」ボタンをクリックしてください。</p>
            <?php endif ?>
            <div>
                <div>
                    <label>お名前<span>必須</span></label>
                    <input
                        type="text"
                        name="name"
                        value="<?= htmlspecialchars($_POST['name']) ?>"
                        readonly
                        class="readonly-field">
                </div>
                <div>
                    <label>生年月日<span>必須</span></label>
                    <input
                        type="text"
                        name="birth_date"
                        value="<?php echo $_POST['birth_date'] ?>"
                        readonly
                        class="readonly-field">
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
                <?php if ($_SESSION['user_permissions'] == 1): ?>
                    <div>
                        <label>権限<span>必須</span></label>
                        <?php $_POST['user_permissions'] ?? '0'; ?>
                        <label class="gender">
                            <input
                                type="radio"
                                name="user_permissions"
                                value='0'
                                <?= ($_POST['user_permissions']) == '0'
                                    ? 'checked' : '' ?>>一般</label>
                        <label class="gender">
                            <input
                                type="radio"
                                name="user_permissions"
                                value='1'
                                <?= ($_POST['user_permissions']) == '1'
                                    ? 'checked' : '' ?>>管理者</label>
                        <label class="gender">
                            <input
                                type="radio"
                                name="user_permissions"
                                value='2'
                                <?= ($_POST['user_permissions']) == '2'
                                    ? 'checked' : '' ?>>その他</label>
                    </div>
                <?php endif ?>
            </div>
            <button type="submit" id="confirmBtn">更新</button>
            <?php if ($_SESSION['user_permissions'] == 1): ?>
                <button type="button" class="button-back" onclick="location.href='login_dashboard.php'">ユーザー一覧に戻る</button>
            <?php elseif (!isset($_SESSION['login_id'])): ?>
                <button type="button" class="button-back" onclick="location.href='login.php'">ログイン画面に戻る</button>
            <?php else : ?>
                <button type="button" class="button-back" onclick="location.href='user_profile.php'">マイページに戻る</button>
            <?php endif ?>
        </form>
        <?php if (isset($_SESSION['login_id'])): ?>
            <form action="login_delete.php" method="post" name="delete" onsubmit="return deleteConfirm();">
                <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
                <button type="submit">削除</button>
            </form>
        <?php endif ?>
    </div>
</body>

</html>

<script>
    function deleteConfirm() {
        return confirm(`本当に<?php echo $_POST['name'] ?>さんのユーザー登録を削除しますか？`);
    }
</script>