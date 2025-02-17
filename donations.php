<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];

// Fetch donation requests linked to this donor
$query = $conn->prepare("
    SELECT r.id, i.name AS item_name, r.quantity, u.name AS resident_name, r.status, r.scheduled_date 
    FROM requests r
    JOIN items i ON r.item_id = i.id
    JOIN users u ON r.resident_id = u.id
    WHERE r.donor_id = ?
");
$query->bind_param("i", $donor_id);
$query->execute();
$result = $query->get_result();

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {
    $request_id = $_POST["request_id"];
    $new_status = $_POST["status"];

    $updateQuery = $conn->prepare("UPDATE requests SET status = ? WHERE id = ? AND donor_id = ?");
    $updateQuery->bind_param("sii", $new_status, $request_id, $donor_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='donations.php';</script>";
    } else {
        echo "<script>alert('Error updating status. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor - Donation Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
            margin: 20px;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        select, button {
            padding: 8px;
            margin-top: 5px;
            font-size: 14px;
        }
        select {
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 6px;
        }
        .btn-update {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-update:hover {
            background-color: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>📦 Donation Requests</h2>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Requested By</th>
                <th>Status</th>
                <th>Scheduled Date</th>
                <th>Update Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['scheduled_date']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php if ($row['status'] == "Pending") echo "selected"; ?>>Pending</option>
                                <option value="Shipping" <?php if ($row['status'] == "Shipping") echo "selected"; ?>>Shipping</option>
                                <option value="Delivering" <?php if ($row['status'] == "Delivering") echo "selected"; ?>>Delivering</option>
                                <option value="Arrived at Your Location" <?php if ($row['status'] == "Arrived at Your Location") echo "selected"; ?>>Arrived at Your Location</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-update">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="donor_dashboard.php" class="back-link">⬅️ Back to Dashboard</a>
    </div>

</body>
</html>
