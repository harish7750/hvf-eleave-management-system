<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli(
    getenv('MYSQLHOST'),
    getenv('MYSQLUSER'),
    getenv('MYSQLPASSWORD'),
    getenv('MYSQLDATABASE'),
    getenv('MYSQLPORT')
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
