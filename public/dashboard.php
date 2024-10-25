<?php
date_default_timezone_set('Asia/Jakarta');

require_once("../include/initDB_T.php");
require_once("../include/connection.php");

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

$user_id = $_SESSION['to-do-list-user_id'];

$pdo->exec("SET time_zone = '+07:00';");

if (!isset($_GET['filter']) && (!isset($_GET['search']))) {
    $stmt = $pdo->prepare("
            SELECT lists.list_id AS list_id, lists.title as list_title, items.item_id AS item_id, items.description, items.due_date, items.status 
            FROM lists
            LEFT JOIN items ON lists.list_id = items.list_id
            WHERE lists.user_id = :user_id
            ORDER BY lists.list_id, items.due_date;
            ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

if (isset($_GET['filter'])) {
    if ($_GET['filter'] == 'completed') {
        $filter = 1;
    } else if ($_GET['filter'] == 'uncompleted') {
        $filter = 0;
    }
    $stmt = $pdo->prepare("
                SELECT lists.list_id AS list_id, lists.title as list_title, items.item_id AS item_id, items.description, items.due_date, items.status 
                FROM lists
                LEFT JOIN items ON lists.list_id = items.list_id
                WHERE lists.user_id = :user_id
                AND items.status = :status
                ORDER BY lists.list_id, items.due_date;
                ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':status', $filter, PDO::PARAM_INT);
    $stmt->execute();
}

if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $stmt = $pdo->prepare("
                SELECT lists.list_id AS list_id, lists.title as list_title, items.item_id AS item_id, items.description, items.due_date, items.status 
                FROM lists
                LEFT JOIN items ON lists.list_id = items.list_id
                WHERE lists.user_id = :user_id
                AND items.description LIKE :search
                ORDER BY lists.list_id, items.due_date;
                ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->execute();
}

$lists = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $list_id = $row['list_id'];
    if (!isset($lists[$list_id])) {
        $lists[$list_id] = [
            'title' => $row['list_title'],
            'items' => []
        ];
    }

    if ($row['item_id']) {
        $lists[$list_id]['items'][] = [
            'item_id' => $row['item_id'],
            'description' => $row['description'],
            'due_date' => $row['due_date'],
            'status' => $row['status']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Dashboard</title>
    <style>
        body {
            background-color: #f2f9f5;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3 {
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .navbar {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar-text {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            padding: 0 10px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .navbar-collapse {
                text-align: center;
            }
        }

        .form-inline {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .search-bar {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-bar input {
            width: 100%;
            padding-right: 40px;
            height: 38px;
            box-sizing: border-box;
        }

        .search-bar .btn-search {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            height: 38px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .search-bar .btn-search {
                right: 5px;
            }
        }

        .btn-cool{
            background-color: #b3f4c8;
            color: black;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-size: 14px;
            padding: 10px 15px;
            outline: none;
        }

        .btn-cool:hover{
            background-color: #b3f9c8;
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            color: white;
        }


        .btn-success{
            color: black;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-size: 14px;
            padding: 10px 15px;
        }

        .btn-danger{
            color: black;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-size: 14px;
            padding: 10px 15px;
        }

        .btn-secondary {
            color: black;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            font-size: 14px;
            padding: 10px 15px;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        .display-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .table-wrapper {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            width: calc(50% - 10px); 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .table-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        
        .table {
            margin-top: 10px;
        }

        
        @media (max-width: 768px) {
            .display-wrapper {
                flex-direction: column;
                gap: 10px;
            }

            .table-wrapper {
                width: 100%;
            }

            .btn {
                font-size: 12px;
                padding: 6px 10px;
            }
        }

        .container{
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TickIt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <form method="GET" class="form-inline mx-auto">
                    <div class="search-bar">
                        <input type="text" id="search" name="search" class="form-control" placeholder="Search task">
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text" id="profileButton">
                            Hello, <?= htmlspecialchars($_SESSION['to-do-list-username']); ?>!
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between flex-wrap">
            <a href="../include/add_list.php" class="btn btn-secondary mb-3">Add List</a>
            <a href="dashboard.php" class="btn btn-cool mb-3">Show Default</a>
            <a href="dashboard.php?filter=completed" class="btn btn-success mb-3">Show Completed Tasks</a>
            <a href="dashboard.php?filter=uncompleted" class="btn btn-danger mb-3">Show Uncompleted Tasks</a>
        </div>

        <div class="display-wrapper table-responsive">
            <?php if (empty($lists)): ?>
                <p class="text-center">No tasks yet.</p>
            <?php else: ?>
                <?php foreach ($lists as $list_id => $list): ?>
                    <div class="table-wrapper">
                    <h2 data-title="<?= htmlspecialchars($list['title']); ?>" data-list-id="<?= htmlspecialchars($list_id); ?>">
                            <?= htmlspecialchars($list['title']); ?>
                        </h2>
                        <a href="../include/add_task.php?list_id=<?= $list_id ?>" class="btn btn-primary mb-2">Add Task</a>
                        <a href="#" class="btn btn-danger mb-2 delete-list">Delete List</a>

                        <table class="table table-striped">
                            <?php if (empty($list['items'])): ?>
                                <p>No tasks in this list.</p>
                            <?php else: ?>
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Due Date</th>
                                        <th>Done?</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($list['items'] as $item): ?>
                                        <tr class="<?php
                                            $current_time = new DateTime();
                                            $due_date = new DateTime($item['due_date']);
                                            if ($item['status'] == 0 && ($current_time > $due_date)) {
                                                echo 'table-danger';
                                            } else if ($item['status'] == 1) {
                                                echo 'table-success';
                                            }
                                        ?>"data-list-id="<?= htmlspecialchars($list_id); ?>"
                                            data-item-id="<?= htmlspecialchars($item['item_id']); ?>"
                                            data-item-description="<?= htmlspecialchars($item['description']); ?>"
                                            data-due-date="<?= $due_date->format('Y-m-d H:i:s') ?>"
                                            data-status="<?= $item['status'] ?>">
                                            
                                            <td><?= htmlspecialchars($item['description']); ?></td>
                                            <td><?= $due_date->format('H:i, d M Y'); ?></td>
                                            <td><?= $item['status'] == 0 ? 'Not yet' : 'Done!' ?></td>
                                            <td>
                                                <?php if ($item['status'] == 0): ?>
                                                    <a href="../include/done_task.php?item_id=<?= htmlspecialchars($item['item_id']) ?>&list_id=<?= htmlspecialchars($list_id) ?>" class="btn btn-success">Done?</a>
                                                <?php else: ?>
                                                    <a href="../include/undone_task.php?item_id=<?= htmlspecialchars($item['item_id']) ?>&list_id=<?= htmlspecialchars($list_id) ?>" class="btn btn-danger">Belum?</a>
                                                <?php endif; ?>
                                                <a href="#" class="btn btn-danger delete-task">Delete Task</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        document.getElementById('profileButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Profile Options',
                html: `
                    <a href="view_profile.php" class="btn btn-info w-100 my-2">View Profile</a>
                    <a href="edit_profile.php" class="btn btn-warning w-100 my-2">Edit Profile</a>
                    <a href="../include/logout.php" class="btn btn-danger w-100 my-2">Logout</a>
                `,
                showConfirmButton: false,
                heightAuto: false
            });
        });
    </script>
    <script>
        function confirmLogOut() {
            Swal.fire({
                title: "Log Out?",
                text: "You will return to the login page!",
                icon: "warning",
                iconColor: "#a8211e",
                heightAuto: false,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Log Out",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../include/logout.php';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-task').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const itemDesc = row.getAttribute('data-item-description');
                    const itemId = row.getAttribute('data-item-id');
                    const listId = row.getAttribute('data-list-id');
                    Swal.fire({
                        title: `Delete Task ${itemDesc}?`,
                        text: "This will permanently delete the task!",
                        icon: "warning",
                        iconColor: "#a8211e",
                        heightAuto: false,
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `../include/delete_task.php?item_id=${itemId}&list_id=${listId}`;
                        }
                    });
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-list').forEach(function(button) {
                button.addEventListener('click', function() {
                    const listTitle = this.parentElement.querySelector('h2').getAttribute('data-title');
                    const listId = this.parentElement.querySelector('h2').getAttribute('data-list-id');

                    Swal.fire({
                        title: `Delete List ${listTitle}?`,
                        text: "The tasks inside the list will be gone too!",
                        icon: "warning",
                        iconColor: "#a8211e",
                        heightAuto: false,
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `../include/delete_list.php?list_id=${listId}`;
                        }
                    });
                });
            });
        });

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

        document.addEventListener('DOMContentLoaded', function() {
            const currentTime = new Date();

            document.querySelectorAll('tr[data-due-date]').forEach(function(row) {
                const dueDate = new Date(row.getAttribute('data-due-date'));
                const status = row.getAttribute('data-status');

                if (status == 0 && currentTime < dueDate) {
                    const timeUntilDue = dueDate - currentTime;

                    setTimeout(function() {
                        location.reload();
                    }, timeUntilDue);
                }
            });
        });

        <?php if (!empty($_SESSION['to-do-list-status_login'])): ?>
            const loginStatuses = <?php echo json_encode($_SESSION['to-do-list-status_login']); ?>;
            loginStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>

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

        <?php if (!empty($_SESSION['to-do-list-status_add_list'])): ?>
            const addListStatuses = <?php echo json_encode($_SESSION['to-do-list-status_add_list']); ?>;
            addListStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>

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

        <?php if (!empty($_SESSION['to-do-list-status_delete_list'])): ?>
            const deleteListStatuses = <?php echo json_encode($_SESSION['to-do-list-status_delete_list']); ?>;
            deleteListStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>

        <?php if (!empty($_SESSION['to-do-list-status_delete_task'])): ?>
            const deleteTaskStatuses = <?php echo json_encode($_SESSION['to-do-list-status_delete_task']); ?>;
            deleteTaskStatuses.forEach((status, index) => {
                setTimeout(() => {
                    Toast.fire({
                        icon: status.includes('Successfully') ? "success" : "error",
                        title: status
                    });
                }, index * 3500);
            });
        <?php endif; ?>
        <?php unset($_SESSION['to-do-list-status_login']); ?>
        <?php unset($_SESSION['to-do-list-status_add_task']); ?>
        <?php unset($_SESSION['to-do-list-status_add_list']); ?>
        <?php unset($_SESSION['to-do-list-status_edit_profile']); ?>
        <?php unset($_SESSION['to-do-list-status_delete_list']); ?>
        <?php unset($_SESSION['to-do-list-status_delete_task']); ?>
    </script>
</body>

</html>