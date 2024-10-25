<?php
session_start();
require_once("../include/initDB_T.php");
require_once("../include/connection.php");
$_SESSION['to-do-list-form_token'] = hash("sha256", bin2hex(random_bytes(16)));

if (isset($_SESSION['to-do-list-logged-in'])) {
    header('Location: dashboard.php');
    exit();
}

$error_msg = $_SESSION['to-do-list-forgot_password_error_msg'] ?? "";
unset($_SESSION['to-do-list-forgot_password_error_msg']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Forgot Password</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f2f9f5;
        }

        .container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .navbar {
            width: 100%;
            padding: 10px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }

        .forgot-password-card {
            background-color: #def9e8;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            width: 100%;
            max-width: 400px;
            margin-bottom: 13em;
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #b3f4c8;
            color: black;
            font-weight: bold;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #b3f4c8;
            transform: translateY(-5px);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .error-msg {
            color: red;
            margin-top: 10px;
            text-align: center;
            font-size: 0.9rem;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #333;
        }

        @media (max-width: 768px) {
            .forgot-password-card {
                padding: 20px;
                margin: 10px;
                margin-bottom: 13em;
            }

            h1 {
                font-size: 1.5rem;
            }

            .btn-primary {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar">
        <a class="navbar-brand" href="#">TickIt</a>
    </nav>

    <div class="container">
        <div class="forgot-password-card">
            <h1>Forgot Password</h1>
            <form id="form-forgot-password" action="../include/send_password_mail.php" method="post">
                <?php if (!empty($error_msg)): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error_msg[0]); ?></div>
                <?php endif; ?>
                <input type="text" name="email" placeholder="E-mail">
                <button type="submit" class="btn btn-primary">Send E-mail</button>
            </form>
            <a href="register.php">Don't have an account? Register</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('form-forgot-password').addEventListener('submit', function(event) {
            Swal.fire({
                title: 'Processing...',
                html: 'Please Wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 5000,
            heightAuto: false,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        <?php if (!empty($_SESSION['to-do-list-status_forgot_password'])): ?>
            const forgotPasswordStatuses = <?php echo json_encode($_SESSION['to-do-list-status_forgot_password']); ?>;
            forgotPasswordStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 5500);
            });
        <?php endif; ?>

        <?php if (!empty($_SESSION['to-do-list-status_reset_password'])): ?>
            const resetPasswordStatuses = <?php echo json_encode($_SESSION['to-do-list-status_reset_password']); ?>;
            resetPasswordStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 5500);
            });
        <?php endif; ?>
        <?php unset($_SESSION['to-do-list-status_reset_password']); ?>
        <?php unset($_SESSION['to-do-list-status_forgot_password']); ?>
    </script>
</body>

</html>