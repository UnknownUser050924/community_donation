<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash password
    $role = $_POST["role"];
    $address = $_POST["address"];
    $state = $_POST["state"];
    $phone = $_POST["phone"];

    // Insert into database
    $query = $conn->prepare("INSERT INTO users (name, email, password, role, address, state, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("sssssss", $name, $email, $password, $role, $address, $state, $phone);

    if ($query->execute()) {
        echo "<script>alert('Account added successfully!'); window.location.href='admin_accounts.php';</script>";
    } else {
        echo "<script>alert('Error adding account. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Account</title>
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

    <h2>Add New Account</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="role">Role:</label>
        <select name="role" required>
            <option value="resident">Resident</option>
            <option value="donor">Donor</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" required>

        <label for="state">State:</label>
        <input type="text" name="state" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" required>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <a href="admin_accounts.php" class="back">🔙 Back to Accounts</a>

</body>
</html>
        