<?php
include "db.php";
session_start();

// Redirect if already logged in
if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "admin") {
    header("Location: admin_index.php");
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Fetch admin from database
    $query = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND role = 'admin'");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $admin["password"])) {
            $_SESSION["user_id"] = $admin["id"];
            $_SESSION["name"] = $admin["name"];
            $_SESSION["role"] = "admin";

            header("Location: admin_index.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Password:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
