<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Fetch history records with correct column name (use `name` instead of `username`)
$query = $conn->query("SELECT h.*, u.name FROM history h JOIN users u ON h.user_id = u.id ORDER BY h.timestamp DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Admin - Activity History</h2>
    <table>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $query->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row["name"]); ?></td>
                <td><?php echo htmlspecialchars($row["action"]); ?></td>
                <td><?php echo htmlspecialchars($row["timestamp"]); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_index.php">Back to Dashboard</a>
</body>
</html>
