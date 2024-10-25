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
$_SESSION['to-do-list-status_logout'][] = 'Successfully Logged Out!';
unset($_SESSION['to-do-list-logged-in']);
unset($_SESSION['to-do-list-user_id']);
unset($_SESSION['to-do-list-username']);
header('Location: ../public/login.php');
exit();
