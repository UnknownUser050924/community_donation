<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION["name"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            margin-top: 50px;
        }
        .btn {
            display: block;
            width: 200px;
            margin: 10px auto;
            padding: 10px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?> (Admin)</h2>

    <div class="container">
        <a href="admin_accounts.php" class="btn">Manage Accounts</a>
        <a href="admin_items.php" class="btn">Manage Items</a>
        <a href="logout_admin.php" class="btn" style="background-color: red;">Logout</a>
    </div>
</body>
</html>
