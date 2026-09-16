<?php
// Database credentials for XAMPP
$host = 'localhost';
$user = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password is empty
$dbname = 'unisync_db';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}
?>