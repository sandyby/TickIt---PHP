<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['to-do-list-logged-in']) || $_SESSION['to-do-list-logged-in'] !== true) {
    echo "You do not have access to this page!";
    echo '
    <div style="margin: 10px">
    <a href="../public/login.php" style="font-size: 20px">Log In</a>
    </div>
    ';
    exit();
}

require_once("connection.php");
require_once("formValidation.php");
$pdo->exec("SET time_zone = '+07:00';");

$error_msg = $_SESSION['to-do-list-add_task_error_msg'] ?? [];
unset($_SESSION['to-do-list-add_task_error_msg']);

if (!isset($_GET['list_id'])) {
    header("Location: ../public/dashboard.php");
}
$list_id = $_GET['list_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['task'];
    $due_date = ($_POST['due_date'] === "") ? NULL : $_POST['due_date'];
    $user_id = $_SESSION['to-do-list-user_id'];

    if (!isValid($description)) {
        $error_msg[] = 'Please insert a valid task name!';
    }

    if (!isString($description)) {
        $error_msg[] = 'Task name can only include these symbols: .,?!-]+$/';
    }

    if (!empty($error_msg)) {
        $_SESSION['to-do-list-add_task_error_msg'] = $error_msg;
        header("Location: ../include/add_task.php?list_id=" . $list_id);
        exit();
    }

    $stmt = $pdo->prepare("SELECT title, list_id FROM lists WHERE list_id = :list_id AND user_id = :user_id");
    $stmt->bindParam(':list_id', $list_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['to-do-list-status_add_task'][] = 'Failed to add task! Please try again';
        header("Location: ../include/add_task.php?list_id=" . $list_id);
        exit();
    }

    if (is_null($due_date)) {
        $due_date = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt2 = $pdo->prepare("INSERT INTO items(list_id, description, due_date) VALUES (:list_id, :description, :due_date)");
        $stmt2->bindParam(':due_date', $due_date, PDO::PARAM_STR);
    } else {
        $stmt2 = $pdo->prepare("INSERT INTO items(list_id, description, due_date) VALUES (:list_id, :description, :due_date)");
        $stmt2->bindParam(':due_date', $due_date, PDO::PARAM_STR);
    }

    $stmt2->bindParam(':list_id', $list_id, PDO::PARAM_INT);
    $stmt2->bindParam(':description', $description, PDO::PARAM_STR);

    if ($stmt2->execute()) {
        $_SESSION['to-do-list-status_add_task'][] = 'Successfully added task to list ' . htmlspecialchars($row["title"]) . '!';
        header('Location: ../public/dashboard.php');
        exit();
    } else {
        $_SESSION['to-do-list-status_add_task'][] = 'Failed to add task! Please try again';
        header("Location: ../include/add_task.php?list_id=" . $list_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Add Task</title>
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

        .add-task-card {
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

        input[type="text"], input[type="datetime-local"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        .btn-yes, .btn-no {
            background-color: #b3f4c8;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-yes:hover, .btn-no:hover {
            background-color: #b3f4c8;
            transform: translateY(-5px);
        }

        .btn-yes:active, .btn-no:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .error-msg {
            color: red;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .add-task-card {
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
        <div class="add-task-card">
            <h1>Add Task</h1>
            <form method="post" enctype="multipart/form-data">
                <?php if (!empty($error_msg)): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error_msg[0]); ?></div>
                <?php endif; ?>
                <div class="my-2">
                    <label for="task" class="form-label">Task</label>
                    <input type="text" class="form-control" id="task" name="task" placeholder="Insert task name">

                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                </div>
                <button type="submit" class="btn btn-yes my-2">Submit</button>
            </form>
            <a href="../public/dashboard.php" class="btn btn-no">Back to Dashboard</a>
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

        <?php if (!empty($_SESSION['to-do-list-status_add_task'])): ?>
            const addTaskStatuses = <?php echo json_encode($_SESSION['to-do-list-status_add_task']); ?>;
            addTaskStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>
        <?php unset($_SESSION['to-do-list-status_add_task']); ?>
    </script>
</body>

</html>
