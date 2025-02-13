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

$checkQuery = $conn->prepare("SELECT id FROM items WHERE id = ? AND donor_id = ?");
$checkQuery->bind_param("ii", $item_id, $donor_id);
$checkQuery->execute();
$result = $checkQuery->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Unauthorized action!";
    $_SESSION['message_type'] = "error";
    header("Location: donor_dashboard.php");
    exit();
}

$deleteQuery = $conn->prepare("DELETE FROM items WHERE id = ? AND donor_id = ?");
$deleteQuery->bind_param("ii", $item_id, $donor_id);

if ($deleteQuery->execute()) {
    $_SESSION['message'] = "Item deleted successfully!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting item. Please try again.";
    $_SESSION['message_type'] = "error";
}

header("Location: donor_dashboard.php");
exit();
