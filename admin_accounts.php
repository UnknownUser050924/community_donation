<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Fetch all user accounts (excluding admin)
$query = $conn->prepare("SELECT id, name, email, role FROM users WHERE role IN ('resident', 'donor')");
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
        }
        .edit {
            background-color: #28a745;
        }
        .delete {
            background-color: #dc3545;
        }
        .back {
            background-color: #007bff;
            padding: 8px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h2>Manage User Accounts</h2>
    <a href="admin_add_account.php" class="btn back">➕ Add Account</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['role'])); ?></td>
                <td>
                    <a href="admin_edit_account.php?id=<?php echo $row['id']; ?>" class="btn edit">Edit</a>
                    <a href="admin_delete_account.php?id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this account?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_index.php" class="btn back">🔙 Back to Dashboard</a>

</body>
</html>
