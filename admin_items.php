<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Fetch all items from donors
$query = $conn->prepare("SELECT i.id, i.name, i.status, u.name AS donor_name 
                         FROM items i
                         JOIN users u ON i.donor_id = u.id");
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #ffc107;
            color: black;
        }
        .btn-delete {
            background-color: red;
            color: white;
        }
        .add-item {
            display: inline-block;
            margin: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <h2>🛍️ Manage Donor Items</h2>
    <a href="admin_add_item.php" class="add-item">➕ Add New Item</a>
    
    <table>
        <tr>
            <th>Item Name</th>
            <th>Status</th>
            <th>Donor</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                <td>
                    <a href="admin_edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="admin_delete_item.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">🗑️ Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="admin_index.php">⬅️ Back to Dashboard</a>

</body>
</html>
