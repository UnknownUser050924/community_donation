<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];

// Fetch the donor's own items
$query = $conn->prepare("SELECT * FROM items WHERE donor_id = ?");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            color: white;
            border-radius: 5px;
        }
        .success { background-color: #28a745; }
        .error { background-color: #dc3545; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-add {
            background-color: #007bff;
        }
        .btn-add:hover {
            background-color: #0056b3;
        }
        .btn-edit {
            background-color: #ffc107;
        }
        .btn-edit:hover {
            background-color: #e0a800;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        // Remove success/error message after 3 seconds
        setTimeout(function () {
            var message = document.getElementById("message");
            if (message) {
                message.style.display = "none";
            }
        }, 3000);
    </script>
</head>
<body>

<div class="container">
    <h2>📦 Donor Dashboard</h2>

    <!-- Display success/error message -->
    <?php if (isset($_SESSION['message'])): ?>
        <div id="message" class="message <?= ($_SESSION['message_type'] === "success") ? "success" : "error" ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <a href="add_item.php" class="btn btn-add">➕ Add New Item</a>

    <h3>My Donated Items</h3>
    <table>
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Item Type</th>
            <th>State</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td><?= htmlspecialchars($row['item_type']) ?></td>
                    <td><?= htmlspecialchars($row['state']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td>
                        <a href="edit_item.php?id=<?= $row['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                        <a href="delete_item.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No items added yet.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
