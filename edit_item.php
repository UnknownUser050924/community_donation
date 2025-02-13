<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit();
}

$donor_id = $_SESSION["user_id"];

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: donor_dashboard.php");
    exit();
}

$item_id = (int) $_GET["id"];

$query = $conn->prepare("SELECT * FROM items WHERE id = ? AND donor_id = ?");
$query->bind_param("ii", $item_id, $donor_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    header("Location: donor_dashboard.php");
    exit();
}

$item = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST["item_name"];
    $item_type = $_POST["item_type"] === "others" ? $_POST["other_item_type"] : $_POST["item_type"];
    $quantity = $_POST["quantity"];
    $state = $_POST["state"];
    $address = $_POST["address"];

    $updateQuery = $conn->prepare("UPDATE items SET item_name = ?, item_type = ?, quantity = ?, state = ?, address = ? WHERE id = ? AND donor_id = ?");
    $updateQuery->bind_param("ssissii", $item_name, $item_type, $quantity, $state, $address, $item_id, $donor_id);

    if ($updateQuery->execute()) {
        $_SESSION['message'] = "Item updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating item. Please try again.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: donor_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        form {
            display: inline-block;
            text-align: left;
            padding: 20px;
            border: 1px solid black;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        label {
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 5px;
            margin: 5px 0;
        }
        .hidden {
            display: none;
        }
        .btn-submit {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .back-link {
            display: block;
            margin-top: 10px;
        }
    </style>
    <script>
        function toggleOtherType() {
            var itemType = document.getElementById("item_type");
            var otherTypeInput = document.getElementById("other_item_type");

            if (itemType.value === "others") {
                otherTypeInput.classList.remove("hidden");
                otherTypeInput.required = true;
            } else {
                otherTypeInput.classList.add("hidden");
                otherTypeInput.required = false;
            }
        }
    </script>
</head>
<body>

    <h2>✏️ Edit Item</h2>
    
    <?php if ($message) echo "<p><b>$message</b></p>"; ?>

    <form method="POST">
        <label for="name">Item Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" required min="1">

        <label for="item_type">Item Type:</label>
        <select name="item_type" id="item_type" onchange="toggleOtherType()" required>
            <option value="clothing" <?= $item['item_type'] == 'clothing' ? 'selected' : '' ?>>Clothing</option>
            <option value="electronics" <?= $item['item_type'] == 'electronics' ? 'selected' : '' ?>>Electronics</option>
            <option value="food" <?= $item['item_type'] == 'food' ? 'selected' : '' ?>>Food</option>
            <option value="furniture" <?= $item['item_type'] == 'furniture' ? 'selected' : '' ?>>Furniture</option>
            <option value="others" <?= $isOtherType ? 'selected' : '' ?>>Others</option>
        </select>

        <input type="text" name="other_item_type" id="other_item_type" 
               class="<?= $isOtherType ? '' : 'hidden' ?>" 
               placeholder="Enter item type" 
               value="<?= $isOtherType ? htmlspecialchars($item['item_type']) : '' ?>">

        <label for="state">State:</label>
        <select name="state" required>
            <option value="Johor" <?= $item['state'] == 'Johor' ? 'selected' : '' ?>>Johor</option>
            <option value="Kedah" <?= $item['state'] == 'Kedah' ? 'selected' : '' ?>>Kedah</option>
            <option value="Kelantan" <?= $item['state'] == 'Kelantan' ? 'selected' : '' ?>>Kelantan</option>
            <option value="Malacca" <?= $item['state'] == 'Malacca' ? 'selected' : '' ?>>Malacca</option>
            <option value="Penang" <?= $item['state'] == 'Penang' ? 'selected' : '' ?>>Penang</option>
            <option value="Sabah" <?= $item['state'] == 'Sabah' ? 'selected' : '' ?>>Sabah</option>
            <option value="Sarawak" <?= $item['state'] == 'Sarawak' ? 'selected' : '' ?>>Sarawak</option>
            <option value="Selangor" <?= $item['state'] == 'Selangor' ? 'selected' : '' ?>>Selangor</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($item['address']) ?>" required>

        <button type="submit" class="btn-submit">Update Item</button>
    </form>

    <a href="donor_dashboard.php" class="back-link">⬅️ Back to Dashboard</a>

</body>
</html>
