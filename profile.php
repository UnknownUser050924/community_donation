<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Unauthorized access! Please log in.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"]; // Get the user role

$query = $conn->prepare("SELECT name, email, address, state, phone FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Determine dashboard based on role
$dashboard_page = ($role == "resident") ? "resident_dashboard.php" : "donor_dashboard.php";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $state = $_POST["state"];
    $phone = $_POST["phone"];

    $updateQuery = $conn->prepare("UPDATE users SET name = ?, address = ?, state = ?, phone = ? WHERE id = ?");
    $updateQuery->bind_param("ssssi", $name, $address, $state, $phone, $user_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('✅ Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating profile. Please try again.');</script>";
    }
}

// Handle account deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_account"])) {
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteQuery->bind_param("i", $user_id);

    if ($deleteQuery->execute()) {
        session_destroy();
        echo "<script>alert('⚠️ Your account has been deleted.'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Error deleting account. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
        }
        .profile-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #ff4f70;
            margin-bottom: 15px;
        }
        .input-group {
            margin: 10px 0;
            text-align: left;
        }
        .input-group label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background: #ff4f70;
            color: white;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn:hover {
            background: #e0435f;
        }
        .btn-danger {
            background: red;
        }
        .btn-danger:hover {
            background: darkred;
        }
        .link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            text-decoration: none;
            color: #ff4f70;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>👤 My Profile</h2>

    <form method="POST">
        <div class="input-group">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>

        <div class="input-group">
            <label for="address">Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        </div>

        <div class="input-group">
            <label for="state">State:</label>
            <input type="text" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
        </div>

        <div class="input-group">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>

        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>

    <form method="POST" onsubmit="return confirm('⚠️ Are you sure you want to delete your account? This action cannot be undone.');">
        <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
    </form>

    <a href="<?php echo $dashboard_page; ?>" class="link">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
