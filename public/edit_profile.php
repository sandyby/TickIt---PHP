<?php
session_start();

if (!isset($_SESSION['to-do-list-logged-in']) || $_SESSION['to-do-list-logged-in'] !== true) {
    echo "Anda tidak memiliki akses ke laman ini!";
    echo '
    <div style="margin: 10px">
        <a href="../public/login.php" style="font-size: 20px">Log In</a>
    </div>
    ';
    exit();
}

require_once("../include/connection.php");
require_once("../include/formValidation.php");

$error_msg = $_SESSION['to-do-list-edit_profile_error_msg'] ?? [];
unset($_SESSION['to-do-list-edit_profile_error_msg']);

$user_id = htmlspecialchars($_SESSION['to-do-list-user_id']);
$username = htmlspecialchars($_SESSION['to-do-list-username']);
$email = htmlspecialchars($_SESSION['to-do-list-email']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = htmlspecialchars($_POST['username']);
    $new_email = htmlspecialchars($_POST['email']);
    $old_password_input = htmlspecialchars($_POST['old_password']);
    $new_password = htmlspecialchars($_POST['new_password']);

    if (!isValid($new_username) || !isValid($new_email) || !isValid($old_password_input)) {
        $error_msg[] = 'Please insert valid inputs!';
    }
    if (!isUsername($new_username)) {
        $error_msg[] = 'Please insert a valid username! (3-16 characters, only symbols allowed are . and _)';
    }
    if (!isEmail($new_email)) {
        $error_msg[] = 'Please insert valid E-mail!';
    }

    if (!empty($error_msg)) {
        $_SESSION['to-do-list-edit_profile_error_msg'] = $error_msg;
        header("Location: ../public/edit_profile.php");
        exit();
    }

    if ($username !== $new_username) {
        $sql1 = "SELECT * FROM users
        WHERE username = ?";
        $stmt = $pdo->prepare($sql1);
        $stmt->execute([$new_username]);
        $row1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row1) {
            $_SESSION['to-do-list-edit_profile_error_msg'][] = 'Username taken!';
            header("Location: ../public/edit_profile.php");
            exit();
        }
    }

    if ($email !== $new_email) {
        $sql2 = "SELECT * FROM users
        WHERE email = ?";
        $stmt = $pdo->prepare($sql2);
        $stmt->execute([$new_email]);
        $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row2) {
            $_SESSION['to-do-list-edit_profile_error_msg'][] = 'E-mail already registered!';
            header("Location: ../public/edit_profile.php");
            exit();
        }
    }

    $en_pass_old = password_hash($old_password_input, PASSWORD_BCRYPT);

    $stmt1 = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
    $stmt1->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt1->execute();

    $old_password_in_db = $stmt1->fetch(PDO::FETCH_ASSOC)['password'];

    if (!password_verify($old_password_input, $old_password_in_db)) {
        $_SESSION['to-do-list-edit_profile_error_msg'][] = 'Wrong old password! Please try again';
        header("Location: ../public/edit_profile.php");
        exit();
    } else {


        if (empty($new_password)) {
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE user_id = :user_id");
        } else {
            if (!isPassword($new_password)) {
                $_SESSION['to-do-list-edit_profile_error_msg'][] = 'Please insert valid password! (min. 8 characters, 1 lowercase, 1 uppercase, 1 number, dan 1 symbol. Allowed symbols: @$!%*?&_)';
                header("Location: ../public/edit_profile.php");
                exit();
            }
            $en_pass_new = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, password = :password WHERE user_id = :user_id");
            $stmt->bindParam(':password', $en_pass_new, PDO::PARAM_STR);
        }
        $stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['to-do-list-username'] = $new_username;
        $_SESSION['to-do-list-email'] = $new_email;
        $_SESSION['to-do-list-status_edit_profile'][] = 'Successfully updated profile!';
        header('Location: dashboard.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Profile</title>
    <style>
        body {
            background-color: #f2f9f5;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #def9e8;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .error-msg {
            color: red;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        .btn {
            width: 100%;
            margin-top: 10px;
        }

        .navbar {
            width: 100%;
            padding: 10px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            margin: auto;
        }

        .navbar-nav {
            margin-left: auto;
        }

        .nav-item {
            margin-left: 20px;
        }

        .logout-btn {
            margin-left: auto;
            padding: 5px 15px;
            background-color: #ff4d4d;
            border: none;
            color: white;
            border-radius: 5px;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
                padding: 15px;
            }

            .navbar-brand {
                font-size: 20px;
            }
        }

        .btn-primary {
            background-color: #cbeedb; 
            color: #000; 
            border: none;
        }

        .btn-primary:hover {
            background-color: #b0e3cc;
            color: #000; 
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1); 
            transition: background-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-3px);
            transition: transform 0.2s ease-in-out;
        }
        
        .btn-secondary {
            width: 150px; 
            margin: 10px auto 0 auto;
            display: block;
        }

        .btn:hover {
            transform: translateY(-3px); 
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1); 
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; 
        }

        
        .btn {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }



    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand mx-auto" href="#">TickIt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">Edit Profile</a>
                    </li>
                    <li class="nav-item">
                        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Edit Profile</h1>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_msg[0]); ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Username"
                    value="<?= $username; ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Email"
                    value="<?= $email; ?>">
            </div>

            <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" name="old_password" class="form-control" id="old_password"
                    placeholder="Old Password">
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password (Optional)</label>
                <input type="password" name="new_password" class="form-control" id="new_password"
                    placeholder="New Password">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>

        <a href="dashboard.php" class="btn btn-secondary mt-2">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            heightAuto: false,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        <?php if (!empty($_SESSION['to-do-list-status_edit_profile'])): ?>
            const editProfileStatuses = <?php echo json_encode($_SESSION['to-do-list-status_edit_profile']); ?>;
            editProfileStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>
        <?php unset($_SESSION['to-do-list-status_edit_profile']); ?>
    </script>
</body>

</html>