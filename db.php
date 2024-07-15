<?php
function getDbConnection() {
$db_host = getenv('HOSTNAME');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USERNAME');
$db_pass = getenv('DB_PASSWORD');
    
    try {
        $dbh = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    } catch (PDOException $e) {
        echo 'データベースエラー: ' . $e->getMessage();
        exit();
    }
}
?>
