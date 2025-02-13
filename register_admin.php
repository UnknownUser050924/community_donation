<?php
include "db.php";

// Set admin credentials (Change these manually)
$admin_name = "Admin";
$admin_email = "admin@gmail.com";
$admin_password = password_hash("admin123", PASSWORD_DEFAULT); // Hash password for security

// Check if the admin already exists
$checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkQuery->bind_param("s", $admin_email);
$checkQuery->execute();
$checkQuery->store_result();

if ($checkQuery->num_rows > 0) {
    echo "Admin already exists. No need to register again.";
} else {
    // Insert admin into `users` table
    $query = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $query->bind_param("sss", $admin_name, $admin_email, $admin_password);
    
    if ($query->execute()) {
        echo "Admin registered successfully!";
    } else {
        echo "Error registering admin.";
    }
}

// Close database connection
$conn->close();
?>
