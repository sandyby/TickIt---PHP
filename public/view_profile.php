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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Profile</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f2f9f5;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; 
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            padding-bottom: 50px;
        }

        .card-body {
            background-color: #def9e8;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin-top: 7em; 
            margin-bottom: 17em;
        }

        h1, h2 {
            margin: 0;
            font-size: 2rem;
        }

        p {
            font-size: 1rem;
            margin-top: 20px;
            color: #333;
        }

        .btn-info {
            margin-top: 40px;
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

        .btn-info {
            background-color: #b3f4c8; 
            border: none; 
            color: black; 
            font-weight: bold; 
            padding: 10px 20px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-info:hover {
            background-color: #b3f4c8; 
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-info:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>

<body>
    <!-- Navbar -->
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
                        <a class="nav-link" href="view_profile.php">View Profile</a>
                    </li>
                    <li class="nav-item">
                        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Card -->
    <div class="profile-container">
        <div class="card-body">
            <h1>Hello!</h1>
            <div class="my-3">
                <h2><?= htmlspecialchars($_SESSION['to-do-list-username']); ?></h2>
                <p><?= htmlspecialchars($_SESSION['to-do-list-email']); ?></p>
            </div>
            <a href="dashboard.php" class="btn btn-info w-100">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>