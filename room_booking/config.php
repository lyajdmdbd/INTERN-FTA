<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "fast_track_rooms"; // Pastikan nama ni sama kat phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Database Connection Failed!"); }
?>