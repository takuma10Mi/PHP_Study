<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $favorite = $_POST['favorite'] == 1 ? 0 : 1;

    try {
        $dbh = getDbConnection();

        // お気に入り状態を更新
        $stmt = $dbh->prepare("UPDATE inquiries SET favorite = :favorite WHERE id = :id");
        $stmt->bindParam(':favorite', $favorite, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo 'success';
    } catch (PDOException $e) {
        echo 'error';
    }
}
?>
