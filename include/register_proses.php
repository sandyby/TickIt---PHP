<?php
session_start();
require_once("connection.php");
require_once("formValidation.php");

if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['to-do-list-form_token']) {
    header("Location: ../public/register.php");
    exit();
}

unset($_SESSION['to-do-list-form_token']);

$_SESSION['to-do-list-verification_mail_sent'] = false;

$error_msg = [];
$username = htmlspecialchars($_POST['username']);
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);

if (!isValid($username) || !isValid($email) || !isValid($password)) {
    $error_msg[] = 'Please insert valid inputs!';
}
if (!isUsername($username)) {
    $error_msg[] = 'Please enter a valid username! (3-16 characters, symbols allowed: . and _ )';
}
if (!isEmail($email)) {
    $error_msg[] = 'Please enter a valid email!';
}
if (!isPassword($password)) {
    $error_msg[] = 'Please enter a valid password! (min. 8 characters, 1 lowercase, 1 uppercase, 1 number, and 1 symbol. Only the following symbols are allowed: @$!%*?&_)';
}

if (!empty($error_msg)) {
    $_SESSION['to-do-list-register_error_msg'] = $error_msg;
    header("Location: ../public/register.php");
    exit();
}


$sql = "SELECT * FROM users
    WHERE email = ? OR username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $username]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $_SESSION['to-do-list-register_error_msg'][] = 'Username/E-mail already registered!';
    header("Location: ../public/register.php");
    exit();
} else {
    require_once 'verification_mail.php';

    if (!isset($_SESSION['to-do-list-verification_mail_sent']) || $_SESSION['to-do-list-verification_mail_sent'] === false) {
        $_SESSION['to-do-list-status_register'][] = 'Verification email failed to send! Please try again later.';
        header('Location: ../public/register.php');
        exit();
    }
    
    $en_pass = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password, verification_code)
            VALUES(?, ?, ?, ?)";

    $result = $pdo->prepare($sql);
    $result->execute([$username, $email, $en_pass, $verification_code]);
    $_SESSION['to-do-list-status_verifikasi'][] = 'Successfully sent! Please check your email!';
    unset($_SESSION['to-do-list-verification_mail_sent']);
    header('Location: ../public/register.php');
    exit();
}
