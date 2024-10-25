<?php
session_start();

if (!isset($_SESSION['to-do-list-logged-in']) || $_SESSION['to-do-list-logged-in'] !== true) {
    echo "You do not have access to this page!";
    echo '
    <div style="margin: 10px">
        <a href="../public/login.php" style="font-size: 20px">Log In</a>
    </div>
    ';
    exit();
}
require_once("connection.php");

if (!isset($_GET['list_id'])) {
    header("Location: ../public/dashboard.php");
}
$user_id = $_SESSION['to-do-list-user_id'];
$list_id = $_GET['list_id'];

$stmt = $pdo->prepare("DELETE FROM items WHERE list_id = :list_id");
$stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);
$stmt->execute();

$stmt2 = $pdo->prepare("DELETE FROM lists WHERE list_id = :list_id");
$stmt2->bindParam(':list_id', $list_id, PDO::PARAM_INT);
$stmt2->execute();

$_SESSION['to-do-list-status_delete_list'][] = 'Successfully deleted list!';
header('Location: ../public/dashboard.php');
exit();
?>