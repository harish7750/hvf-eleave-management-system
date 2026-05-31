<?php
session_start();

// Connect to DB
$host = "localhost";
$user = "root";
$pass = "";
$db = "hvf_leave";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

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
