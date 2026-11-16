<?php
session_start(); // Start the session so we know who is logged in!

// Pull in the connection we just made
require 'db.php';

// Check if a user is actually logged in before letting them save
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // grab the data sent from the javascript
    $name = $_POST['courseName'];
    $date = $_POST['examDate'];
    $user_id = $_SESSION['user_id']; // Grab the specific user's ID

    // FIX: Define $load so PHP doesn't crash!
    // If no workload is provided, default to 0.
    $load = isset($_POST['courseLoad']) ? $_POST['courseLoad'] : 0;

    // Securely prepare the SQL statement to prevent hacking (SQL injection)
    // We added user_id to the INSERT list and added a 4th "?"
    $stmt = $conn->prepare("INSERT INTO courses (course_name, workload, exam_date, user_id) VALUES (?, ?, ?, ?)");
    
    // We changed "sis" to "sisi" (String, Integer, String, Integer) and added $user_id to the end
    $stmt->bind_param("sisi", $name, $load, $date, $user_id);

    // Execute and return a success message
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    
    $stmt->close();
}
?>