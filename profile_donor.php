<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];
$message = "";

// Fetch donor's profile details
$query = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();
$donor = $result->fetch_assoc();

// Update profile logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    $update_query = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    $update_query->bind_param("ssssi", $name, $email, $phone, $address, $donor_id);

    if ($update_query->execute()) {
        $message = "Profile updated successfully!";
        // Refresh donor data
        $donor = ["name" => $name, "email" => $email, "phone" => $phone, "address" => $address];
    } else {
        $message = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Profile</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding: 20px; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; cursor: pointer; }
        .message { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Donor Profile</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($donor['name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($donor['email']); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($donor['phone']); ?>" required>

            <label>Address:</label>
            <textarea name="address" required><?php echo htmlspecialchars($donor['address']); ?></textarea>

            <button type="submit">Update Profile</button>
        </form>
        <br>
        <a href="donor_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
