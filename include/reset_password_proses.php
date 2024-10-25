<?php
session_start();


require_once("connection.php");
require_once("formValidation.php");
$error_msg = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $reset_password_token = $_POST['reset_password_token'];
    $new_password = $_POST['new-password'];
    $confirm_new_password = $_POST['confirm-new-password'];
    $user_id = $_SESSION['to-do-list-user_id'];

    if (!isValid($new_password) || !isValid($confirm_new_password)) {
        $error_msg[] = 'Please insert valid inputs!';
    }

    if (!isPassword($new_password)) {
        $error_msg[] = 'Please insert a valid password! (min. 8 characters, 1 lowercase, 1 uppercase, 1 number, dan 1 symbol. Symbols allowed: @$!%*?&_)';
    }

    if ($new_password !== $confirm_new_password) {
        $error_msg[] = 'Password not matching! Please try again';
    }

    if (!empty($error_msg)) {
        $_SESSION['to-do-list-reset_password_error_msg'] = $error_msg;
        header("Location: reset_password.php?reset_password_token=" . $reset_password_token);
        exit();
    }

    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

    $sql = "UPDATE users SET password = ?, reset_password_token = NULL, reset_password_token_expiry_date = NULL WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_password_hash, $user_id]);

    if (!$stmt) {
        $_SESSION['to-do-list-status_reset_password'][] = 'Failed to change password! Please try again';
        header("Location: reset_password.php");
        exit();
    }

    $_SESSION['to-do-list-status_reset_password'][] = 'Successfully changed password! Please log in';
    header("Location: ../public/login.php");
    exit();
}
