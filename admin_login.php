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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #ffffff;
            padding: 30px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-container h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .admin-icon {
            font-size: 50px;
            color: #007bff;
        }

        label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        input {
            width: 92.8%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .login-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="admin-icon">🔒</div>
    <h2>Admin Login</h2>

    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required placeholder="Enter your admin email">
        
        <label>Password:</label>
        <input type="password" name="password" required placeholder="Enter your password">
        
        <button type="submit" class="login-btn">Login</button>
    </form>
</div>

</body>
</html>
