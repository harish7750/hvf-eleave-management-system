<?php
include 'db_connect.php';
// Receive data
$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password'];
$designation = $_POST['designation'];
$email = $_POST['email'];
$role = $_POST['role'];

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into users table
$user_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("sss", $username, $hashed_password, $role);

if ($user_stmt->execute()) {
  $user_id = $conn->insert_id; // ✅ this gets the ID of the inserted user

  // If officer, insert into officer table
  if ($role === "officer") {
    $officer_sql = "INSERT INTO officer (id, name, designation, email) VALUES (?, ?, ?, ?)";
    $officer_stmt = $conn->prepare($officer_sql);
    $officer_stmt->bind_param("isss", $user_id, $name, $designation, $email);

    if ($officer_stmt->execute()) {
      echo "<script>alert('Signup successful! You can now log in.'); window.location.href='login.html';</script>";
    } else {
      echo "<script>alert('Signup failed: Officer details not saved.'); window.history.back();</script>";
    }
  } else {
    echo "<script>alert('Signup successful! You can now log in.'); window.location.href='index.html';</script>";
  }
} else {
  echo "<script>alert('Signup failed: Username might already exist.'); window.history.back();</script>";
}

$conn->close();
?>
