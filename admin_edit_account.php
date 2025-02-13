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

// Fetch user details
$query = $conn->prepare("SELECT id, name, email, role, address, state, phone FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='admin_accounts.php';</script>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $role = $_POST["role"];
    $address = $_POST["address"];
    $state = $_POST["state"];
    $phone = $_POST["phone"];

    // Update user in database
    $updateQuery = $conn->prepare("UPDATE users SET name = ?, role = ?, address = ?, state = ?, phone = ? WHERE id = ?");
    $updateQuery->bind_param("sssssi", $name, $role, $address, $state, $phone, $user_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('Account updated successfully!'); window.location.href='admin_accounts.php';</script>";
    } else {
        echo "<script>alert('Error updating account. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        form {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .back {
            background-color: #007bff;
            padding: 8px;
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>

    <h2>Edit Account</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="email">Email (Cannot be changed):</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="resident" <?php if ($user['role'] == 'resident') echo 'selected'; ?>>Resident</option>
            <option value="donor" <?php if ($user['role'] == 'donor') echo 'selected'; ?>>Donor</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

        <label for="state">State:</label>
        <input type="text" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

        <button type="submit" class="btn">Update Account</button>
    </form>

    <a href="admin_accounts.php" class="back">🔙 Back to Accounts</a>

</body>
</html>
