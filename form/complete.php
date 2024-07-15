<?php
session_start();
require 'db.php';

// もしセッションにデータがなければ、input.phpにリダイレクトする
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header('Location: input.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // データベースに接続
        $dbh = getDbConnection();

        // データを挿入
        $stmt = $dbh->prepare("INSERT INTO inquiries (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)");
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':subject', $_POST['subject']);
        $stmt->bindParam(':message', $_POST['message']);
        $stmt->execute();

        // メール送信
        $to_admin = 'admin@example.com';
        $subject_admin = '新しいお問い合わせ';
        $message_admin = "名前: " . $_POST['name'] . "\nメールアドレス: " . $_POST['email'] . "\n電話番号: " . $_POST['phone'] . "\n件名: " . $_POST['subject'] . "\nお問い合わせ内容:\n" . $_POST['message'];
        $headers_admin = 'From: ' . $_POST['email'];

        $to_user = $_POST['email'];
        $subject_user = 'お問い合わせありがとうございます';
        $message_user = $_POST['name'] . " 様\nお問い合わせありがとうございます。以下の内容で承りました。\n\n" . $_POST['message'];
        $headers_user = 'From: admin@example.com';

        mail($to_admin, $subject_admin, $message_admin, $headers_admin);
        mail($to_user, $subject_user, $message_user, $headers_user);

        // セッションをクリア
        session_unset();
        session_destroy();

    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>完了画面</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>完了画面</h1>
        <p>お問い合わせありがとうございました。</p>
        <a href="input.php">戻る</a>
    </div>
</body>
</html>
