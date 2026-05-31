<?php
session_start();

include 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();

  if (password_verify($password, $row['password'])) {
    $_SESSION['username'] = $row['username'];
    $_SESSION['role'] = $row['role'];

    // Set officer or employee id
    if ($role === 'officer') {
      $_SESSION['officer_id'] = $row['id'];
      header("Location: officer_dashboard.php");
      exit();
    } elseif ($role === 'employee') {
      $_SESSION['employee_id'] = $row['id'];
      header("Location: employee_dashboard.php");
      exit();
    }
  } else {
    echo "<script>alert('Invalid password.'); window.history.back();</script>";
  }
} else {
  echo "<script>alert('User not found or role mismatch.'); window.history.back();</script>";
}

$conn->close();
?>
