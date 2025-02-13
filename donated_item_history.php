<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];

// Fetch only completed donations (status: Arrived at Your Location)
$query = $conn->prepare("
    SELECT i.name AS item_name, r.quantity, u.name AS resident_name, r.scheduled_date 
    FROM requests r
    JOIN items i ON r.item_id = i.id
    JOIN users u ON r.resident_id = u.id
    WHERE r.donor_id = ? AND r.status = 'Arrived at Your Location'
    ORDER BY r.scheduled_date DESC
");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor - Donated Item History</title>
</head>
<body>
    <h2>Donated Item History</h2>
    <table border="1">
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Received By</th>
            <th>Delivery Date</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['scheduled_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No donated items history yet.</td>
            </tr>
        <?php endif; ?>
    </table>

    <a href="donor_dashboard.php">Back to Dashboard</a>
</body>
</html>
