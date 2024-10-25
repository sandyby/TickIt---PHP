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

require_once("connection.php");
require_once("formValidation.php");

$error_msg = $_SESSION['to-do-list-add_list_error_msg'] ?? [];
unset($_SESSION['to-do-list-add_list_error_msg']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error_msg = [];
    $title = $_POST['nama_list'];
    $user_id = $_SESSION['to-do-list-user_id'];

    if (!isValid($title)) {
        $error_msg[] = 'Please insert a valid list title!';
    }

    if (!isString($title)) {
        $error_msg[] = 'List title can only use these symbols: .,?!-]+$/';
    }

    if (!empty($error_msg)) {
        $_SESSION['to-do-list-add_list_error_msg'] = $error_msg;
        header("Location: ../include/add_list.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM lists WHERE title = :title AND user_id = :user_id");
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt) {
        $_SESSION['to-do-list-status_add_list'][] = 'Gagal menambahkan list! Silakan coba lagi';
        header("Location: ../include/add_list.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO lists (user_id, title) VALUES (:user_id, :title)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION['to-do-list-status_add_list'][] = 'Successfully added list!';
    header('Location: ../public/dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add List</title>
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

        .add-list-card {
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

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-give, .btn-back {
            background-color: #b3f4c8;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-give:hover, .btn-back:hover {
            background-color: #b3f4c8;
            transform: translateY(-5px);
        }

        .btn-give:active, .btn-back:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .error-msg {
            color: red;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .add-list-card {
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
        <div class="add-list-card">
            <h1>Add List</h1>
            <form method="post" enctype="multipart/form-data">
                <?php if (!empty($error_msg)): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error_msg[0]); ?></div>
                <?php endif; ?>
                <div class="my-2">
                    <label for="nama_list" class="form-label">List Title</label>
                    <input type="text" class="form-control" id="nama_list" name="nama_list" placeholder="Insert list title">
                </div>
                <button type="submit" class="btn btn-give my-2">Add</button>
            </form>
            <a href="../public/dashboard.php" class="btn btn-back">Back to Dashboard</a>
        </div>
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

        <?php if (!empty($_SESSION['to-do-list-status_add_list'])): ?>
            const addListStatuses = <?php echo json_encode($_SESSION['to-do-list-status_add_list']); ?>;
            addListStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Gagal') ? "error" : "success",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>
        <?php unset($_SESSION['to-do-list-status_add_list']); ?>
    </script>
</body>

</html>