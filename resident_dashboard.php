<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$query = $conn->prepare("SELECT name, email, address, state FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Fetch distinct states and item types for filtering
$states_query = $conn->query("SELECT DISTINCT state FROM items");
$item_types_query = $conn->query("SELECT DISTINCT item_type FROM items");

// Fetch items based on filters
$state_filter = $_GET['state'] ?? $user['state']; // Default to resident's state
$item_type_filter = $_GET['item_type'] ?? '';

$sql = "SELECT * FROM items WHERE state = ?";
$params = [$state_filter];

if (!empty($item_type_filter)) {
    $sql .= " AND item_type = ?";
    $params[] = $item_type_filter;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$items_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f4f4f4;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .filter-section {
            margin: 20px 0;
        }
        .item-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .item {
            border: 1px solid #ccc;
            padding: 10px;
            width: 250px;
            text-align: center;
        }
        button {
            background: blue;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
        }
        button:hover {
            background: darkblue;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <div class="dropdown">
        <button>Menu ▼</button>
        <div class="dropdown-content">
            <a href="profile.php">Profile</a>
            <a href="scheduled_items.php">Scheduled Item List</a>
            <a href="request_history.php">Request History</a>
            <a href="logout_resident.php">Logout</a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET">
        <label for="state">Select State:</label>
        <select name="state" id="state">
            <?php while ($row = $states_query->fetch_assoc()): ?>
                <option value="<?php echo $row['state']; ?>" <?php echo ($row['state'] == $state_filter) ? 'selected' : ''; ?>>
                    <?php echo $row['state']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="item_type">Select Item Type:</label>
        <select name="item_type" id="item_type">
            <option value="">All</option>
            <?php while ($row = $item_types_query->fetch_assoc()): ?>
                <option value="<?php echo $row['item_type']; ?>" <?php echo ($row['item_type'] == $item_type_filter) ? 'selected' : ''; ?>>
                    <?php echo $row['item_type']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Filter</button>
    </form>
</div>

<!-- Item List -->
<div class="item-list">
    <?php while ($item = $items_result->fetch_assoc()): ?>
        <div class="item">
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p>Type: <?php echo htmlspecialchars($item['item_type']); ?></p>
            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
            <p>State: <?php echo htmlspecialchars($item['state']); ?></p>
            <form method="POST" action="request_item.php">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                <button type="submit">Request</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
