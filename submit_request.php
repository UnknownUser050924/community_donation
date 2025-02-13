<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "resident") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resident_id = $_SESSION["user_id"];
    $item_id = $_POST["item_id"];
    $schedule_date = $_POST["schedule_date"];

    // Insert request into the database
    $stmt = $conn->prepare("INSERT INTO requests (resident_id, item_id, schedule_date, status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $resident_id, $item_id, $schedule_date);

    if ($stmt->execute()) {
        echo "<h2>Your request has been sent to the donor.</h2>";
        echo "<p>Please wait for them to receive your request. Thank you for your cooperation.</p>";
        echo "<a href='resident_dashboard.php'>Back to Dashboard</a>";
    } else {
        echo "<p>Error submitting request. Please try again.</p>";
    }
}
?>
