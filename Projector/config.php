<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projector_db";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die('{"status":"error","message":"DB Error: ' . $conn->connect_error . '"}');
}

define('MAX_BOOKING_PER_SLOT', 10);
?>