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
if (!isset($_GET['list_id']) || !isset($_GET['item_id'])) {
    header("Location: ../public/dashboard.php");
}
$user_id = $_SESSION['to-do-list-user_id'];
$list_id = $_GET['list_id'];
$item_id = $_GET['item_id'];

$stmt = $pdo->prepare("SELECT list_id FROM lists WHERE list_id = :list_id AND user_id = :user_id");
$stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo "Unable to change task status!";
    exit();
}

$stmt2 = $pdo->prepare("DELETE FROM items WHERE item_id = :item_id");
$stmt2->bindParam(':item_id', $item_id, PDO::PARAM_INT);
$stmt2->execute();

$_SESSION['to-do-list-status_delete_task'][] = 'Successfully Deleted Task!';
header('Location: ../public/dashboard.php');
exit();
?>