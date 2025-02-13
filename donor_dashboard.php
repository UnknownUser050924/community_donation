<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];
$donor_name = isset($_SESSION["name"]) ? $_SESSION["name"] : "Donor"; // Default if not set

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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            padding: 20px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header {
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            z-index: 100;
        }

        .welcome {
            font-size: 18px;
            font-weight: bold;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown button {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .dropdown button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
            border-radius: 5px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            display: block;
            padding: 12px;
            color: black;
            text-decoration: none;
            transition: 0.3s;
        }

        .dropdown-content a:hover {
            background: #f1f1f1;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        table, th, td {
            border: none;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .message {
            padding: 12px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }

        .success { background-color: #28a745; }
        .error { background-color: #dc3545; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .container {
                width: 100%;
                padding: 15px;
            }

            table, th, td {
                font-size: 14px;
                padding: 10px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
    </style>
    <script>
        // Remove success/error message after 3 seconds
        setTimeout(function () {
            var message = document.getElementById("message");
            if (message) {
                message.style.opacity = "0";
                setTimeout(() => message.style.display = "none", 500);
            }
        }, 3000);
    </script>
</head>
<body>

<div class="header">
    <div class="welcome">Welcome, <?= htmlspecialchars($donor_name) ?>!</div>
    <div class="dropdown">
        <button>☰ Menu</button>
        <div class="dropdown-content">
            <a href="profile.php">👤 Profile</a>
            <a href="donations.php">📦 My Donations</a>
            <a href="donated_item_history">📜 Donated History</a>
            <a href="logout_donor.php">🚪 Logout</a>
        </div>
    </div>
</div>

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
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['quantity']) ?></td>
                <td><?= htmlspecialchars($row['item_type']) ?></td>
                <td><?= htmlspecialchars($row['state']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                    <a href="edit_item.php?id=<?= $row['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="delete_item.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure?');">🗑️ Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
