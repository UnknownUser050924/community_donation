<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
    exit();
}

// Check if an item ID is provided
if (!isset($_GET["id"])) {
    echo "<script>alert('Invalid request.'); window.location.href='admin_items.php';</script>";
    exit();
}

$item_id = (int) $_GET["id"];

// Fetch item details
$itemQuery = $conn->prepare("SELECT * FROM items WHERE id = ?");
$itemQuery->bind_param("i", $item_id);
$itemQuery->execute();
$item = $itemQuery->get_result()->fetch_assoc();

if (!$item) {
    echo "<script>alert('Item not found.'); window.location.href='admin_items.php';</script>";
    exit();
}

// Fetch all donors
$donorsQuery = $conn->prepare("SELECT id, name FROM users WHERE role = 'donor'");
$donorsQuery->execute();
$donors = $donorsQuery->get_result();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $quantity = (int) $_POST["quantity"];
    $state = trim($_POST["state"]);
    $address = trim($_POST["address"]);
    $donor_id = (int) $_POST["donor_id"];

    // Handle item type
    if ($_POST["item_type"] === "others") {
        $item_type = trim($_POST["other_item_type"]);
    } else {
        $item_type = trim($_POST["item_type"]);
    }

    // Validate length of item_type (Assuming VARCHAR(100))
    if (strlen($item_type) > 100) {
        echo "<script>alert('Error: Item type too long!'); window.history.back();</script>";
        exit();
    }

    $updateQuery = $conn->prepare("UPDATE items SET name = ?, quantity = ?, item_type = ?, state = ?, address = ?, donor_id = ? WHERE id = ?");
    $updateQuery->bind_param("sisssii", $name, $quantity, $item_type, $state, $address, $donor_id, $item_id);

    if ($updateQuery->execute()) {
        echo "<script>alert('Item updated successfully!'); window.location.href='admin_items.php';</script>";
    } else {
        echo "<script>alert('Error updating item. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <script>
        function toggleOtherItemType() {
            var itemType = document.getElementById("item_type").value;
            var otherItemTypeField = document.getElementById("other_item_type_field");
            otherItemTypeField.style.display = (itemType === "others") ? "block" : "none";
        }
    </script>
</head>
<body>
    <h2>Edit Item</h2>
    <form method="POST">
        <label for="name">Item Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required><br>

        <label for="item_type">Item Type:</label>
        <select name="item_type" id="item_type" onchange="toggleOtherItemType()">
            <option value="Food" <?php if ($item["item_type"] == "Food") echo "selected"; ?>>Food</option>
            <option value="Clothes" <?php if ($item["item_type"] == "Clothes") echo "selected"; ?>>Clothes</option>
            <option value="Electronics" <?php if ($item["item_type"] == "Electronics") echo "selected"; ?>>Electronics</option>
            <option value="Furniture" <?php if ($item["item_type"] == "Furniture") echo "selected"; ?>>Furniture</option>
            <option value="others">Others</option>
        </select><br>

        <div id="other_item_type_field" style="display: none;">
            <label for="other_item_type">Specify Item Type:</label>
            <input type="text" name="other_item_type" placeholder="Enter item type">
        </div>

        <label for="state">State:</label>
        <input type="text" name="state" value="<?php echo htmlspecialchars($item['state']); ?>" required><br>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($item['address']); ?>" required><br>

        <label for="donor_id">Donor:</label>
        <select name="donor_id">
            <?php while ($donor = $donors->fetch_assoc()) { ?>
                <option value="<?php echo $donor["id"]; ?>" <?php if ($item["donor_id"] == $donor["id"]) echo "selected"; ?>>
                    <?php echo htmlspecialchars($donor["name"]); ?>
                </option>
            <?php } ?>
        </select><br>

        <button type="submit">Update Item</button>
    </form>

    <a href="admin_items.php">Back to Items</a>

    <script>
        // Ensure "Others" option shows input field if selected by default
        toggleOtherItemType();
    </script>
</body>
</html>
