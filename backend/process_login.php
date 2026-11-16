<?php
session_start();
require 'db.php'; // Make sure this points to your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check for user by username or email
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: ../dashboard.php"); 
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='../login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='../login.php';</script>";
    }
}
?>