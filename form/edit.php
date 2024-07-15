<?php
session_start();
require 'db.php';

$id = $_GET['id'] ?? null;

// 初期化
$errors = [];
$name = '';
$email = '';
$phone = '';
$subject = '';
$message = '';

// POSTリクエストを処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // 削除処理
        $id = $_POST['id'];
        $dbh = getDbConnection();
        $stmt = $dbh->prepare("DELETE FROM inquiries WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: list.php');
        exit();
    } else {
        // 更新処理
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // バリデーション
        if (empty($name)) {
            $errors[] = '名前を入力してください。';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = '有効なメールアドレスを入力してください。';
        }

        if (empty($phone) || !preg_match('/^\d{10,15}$/', $phone)) {
            $errors[] = '有効な電話番号を入力してください。';
        }

        if (empty($subject)) {
            $errors[] = '件名を入力してください。';
        }

        if (empty($message)) {
            $errors[] = 'お問い合わせ内容を入力してください。';
        }

        if (empty($errors)) {
            $dbh = getDbConnection();
            $stmt = $dbh->prepare("UPDATE inquiries SET name = :name, email = :email, phone = :phone, subject = :subject, message = :message WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            $stmt->execute();
            header('Location: list.php');
            exit();
        } else {
            // エラーメッセージと入力データをセッションに保存してリダイレクト
            $_SESSION['errors'] = $errors;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['phone'] = $phone;
            $_SESSION['subject'] = $subject;
            $_SESSION['message'] = $message;
            header('Location: edit.php?id=' . $id);
            exit();
        }
    }
}

if ($id) {
    $dbh = getDbConnection();
    $stmt = $dbh->prepare("SELECT * FROM inquiries WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $inquiry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inquiry) {
        echo 'データが見つかりません。';
        exit();
    }
} else {
    header('Location: list.php');
    exit();
}

// セッションからエラーメッセージと入力データを取得
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
$name = $_SESSION['name'] ?? $inquiry['name'];
$email = $_SESSION['email'] ?? $inquiry['email'];
$phone = $_SESSION['phone'] ?? $inquiry['phone'];
$subject = $_SESSION['subject'] ?? $inquiry['subject'];
$message = $_SESSION['message'] ?? $inquiry['message'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ編集</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>お問い合わせ編集</h1>
        <?php if ($errors): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form action="edit.php?id=<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="phone">電話番号:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="subject">件名:</label>
            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>">

            <label for="message">お問い合わせ内容:</label>
            <textarea id="message" name="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></textarea>

            <input type="submit" value="更新">
        </form>
        <form action="edit.php" method="post" onsubmit="return confirm('本当に削除しますか？');">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="delete" value="1">
            <input type="submit" value="削除">
        </form>
        <a href="list.php">戻る</a>
    </div>
</body>
</html>
