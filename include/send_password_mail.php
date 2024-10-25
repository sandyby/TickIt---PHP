<?php
session_start();

require_once 'connection.php';
require_once("formValidation.php");
$error_msg = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = $_POST['email'];

    if (!isValid($email)) {
        $error_msg[] = 'Please insert valid inputs!';
    }

    if (!empty($error_msg)) {
        $_SESSION['to-do-list-forgot_password_error_msg'] = $error_msg;
        header("Location: ../public/forgot_password.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['to-do-list-forgot_password_error_msg'][] = 'E-mail not yet registered! Please register a new account!';
        header("Location: ../public/forgot_password.php");
        exit();
    }

    $reset_password_token = bin2hex(random_bytes(16));
    $reset_password_token_hash = hash("sha256", $reset_password_token);
    $expiry_date = date("Y-m-d H:i:s", time() + 60 * 10);

    $sql = "UPDATE users SET reset_password_token = ?, reset_password_token_expiry_date = ? WHERE email = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$reset_password_token_hash, $expiry_date, $email]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['to-do-list-forgot_password_error_msg'][] = 'Something wrong happened! Please try again';
        header("Location: ../public/forgot_password.php");
        exit();
    }

    $mail = require_once 'reset_password_mail.php';

    $mail->setFrom('noreply@tick-it.com', 'Tick It');
    $mail->addAddress($recipient_email);

    $mail->Subject = 'Password Reset Request';
    $mail->Body = '
    <html>
    
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #FAF7F0;
                margin: 0;
                padding: 0;
            }
    
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
    
            .header {
                padding: 10px;
                text-align: center;
                border-radius: 5px 5px 0 0;
                display: flex;
                flex-direction: row;
            }
    
            .header img {
                max-width: 100%;
                height: 50px;
            }
    
            .content {
                margin-top: 20px;
            }
    
            .content p {
                line-height: 1.6;
            }
    
            .button {
                display: inline-block;
                padding: 10px 20px;
                background: #72BF78;
                color: white !important;
                text-decoration: none;
                border-radius: 5px;
                margin-top: 20px;
            }
    
            .button:hover {
                background: #A0D683;
            }
        </style>
    </head>
    
    <body>
        <div class="container">
            <div class="header">
                <img class="tick-it-logo" src="https://i.ibb.co.com/XtmgzvG/tick-it-full.png" alt="tick-it-full" border="0"></a>
            </div>
            <div class="content">
                <p>Please click the link below to reset your Tick It password.!</p>
                <a href="http://tickit.my.id/include/reset_password.php?reset_password_token=' . $reset_password_token . '" class="button">Reset Password</a>
    
                <br /><br />
                <b>If you do not feel that you asked to reset your Tick It account password with this email, please ignore this email.</b>
            </div>
        </div>
    </body>
    
    </html>';


    try {
        if (!$mail->send()) {
            $_SESSION['to-do-list-status_forgot_password'][] = 'Failed to process request! Please try again later';
            header("Location: ../public/forgot_password.php");
            throw new Exception("Error sending reset password e-mail");
            exit();
        }
        $_SESSION['to-do-list-status_forgot_password'][] = 'Successfully sent E-mail! Please check your E-mail';
        header("Location: ../public/login.php");
        exit();
    } catch (Exception $e) {
        error_log("Reset password e-mail couldn't be sent: " . $mail->ErrorInfo);
    }
}
