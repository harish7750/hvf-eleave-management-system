<?php
session_start();

// Connect to DB
include 'db_connect.php';

// Get officer ID from session
$officer_id = $_SESSION['officer_id'] ?? 1; // Default 1 if session not set

// Get form data
$date = $_POST['date'];
$type = $_POST['type'];
$reason = $_POST['reason'];

// Insert into leaves table
$sql = "INSERT INTO leaves (officer_id, date, type, reason) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $officer_id, $date, $type, $reason);

if ($stmt->execute()) {
  echo "<script>alert('Leave applied successfully.'); window.location.href='officer_dashboard.php';</script>";
} else {
  echo "<script>alert('Error applying leave.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
