<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>お問い合わせフォーム</h1>
        <?php if ($errors): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form action="confirm.php" method="post">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            
            <label for="email">メールアドレス:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            
            <label for="phone">電話番号:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            
            <label for="subject">件名:</label>
            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_SESSION['subject'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            
            <label for="message">お問い合わせ内容:</label>
            <textarea id="message" name="message"><?php echo htmlspecialchars($_SESSION['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            
            <input type="submit" value="確認">
        </form>
    </div>
</body>
</html>
