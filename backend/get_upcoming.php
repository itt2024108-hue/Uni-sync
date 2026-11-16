<?php
session_start();
header('Content-Type: application/json');

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
$sql = "SELECT * FROM courses WHERE user_id = $user_id ORDER BY exam_date ASC";
$result = $conn->query($sql);

$courses = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
    
        if (!isset($row['completed_workload'])) {
            $row['completed_workload'] = 0;
        }
        $courses[] = $row;
    }
}


echo json_encode($courses);

$conn->close();
?>