<?php
session_start();


require_once 'connection.php';
$error_msg = $_SESSION['to-do-list-reset_password_error_msg'] ?? "";
unset($_SESSION['to-do-list-reset_password_error_msg']);

$reset_password_token = $_GET['reset_password_token'];
$reset_password_token_hash = hash("sha256", $reset_password_token);

$sql = "SELECT * FROM users
        WHERE reset_password_token = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$reset_password_token_hash]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['to-do-list-status_reset_password'][] = 'Invalid token! Please try again';
    header("Location: ../public/forgot_password.php");
    exit();
}

if (strtotime($row['reset_password_token_expiry_date']) <= time()) {
    $_SESSION['to-do-list-status_reset_password'][] = 'Expired token! Please create a new one';
    header("Location: ../public/forgot_password.php");
    exit();
}

$_SESSION['to-do-list-user_id'] = $row['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f2f9f5;
        }

        .container {
            max-width: 500px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
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

        .reset-password-card {
            background-color: #def9e8;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .custom-btn {
            background-color: #ff5c5c;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .custom-btn:hover {
            background-color: #ff4242;
            transform: translateY(-5px);
        }

        .custom-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .error-msg {
            color: red;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .reset-password-card {
                padding: 20px;
                width: 90%;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a class="navbar-brand" href="#">TickIt</a>
    </nav>

    <div class="container">
        <div class="reset-password-card">
            <h1>Reset Password</h1>
            <form action="reset_password_proses.php" method="post">
                <input type="hidden" name="form_token" value="<?= $_SESSION['to-do-list-form_token']; ?>">
                <input type="hidden" name="reset_password_token" value="<?= htmlspecialchars($reset_password_token); ?>">
                <?php if (!empty($error_msg)): ?>
                    <div class="error-msg"><?= htmlspecialchars($error_msg[0]); ?></div>
                <?php endif; ?>
                <input type="password" name="new-password" placeholder="New Password">
                <input type="password" name="confirm-new-password" placeholder="Confirm New Password">
                <button type="submit" class="custom-btn">Change</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
    </script>
</body>

</html> 