<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

$resident_id = $_SESSION["user_id"];

// Corrected SQL Query: Use `resident_id` instead of `user_id`
$query = $conn->prepare("
    SELECT r.id, i.name AS item_name, r.quantity, r.state, r.status, r.scheduled_date 
    FROM requests r
    JOIN items i ON r.item_id = i.id
    WHERE r.resident_id = ?
");
$query->bind_param("i", $resident_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            display: block;
            width: fit-content;
            margin: 20px auto;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Scheduled Items</h2>
    
    <table>
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>State</th>
            <th>Status</th>
            <th>Scheduled Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['state']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['scheduled_date']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="resident_dashboard.php">Back to Dashboard</a>

</body>
</html>
