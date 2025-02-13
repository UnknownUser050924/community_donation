<?php
include "db.php";
session_start();

$error = ""; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        // Fetch user details
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["name"] = $user["name"];

            // Redirect based on role
            if ($user["role"] == "resident") {
                header("Location: resident_dashboard.php");
            } elseif ($user["role"] == "donor") {
                header("Location: donor_dashboard.php");
            } elseif ($user["role"] == "admin") {
                header("Location: admin_dashboard.php");
            }
            exit();
        } else {
            $error = "❌ Invalid email or password.";
        }
    } else {
        $error = "⚠️ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #007bff, #00d4ff);
        }
        .login-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 350px;
        }
        .login-container h2 {
            margin-bottom: 15px;
            color: #007bff;
        }
        .input-group {
            margin: 10px 0;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background: #007bff;
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
            background: #0056b3;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
        .link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            text-decoration: none;
            color: #007bff;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>🔑 Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="📧 Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="🔒 Password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <a href="reset_password.php" class="link">🔄 Reset Password</a>
</div>

</body>
</html>
