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
$state_filter = $_GET['state'] ?? $user['state'];
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

// Fetch all items (for Settings section)
$all_items_query = $conn->query("SELECT * FROM items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: #f5f7fa;
            color: #333;
        }
        .header {
            background: #4A90E2;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header h2 {
            font-size: 24px;
            font-weight: bold;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown button {
            background: white;
            color: #4A90E2;
            padding: 8px 15px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1;
        }
        .dropdown-content a {
            color: #333;
            padding: 12px;
            display: block;
            text-decoration: none;
            transition: 0.3s;
        }
        .dropdown-content a:hover {
            background: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .filter-section, .settings-section {
            background: white;
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .filter-section select, .filter-section button {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }
        .filter-section button {
            background: #4A90E2;
            color: white;
            cursor: pointer;
        }
        .filter-section button:hover {
            background: #357ABD;
        }
        .item-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }
        .item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
        }
        .item h3 {
            color: #4A90E2;
            font-size: 18px;
            margin-bottom: 8px;
        }
        .item p {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .item button {
            background: #4A90E2;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .item button:hover {
            background: #357ABD;
        }

        .settings-section h2 {
            text-align: center; /* Centers the text */
            font-size: 22px;
            color: #4A90E2;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<!-- Header -->
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

<!-- All Items Section -->
<div class="settings-section">
    <h2>All Items</h2>
    <div class="item-list">
        <?php while ($item = $all_items_query->fetch_assoc()): ?>
            <div class="item">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p>Type: <?php echo htmlspecialchars($item['item_type']); ?></p>
                <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                <p>State: <?php echo htmlspecialchars($item['state']); ?></p>
                <form action="request_item.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button type="submit">Request Item</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
