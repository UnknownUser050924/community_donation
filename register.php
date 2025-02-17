<?php
include "db.php";
session_start();

$message = ""; // To store success or error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $age = trim($_POST["age"]);
    $gender = trim($_POST["gender"]);
    $address = trim($_POST["address"]);
    $state = trim($_POST["state"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"]; // resident or donor

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $message = "❌ Email already exists!";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, age, gender, address, state, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissssss", $name, $age, $gender, $address, $state, $email, $password, $role);

        if ($stmt->execute()) {
            $message = "✅ Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "⚠️ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .register-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            margin-bottom: 15px;
            color: #007bff;
        }
        .input-group {
            margin: 10px 0;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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
        .message {
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

<div class="register-container">
    <h2>📝 Register</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="name" placeholder="👤 Full Name" required>
        </div>
        <div class="input-group">
            <input type="number" name="age" placeholder="🎂 Age" required>
        </div>
        <div class="input-group">
            <select name="gender" required>
                <option value="Male">🚹 Male</option>
                <option value="Female">🚺 Female</option>
                <option value="Other">⚧ Other</option>
            </select>
        </div>
        <div class="input-group">
            <input type="text" name="address" placeholder="🏠 Address" required>
        </div>
        <div class="input-group">
            <input type="text" name="state" placeholder="📍 State" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="📧 Email" required>
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="🔒 Password" required>
        </div>
        <div class="input-group">
            <select name="role" required>
                <option value="resident">🏡 Resident</option>
                <option value="donor">❤️ Donor</option>
            </select>
        </div>
        <button type="submit" class="btn">Register</button>
    </form>

    <a href="login.php" class="link">🔑 Already have an account? Login</a>
</div>

</body>
</html>
