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
$isOtherType = !in_array($item['item_type'], ["Clothing", "Electronics", "Food", "Furniture"]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST["name"]);
    $quantity = (int) $_POST["quantity"];
    $state = trim($_POST["state"]);
    $address = trim($_POST["address"]);

    $item_type = ($_POST["item_type"] === "Others" && !empty($_POST["other_item_type"])) ? trim($_POST["other_item_type"]) : trim($_POST["item_type"]);

    $updateQuery = $conn->prepare("UPDATE items SET name = ?, item_type = ?, quantity = ?, state = ?, address = ? WHERE id = ? AND donor_id = ?");
    $updateQuery->bind_param("ssissii", $item_name, $item_type, $quantity, $state, $address, $item_id, $donor_id);

    if ($updateQuery->execute()) {
        $_SESSION['message'] = "✅ Item updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Error updating item. Please try again.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: donor_dashboard.php");
    exit();
}

// Retrieve session message
$message = $_SESSION['message'] ?? "";
$message_type = $_SESSION['message_type'] ?? "";
unset($_SESSION['message'], $_SESSION['message_type']);
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
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            margin: 50px auto;
            width: 400px;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .hidden {
            display: none;
        }
        .btn-submit {
            margin-top: 15px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function toggleOtherType() {
            var itemType = document.getElementById("item_type");
            var otherTypeInput = document.getElementById("other_item_type");

            if (itemType.value === "Others") {
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

    <div class="container">
        <h2>✏️ Edit Item</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Item Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" required min="1">

            <label for="item_type">Item Type:</label>
            <select name="item_type" id="item_type" onchange="toggleOtherType()" required>
                <option value="Clothing" <?= $item['item_type'] == 'Clothing' ? 'selected' : '' ?>>Clothing</option>
                <option value="Electronics" <?= $item['item_type'] == 'Electronics' ? 'selected' : '' ?>>Electronics</option>
                <option value="Food" <?= $item['item_type'] == 'Food' ? 'selected' : '' ?>>Food</option>
                <option value="Furniture" <?= $item['item_type'] == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                <option value="Others" <?= $isOtherType ? 'selected' : '' ?>>Others</option>
            </select>

            <input type="text" name="other_item_type" id="other_item_type" 
                   class="<?= $isOtherType ? '' : 'hidden' ?>" 
                   placeholder="Enter item type" 
                   value="<?= $isOtherType ? htmlspecialchars($item['item_type']) : '' ?>">

            <label for="state">State:</label>
            <select name="state" required>
                <?php 
                $states = ["Johor", "Kedah", "Kelantan", "Malacca", "Penang", "Sabah", "Sarawak", "Selangor"];
                foreach ($states as $state) {
                    $selected = ($item['state'] == $state) ? 'selected' : '';
                    echo "<option value='$state' $selected>$state</option>";
                }
                ?>
            </select>

            <label for="address">Address:</label>
            <input type="text" name="address" value="<?= htmlspecialchars($item['address']) ?>" required>

            <button type="submit" class="btn-submit">Update Item</button>
        </form>

        <a href="donor_dashboard.php" class="back-link">⬅️ Back to Dashboard</a>
    </div>

</body>
</html>
