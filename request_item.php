<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

$item_id = $_POST['item_id'] ?? null;
if (!$item_id) {
    header("Location: resident_dashboard.php");
    exit();
}

// Fetch item details
$item_query = $conn->prepare("SELECT * FROM items WHERE id = ?");
$item_query->bind_param("i", $item_id);
$item_query->execute();
$item = $item_query->get_result()->fetch_assoc();

if (!$item) {
    die("Error: Item not found. <a href='resident_dashboard.php'>Go back</a>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
            margin: 20px;
        }
        .container {
            width: 90%;
            max-width: 600px;
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
        .info {
            text-align: left;
            padding: 10px;
            font-size: 16px;
        }
        .info p {
            margin: 5px 0;
        }
        form {
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input[type="number"], input[type="date"] {
            width: 96.8%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .back-link:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>📦 Request Item</h2>
        <form method="POST" action="submit_request.php">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">

            <div class="info">
                <p><strong>Item:</strong> <?php echo htmlspecialchars($item['name']); ?></p>
                <p><strong>Available Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($item['item_type']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($item['state']); ?></p>
            </div>

            <label for="quantity">Enter Quantity:</label>
            <input type="number" name="quantity" min="1" max="<?php echo htmlspecialchars($item['quantity']); ?>" required>

            <label for="scheduled_date">Select Schedule Date:</label>
            <input type="date" name="scheduled_date" required>

            <button type="submit">Submit Request</button>
        </form>

        <a href="resident_dashboard.php" class="back-link">⬅️ Back to Dashboard</a>
    </div>

</body>
</html>
