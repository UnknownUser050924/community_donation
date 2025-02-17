<?php
require 'db.php'; // Ensure database connection is included
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    die("Error: Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and validate input values
    $resident_id = $_SESSION["user_id"]; // Get resident ID from session
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $scheduled_date = isset($_POST['scheduled_date']) ? $_POST['scheduled_date'] : '';

    // Debug: Print received values
    // echo "<pre>"; print_r($_POST); echo "</pre>"; exit(); // Uncomment for debugging

    // Check if required fields are provided
    if ($item_id == 0 || $quantity <= 0 || empty($scheduled_date)) {
        die("Error: Missing required fields.");
    }

    // Step 1: Check if item exists and has enough stock
    $itemQuery = "SELECT id, name, item_type, state, address, quantity, donor_id FROM items WHERE id = ?";
    if ($stmt = $conn->prepare($itemQuery)) {
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        if (!$item) {
            die("Error: Item not found.");
        }
        if ($item['quantity'] < $quantity) {
            die("Error: Not enough stock available.");
        }
    } else {
        die("Error checking item: " . $conn->error);
    }

    // Extract item details
    $donor_id = $item['donor_id'];
    $item_name = $item['name'];
    $item_type = $item['item_type'];
    $state = $item['state'];
    $address = $item['address'];

    // Step 2: Insert the request into the `requests` table
    $sql = "INSERT INTO requests (resident_id, donor_id, item_id, item_name, quantity, item_type, state, address, status, created_at, scheduled_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiisissss", $resident_id, $donor_id, $item_id, $item_name, $quantity, $item_type, $state, $address, $scheduled_date);
        
        if ($stmt->execute()) {
            // Step 3: Update the item stock in the `items` table
            $updateStock = "UPDATE items SET quantity = quantity - ? WHERE id = ?";
            if ($updateStmt = $conn->prepare($updateStock)) {
                $updateStmt->bind_param("ii", $quantity, $item_id);
                $updateStmt->execute();
                $updateStmt->close();
            }

            echo "<script>alert('Request submitted successfully!'); window.location.href = 'resident_dashboard.php';</script>";
        } else {
            echo "Error inserting request: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

$conn->close();
?>
