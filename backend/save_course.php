<?php
// Pull in the connection we just made
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Grab the data sent from the JavaScript
    $name = $_POST['courseName'];
    $load = $_POST['courseLoad'];
    $date = $_POST['examDate'];

    // Securely prepare the SQL statement to prevent hacking (SQL injection)
    $stmt = $conn->prepare("INSERT INTO courses (course_name, workload, exam_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $name, $load, $date);

    // Execute and return a success message
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }
    
    $stmt->close();
}
$conn->close();
?>