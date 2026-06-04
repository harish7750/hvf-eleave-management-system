<?php
$conn = new mysqli(
    "sqlXXX.epizy.com",
    "epiz_xxxxx",
    "your_password",
    "epiz_xxxxx_hvf"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
