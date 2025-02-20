<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

$resident_id = $_SESSION["user_id"];

// Retrieve request history with additional details
$query = $conn->prepare("
    SELECT i.name AS item_name, r.scheduled_date, r.quantity, r.item_type, r.state
    FROM requests r
    JOIN items i ON r.item_id = i.id
    WHERE r.resident_id = ? AND r.status = 'arrived at your location'
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
    <title>Request History</title>
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
            background-color: #28a745;
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
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        a:hover {
            background: #218838;
        }
    </style>
</head>
<body>

    <h2>Request History</h2>
    
    <table>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Item Type</th>
            <th>State</th>
            <th>Scheduled Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                <td><?php echo htmlspecialchars($row['state']); ?></td>
                <td><?php echo htmlspecialchars($row['scheduled_date']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="resident_dashboard.php">Back to Dashboard</a>

</body>
</html>
