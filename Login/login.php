<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include 'db_connect.php';

// Check database connection
if (!$conn) {
    die("Database connection failed.");
}

// Check form submission
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Invalid request.");
}

// Get form data safely
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? '');

// Validate input
if (empty($username) || empty($password) || empty($role)) {
    die("All fields are required.");
}

// Fetch user
$sql = "SELECT * FROM users WHERE username = ? AND role = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ss", $username, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($role === 'officer') {

            $_SESSION['officer_id'] = $row['id'];
            header("Location: officer_dashboard.php");
            exit();

        } elseif ($role === 'employee') {

            $_SESSION['employee_id'] = $row['id'];

            // Change if you later create employee_dashboard.php
            header("Location: employee.html");
            exit();

        } elseif ($role === 'admin') {

            $_SESSION['admin_id'] = $row['id'];
            header("Location: admin_dashboard.php");
            exit();
        }

    } else {

        echo "<script>
                alert('Invalid password');
                window.location.href='login.html';
              </script>";
    }

} else {

    echo "<script>
            alert('User not found or role mismatch');
            window.location.href='login.html';
          </script>";
}

$stmt->close();
$conn->close();
?>
