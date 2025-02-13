<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: admin_login.php");
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

    // Ensure item_type is within database limits
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

    $insertQuery = $conn->prepare("INSERT INTO items (name, quantity, item_type, state, address, donor_id) VALUES (?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("sisssi", $name, $quantity, $item_type, $state, $address, $donor_id);

    if ($insertQuery->execute()) {
        echo "<script>alert('Item added successfully!'); window.location.href='admin_items.php';</script>";
    } else {
        echo "<script>alert('Error adding item. Please try again.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Item</title>
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
        function toggleOtherInput() {
            var itemType = document.getElementById("item_type");
            var otherInput = document.getElementById("other_item_type");

            if (itemType.value === "others") {
                otherInput.classList.remove("hidden");
                otherInput.required = true;
            } else {
                otherInput.classList.add("hidden");
                otherInput.required = false;
            }
        }
    </script>
</head>
<body>

    <h2>➕ Add New Item</h2>

    <form method="POST">
        <label for="name">Item Name:</label>
        <input type="text" name="name" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required min="1">

        <label for="item_type">Item Type:</label>
        <select name="item_type" id="item_type" onchange="toggleOtherInput()" required>
            <option value="">-- Select Type --</option>
            <option value="clothing">Clothing</option>
            <option value="electronics">Electronics</option>
            <option value="food">Food</option>
            <option value="furniture">Furniture</option>
            <option value="others">Others</option>
        </select>

        <input type="text" name="other_item_type" id="other_item_type" class="hidden" placeholder="Enter item type">

        <label for="state">State:</label>
        <select name="state" required>
            <option value="">-- Select State --</option>
            <option value="Johor">Johor</option>
            <option value="Kedah">Kedah</option>
            <option value="Kelantan">Kelantan</option>
            <option value="Malacca">Malacca</option>
            <option value="Negeri Sembilan">Negeri Sembilan</option>
            <option value="Pahang">Pahang</option>
            <option value="Penang">Penang</option>
            <option value="Perak">Perak</option>
            <option value="Perlis">Perlis</option>
            <option value="Sabah">Sabah</option>
            <option value="Sarawak">Sarawak</option>
            <option value="Selangor">Selangor</option>
            <option value="Terengganu">Terengganu</option>
            <option value="Kuala Lumpur">Kuala Lumpur</option>
            <option value="Labuan">Labuan</option>
            <option value="Putrajaya">Putrajaya</option>
        </select>

        <label for="address">Address:</label>
        <input type="text" name="address" required>

        <label for="donor_id">Donor:</label>
        <select name="donor_id" required>
            <option value="">-- Select Donor --</option>
            <?php while ($row = $donors->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="btn-submit">Add Item</button>
    </form>

    <a href="admin_items.php" class="back-link">⬅️ Back to Items List</a>

</body>
</html>
