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

// Delete item
$deleteQuery = $conn->prepare("DELETE FROM items WHERE id = ?");
$deleteQuery->bind_param("i", $item_id);

if ($deleteQuery->execute()) {
    echo "<script>alert('Item deleted successfully!'); window.location.href='admin_items.php';</script>";
} else {
    echo "<script>alert('Error deleting item. Please try again.'); window.location.href='admin_items.php';</script>";
}
?>
