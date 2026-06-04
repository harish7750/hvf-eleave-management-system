<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method");
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$designation = trim($_POST['designation'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = trim($_POST['role'] ?? '');

if (
    empty($name) ||
    empty($username) ||
    empty($password) ||
    empty($role)
) {
    die("Required fields are missing");
}

$check_sql = "SELECT id FROM users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);

if (!$check_stmt) {
    die("Prepare failed: " . $conn->error);
}

$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {

    echo "<script>
            alert('Username already exists');
            window.history.back();
          </script>";
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$user_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    die("Prepare failed: " . $conn->error);
}

$user_stmt->bind_param(
    "sss",
    $username,
    $hashed_password,
    $role
);

if (!$user_stmt->execute()) {
    die("User insert failed: " . $user_stmt->error);
}

$user_id = $conn->insert_id;

if ($role === "officer") {

    $officer_sql = "INSERT INTO officer (id, name, designation, email)
                    VALUES (?, ?, ?, ?)";

    $officer_stmt = $conn->prepare($officer_sql);

    if (!$officer_stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $officer_stmt->bind_param(
        "isss",
        $user_id,
        $name,
        $designation,
        $email
    );

    if (!$officer_stmt->execute()) {
        die("Officer insert failed: " . $officer_stmt->error);
    }
}

echo "<script>
        alert('Signup successful');
        window.location.href='login.html';
      </script>";

$conn->close();

?>
