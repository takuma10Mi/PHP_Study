<?php
session_start();
require 'db.php';

$dbh = getDbConnection();
$stmt = $dbh->prepare("SELECT * FROM inquiries");
$stmt->execute();
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $idsToDelete = $_POST['delete'] ?? [];
    if (!empty($idsToDelete)) {
        $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
        $stmt = $dbh->prepare("DELETE FROM inquiries WHERE id IN ($placeholders)");
        $stmt->execute($idsToDelete);
        header('Location: list.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ一覧</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function confirmDelete() {
            return confirm('本当に削除しますか？');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>お問い合わせ一覧</h1>
        <form action="list.php" method="post" onsubmit="return confirmDelete();">
            <table>
                <thead>
                    <tr>
                        <th>選択</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>電話番号</th>
                        <th>件名</th>
                        <th>編集</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><input type="checkbox" name="delete[]" value="<?php echo htmlspecialchars($inquiry['id'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                            <td><?php echo htmlspecialchars($inquiry['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a href="edit.php?id=<?php echo htmlspecialchars($inquiry['id'], ENT_QUOTES, 'UTF-8'); ?>">編集</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <input type="submit" name="delete_selected" value="選択した項目を削除">
        </form>
    </div>
</body>
</html>
