<?php
// Pull in the database connection
require 'db.php';

// Ask MySQL for everything in the courses table, sorted by exam date
$sql = "SELECT course_name, workload, exam_date FROM courses ORDER BY exam_date ASC";
$result = $conn->query($sql);

$courses = array();

// If we found courses, add them to our array
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Send the data back as JSON
echo json_encode($courses);
$conn->close();
?>