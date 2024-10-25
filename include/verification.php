<?php
session_start();
require_once 'connection.php';
$verification_code = $_GET['verification_code'];

if (isset($verification_code)) {
    
    $sql = "SELECT * FROM users WHERE verification_code = ?";
    $result = $pdo->prepare($sql);
    $result->execute([$verification_code]);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['to-do-list-status_verifikasi'][] = 'Failed to send verification E-mail! Please try again';
        error_log("Failed trying to verificate account");
        header('Location: ../public/register.php');
        exit();
    }

    $sql = "UPDATE users SET isVerified = 1, verified_at = NOW() WHERE user_id = ?";
    $result = $pdo->prepare($sql);

    if (!$result->execute([$row['user_id']])) {
        $_SESSION['to-do-list-status_verifikasi'][] = 'Failed to send verification E-mail! Please try again';
        error_log("Failed trying to verificate account");
        header('Location: ../public/register.php');
        exit();
    }

    $_SESSION['to-do-list-status_verifikasi_2'][] = 'Successfully verified Email! Please log in';
    header('Location: ../public/login.php');
    exit();
}
