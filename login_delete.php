<?php

//  1.DB接続情報、クラス定義の読み込み
require_once 'Db.php';
require_once 'login_User.php';

// 2.更新・削除画面からの入力値を変数に設定
$id = $_POST["id"];

// 3-1.Userクラスをインスタンス化
$user = new Login($pdo);

// 3-2.Userクラスのdelete()メソッドでデータ削除
$user->delete($id);

// 4.html の描画
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>完了画面</title>
    <link rel="stylesheet" href="style_new.css">
</head>

<body>
    <div>
        <h1>mini System</h1>
    </div>
    <div>
        <h2>ユーザー削除完了画面</h2>
    </div>
    <div>
        <div>
            <h1>削除完了</h1>
            <p>
                削除しました。<br>
            </p>
            <a href="index.php">
                <button type="button">TOPに戻る</button>
            </a>
        </div>
    </div>
</body>

</html>