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

$item_query = $conn->prepare("SELECT * FROM items WHERE id = ?");
$item_query->bind_param("i", $item_id);
$item_query->execute();
$item = $item_query->get_result()->fetch_assoc();

$user_query = $conn->prepare("SELECT name, email, address, state FROM users WHERE id = ?");
$user_query->bind_param("i", $_SESSION["user_id"]);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();
?>

<h2>Request Item</h2>
<form method="POST" action="submit_request.php">
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
    <p>Item: <?php echo htmlspecialchars($item['name']); ?></p>
    <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
    <p>Type: <?php echo htmlspecialchars($item['item_type']); ?></p>
    <p>State: <?php echo htmlspecialchars($item['state']); ?></p>

    <p>Your Name: <?php echo htmlspecialchars($user['name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Address: <?php echo htmlspecialchars($user['address']); ?></p>

    <label for="schedule_date">Select Schedule Date:</label>
    <input type="date" name="schedule_date" required>

    <button type="submit">Submit Request</button>
</form>
