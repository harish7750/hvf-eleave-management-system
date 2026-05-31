<?php
$conn = new mysqli(
    "zephyr.proxy.rlwy.net",
    "root",
    "ChoNLqhbXxKcmIJBDluloGGheuHBabKY",
    "railway",
    50789
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>