<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET["id"])) {
    echo "<script>alert('Invalid request.'); window.location.href='admin_accounts.php';</script>";
    exit();
}

$user_id = $_GET["id"];

// Prevent admin from deleting themselves
if ($user_id == $_SESSION["user_id"]) {
    echo "<script>alert('You cannot delete your own account!'); window.location.href='admin_accounts.php';</script>";
    exit();
}

// Fetch user details
$query = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='admin_accounts.php';</script>";
    exit();
}

// Handle account deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirm_delete"])) {
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteQuery->bind_param("i", $user_id);

    if ($deleteQuery->execute()) {
        echo "<script>alert('Account deleted successfully.'); window.location.href='admin_accounts.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting account. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8d7da;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin: 10px;
        }
        .btn-danger {
            background-color: red;
            color: white;
        }
        .btn-cancel {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>⚠️ Confirm Account Deletion</h2>
        <p>Are you sure you want to delete the following account?</p>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

        <form method="POST">
            <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
            <a href="admin_accounts.php" class="btn-cancel">Cancel</a>
        </form>
    </div>

</body>
</html>
