<?php
session_start();

// Redirect if the user isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Database Connection
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "unisync_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

$sql_parts = [];
$params = [];
$types = "";

// Handle Profile Picture Upload
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Allowed image formats
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        // Generate a unique filename to prevent overwriting
        $newFileName = md5(time() . $user_id) . '.' . $fileExtension;
        $uploadFileDir = '../uploads/';
        
        // Ensure the uploads folder exists
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $sql_parts[] = "profile_pic = ?";
            $params[] = 'uploads/' . $newFileName;
            $types .= "s";
        }
    }
}

// Update Username if provided
if (!empty($username)) {
    $sql_parts[] = "username = ?";
    $params[] = $username;
    $types .= "s";
    $_SESSION['username'] = $username; // Update live session
}

// Update Email if provided
if (!empty($email)) {
    $sql_parts[] = "email = ?";
    $params[] = $email;
    $types .= "s";
}

// Run database update if there is data to change
if (!empty($sql_parts)) {
    $sql = "UPDATE users SET " . implode(", ", $sql_parts) . " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Redirect back to profile page with success state
header("Location: ../profile.php?success=1");
exit();
?>