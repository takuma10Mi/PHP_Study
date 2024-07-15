<?php
session_start();

$errors = [];

// エスケープ関数
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力データのバリデーション
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

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

    // エラーがある場合は入力画面に戻る
    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        $_SESSION['subject'] = $subject;
        $_SESSION['message'] = $message;
        header('Location: input.php');
        exit();
    }

    // エスケープしたデータをセッションに保存
    $_SESSION['name'] = h($name);
    $_SESSION['email'] = h($email);
    $_SESSION['phone'] = h($phone);
    $_SESSION['subject'] = h($subject);
    $_SESSION['message'] = h($message);

    // もしセッションにデータがなければ、input.phpにリダイレクトする
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header('Location: input.php');
    exit();
}
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>確認画面</title>
    <link rel="stylesheet" href="../css/reset.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>確認画面</h1>
        <form action="complete.php" method="post">
            <p>名前: <?php echo h($name); ?></p>
            <p>メールアドレス: <?php echo h($email); ?></p>
            <p>電話番号: <?php echo h($phone); ?></p>
            <p>件名: <?php echo h($subject); ?></p>
            <p>お問い合わせ内容: <?php echo nl2br(h($message)); ?></p>

            <!-- 隠しフィールドにエスケープしたデータを設定 -->
            <input type="hidden" name="name" value="<?php echo h($name); ?>">
            <input type="hidden" name="email" value="<?php echo h($email); ?>">
            <input type="hidden" name="phone" value="<?php echo h($phone); ?>">
            <input type="hidden" name="subject" value="<?php echo h($subject); ?>">
            <input type="hidden" name="message" value="<?php echo h($message); ?>">
            
            <input type="submit" value="送信">
        </form>
        <form action="input.php" method="post">
            <input type="submit" value="戻る">
        </form>
    </div>
</body>
</html>
