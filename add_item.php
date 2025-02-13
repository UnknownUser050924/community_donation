<?php
include 'db.php';
session_start();

// Ensure the user is logged in and is a donor
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    $_SESSION['message'] = "Unauthorized access!";
    $_SESSION['message_type'] = "error";
    header("Location: donor_login.php");
    exit();
}

$donor_id = $_SESSION["user_id"]; // Get the logged-in donor's ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $quantity = (int) $_POST["quantity"];
    $state = trim($_POST["state"]);
    $address = trim($_POST["address"]);

    // Handle "Others" option for item_type
    $item_type = ($_POST["item_type"] === "Others" && !empty($_POST["other_item_type"])) ? trim($_POST["other_item_type"]) : trim($_POST["item_type"]);

    // Validate length of item_type (Assuming VARCHAR(100))
    if (strlen($item_type) > 100) {
        $_SESSION['message'] = "Error: Item type too long!";
        $_SESSION['message_type'] = "error";
        header("Location: donor_dashboard.php");
        exit();
    }

    // Insert into the database
    $query = $conn->prepare("INSERT INTO items (name, quantity, item_type, state, address, donor_id) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("sisssi", $name, $quantity, $item_type, $state, $address, $donor_id);

    if ($query->execute()) {
        $_SESSION['message'] = "Item added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding item. Please try again.";
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
    <title>Add Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .hidden {
            display: none;
        }
        .btn {
            display: inline-block;
            padding: 10px;
            margin: 10px 5px;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            border: none;
        }
        .btn-submit {
            background-color: #28a745;
            color: white;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 97.3%;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleOtherInput() {
            var itemType = document.getElementById("item_type");
            var otherInput = document.getElementById("other_item_type");

            if (itemType.value === "Others") {
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

<div class="container">
    <h2>➕ Add New Item</h2>

    <form method="POST">
        <label for="name">Item Name:</label>
        <input type="text" name="name" required placeholder="Enter item name">

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required min="1" placeholder="Enter quantity">

        <label for="item_type">Item Type:</label>
        <select name="item_type" id="item_type" onchange="toggleOtherInput()" required>
            <option value="">-- Select Type --</option>
            <option value="Clothing">Clothing</option>
            <option value="Electronics">Electronics</option>
            <option value="Food">Food</option>
            <option value="Furniture">Furniture</option>
            <option value="Others">Others</option>
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
        <input type="text" name="address" required placeholder="Enter full address">

        <button type="submit" class="btn btn-submit">✅ Add Item</button>
        <a href="donor_dashboard.php" class="btn btn-back">⬅️ Back to Dashboard</a>
    </form>
</div>

</body>
</html>
