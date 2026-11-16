<?php
session_start();
header('Content-Type: application/json');

// If the user is logged out, send back empty data so the homepage stays blank
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]); 
    exit();
}

require 'db.php';
$user_id = $_SESSION['user_id'];

if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

// Safely fetch all courses and count the progress directly from study_units
$sql = "SELECT c.*,
        (SELECT COUNT(*) FROM study_units WHERE course_code = c.course_name AND user_id = $user_id) AS workload,
        (SELECT COUNT(*) FROM study_units WHERE course_code = c.course_name AND status = 'completed' AND user_id = $user_id) AS completed_workload
        FROM courses c
        WHERE c.user_id = $user_id
        ORDER BY c.exam_date ASC";
$result = $conn->query($sql);
$courses = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Safety fallbacks so JavaScript never receives empty data
        if (!isset($row['completed_workload']) || $row['completed_workload'] === null) {
            $row['completed_workload'] = 0;
        }
        if (!isset($row['workload']) || $row['workload'] === null) {
            $row['workload'] = 0;
        }
        $courses[] = $row;
    }
}

echo json_encode($courses);
$conn->close();
?>