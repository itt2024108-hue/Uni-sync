<?php
session_start();
header('Content-Type: application/json');

// Connect directly to the correct database
$conn = new mysqli("localhost", "root", "", "unisync_db");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseInput = isset($_POST['course_code']) ? $_POST['course_code'] : '';

    if (!empty($courseInput)) {
        
        $fullInput = $conn->real_escape_string(trim($courseInput));
        
        // Split the text to grab just the "ICT 1202" part
        $parts = explode('-', $courseInput);
        $shortCode = $conn->real_escape_string(trim($parts[0])); 

        // 1. Wipe from study_units
        $conn->query("DELETE FROM study_units WHERE course_code = '$fullInput' OR course_code LIKE '$shortCode%'");

        // 2. Wipe from deadlines table
        $conn->query("DELETE FROM deadlines WHERE course_name = '$fullInput' OR course_name LIKE '$shortCode%'");

        // 3. Wipe from the main courses table
        $conn->query("DELETE FROM courses WHERE course_name = '$fullInput' OR course_name LIKE '$shortCode%'");

        // Success! Send signal back to JavaScript to wipe browser memory and reload
        echo json_encode(["status" => "success"]);
        
    } else {
        echo json_encode(["status" => "error", "message" => "No course selected."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>