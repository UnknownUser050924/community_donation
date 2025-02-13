<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$query = $conn->prepare("SELECT name, email, address, state, phone FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $state = $_POST["state"];
    $phone = $_POST["phone"];

    $updateQuery = $conn->prepare("UPDATE users SET name = ?, address = ?, state = ?, phone = ? WHERE id = ?");
    $updateQuery->bind_param("ssssi", $name, $address, $state, $phone, $user_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
}

// Handle account deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_account"])) {
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteQuery->bind_param("i", $user_id);

    if ($deleteQuery->execute()) {
        session_destroy(); // Destroy session after deletion
        echo "<script>alert('Your account has been deleted.'); window.location.href='login.php';</script>";
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
    <title>Resident Profile</title>
</head>
<body>
    <h2>Resident Profile</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled><br>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br>

        <label for="state">State:</label>
        <input type="text" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        <button type="submit" name="delete_account" style="background-color: red; color: white;">Delete Account</button>
    </form>

    <a href="resident_dashboard.php">Back to Dashboard</a>
</body>
</html>
