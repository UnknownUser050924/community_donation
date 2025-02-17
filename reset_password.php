<?php
include "db.php";
session_start();

$message = ""; // To store success or error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $current_password = trim($_POST["current_password"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($new_password !== $confirm_password) {
        $message = "❌ Passwords do not match!";
    } else {
        // Fetch user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($current_password, $user["password"])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_password, $email);

            if ($update_stmt->execute()) {
                $message = "✅ Password updated successfully! <a href='login.php'>Login here</a>";
            } else {
                $message = "⚠️ Error updating password.";
            }
        } else {
            $message = "❌ Invalid current password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
            background: linear-gradient(135deg, #ff6b6b, #ffcc5c);
        }
        .reset-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .reset-container h2 {
            margin-bottom: 15px;
            color: #ff6b6b;
        }
        .input-group {
            margin: 10px 0;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            background: #ff6b6b;
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
            background: #e14a4a;
        }
        .message {
            font-size: 14px;
            margin-top: 10px;
        }
        .link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            text-decoration: none;
            color: #ff6b6b;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <h2>🔑 Reset Password</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="📧 Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="current_password" placeholder="🔒 Current Password" required>
        </div>
        <div class="input-group">
            <input type="password" name="new_password" placeholder="🔑 New Password" required>
        </div>
        <div class="input-group">
            <input type="password" name="confirm_password" placeholder="🔁 Confirm Password" required>
        </div>
        <button type="submit" class="btn">Reset Password</button>
    </form>

    <a href="login.php" class="link">🔙 Back to Login</a>
</div>

</body>
</html>
