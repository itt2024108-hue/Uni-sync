<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    // Hash the password for security
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        // Automatically log them in after signing up
        $_SESSION['user_id'] = $stmt->insert_id;
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "<script>alert('Username or Email already exists!'); window.location.href='../signup.php';</script>";
    }
}
?>