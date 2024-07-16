<?php
session_start();
require 'db.php';

$search = $_GET['search'] ?? '';

$dbh = getDbConnection();
if (!empty($search)) {
    $stmt = $dbh->prepare("SELECT * FROM inquiries WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search OR subject LIKE :search OR message LIKE :search");
    $searchTerm = '%' . $search . '%';
    $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
} else {
    $stmt = $dbh->prepare("SELECT * FROM inquiries");
}
$stmt->execute();
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {
    $idsToDelete = $_POST['delete'] ?? [];
    if (!empty($idsToDelete)) {
        $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
        $stmt = $dbh->prepare("DELETE FROM inquiries WHERE id IN ($placeholders)");
        foreach ($idsToDelete as $index => $id) {
            $stmt->bindValue(($index + 1), $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        header('Location: list.php?search=' . urlencode($search));
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/favorite.js"></script>
    <script>
        function confirmDelete() {
            return confirm('本当に削除しますか？');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>お問い合わせ一覧</h1>
        <form action="list.php" method="get">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="検索...">
            <input type="submit" value="検索">
        </form>
        <form action="list.php?search=<?php echo urlencode($search); ?>" method="post" onsubmit="return confirmDelete();">
            <table>
                <thead>
                    <tr>
                        <th>選択</th>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>電話番号</th>
                        <th>件名</th>
                        <th>編集</th>
                        <th>お気に入り</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inquiries)): ?>
                        <?php foreach ($inquiries as $inquiry): ?>
                            <tr>
                                <td><input type="checkbox" name="delete[]" value="<?php echo htmlspecialchars($inquiry['id'], ENT_QUOTES, 'UTF-8'); ?>"></td>
                                <td><?php echo htmlspecialchars($inquiry['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><a href="edit.php?id=<?php echo htmlspecialchars($inquiry['id'], ENT_QUOTES, 'UTF-8'); ?>">編集</a></td>
                                <td>
                                    <button class="favorite-btn" data-id="<?php echo htmlspecialchars($inquiry['id'], ENT_QUOTES, 'UTF-8'); ?>" data-favorite="<?php echo htmlspecialchars($inquiry['favorite'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo $inquiry['favorite'] ? '★' : '☆'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">結果が見つかりませんでした。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <input type="submit" name="delete_selected" value="選択した項目を削除">
        </form>
    </div>
</body>
</html>
