<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "fta_tempahan"; // Pastikan nama ni sama kat phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Database Connection Failed!"); }
?>