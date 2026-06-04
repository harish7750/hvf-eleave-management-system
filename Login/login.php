<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method");
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

if (empty($username) || empty($password) || empty($role)) {
    die("All fields are required");
}

$sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ss", $username, $role);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {

    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        }

        if ($row['role'] === 'officer') {
            $_SESSION['officer_id'] = $row['id'];
            header("Location: officer_dashboard.php");
            exit();
        }

        if ($row['role'] === 'employee') {
            $_SESSION['employee_id'] = $row['id'];
            header("Location: employee_dashboard.php");
            exit();
        }

    } else {

        echo "<script>
                alert('Invalid password');
                window.location='index.html';
              </script>";
    }

} else {

    echo "<script>
            alert('User not found or role mismatch');
            window.location='index.html';
          </script>";
}

$stmt->close();
$conn->close();

?>
