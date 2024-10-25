<?php
session_start();

require_once("connection.php");
require_once("formValidation.php");

if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['to-do-list-form_token']) {
    header("Location: ../public/login.php");
    exit();
}

unset($_SESSION['to-do-list-form_token']);
$error_msg = [];
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);

if (!isValid($email) || !isValid($password)) {
    $error_msg[] = 'Please insert valid inputs!';
}
if (!isEmail($email)) {
    $error_msg[] = 'Please insert valid E-mail!';
}

if (!empty($error_msg)) {
    $_SESSION['to-do-list-login_error_msg'] = $error_msg;
    header("Location: ../public/login.php");
    exit();
}

$sql = "SELECT * FROM users
            WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['to-do-list-login_error_msg'][] = 'User Not Found!';
    header("Location: ../public/login.php");
    exit();
} else {
    if (!password_verify($password, $row['password'])) {
        $_SESSION['to-do-list-login_error_msg'][] = 'Wrong Password! Please try again';
        header("Location: ../public/login.php");
        exit();
    } elseif (!$row['isVerified']) {
        $_SESSION['to-do-list-login_error_msg'][] = 'Please check your email and verify your account first.!';
        header("Location: ../public/login.php");
        exit();
    } else {
        $_SESSION['to-do-list-logged-in'] = true;
        $_SESSION['to-do-list-status_login'][] = 'Successfully Logged In!';
        $_SESSION['to-do-list-user_id'] = $row['user_id'];
        $_SESSION['to-do-list-username'] = $row['username'];
        $_SESSION['to-do-list-email'] = $row['email'];
        header('Location: ../public/dashboard.php');
        exit();
    }
}
