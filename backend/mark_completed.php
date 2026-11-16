<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $course_name = trim($_POST['courseName']);
    $units_logged = (int)$_POST['units'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

    if (!empty($course_name) && $units_logged > 0) {
        $sql = "UPDATE study_units 
                SET status = 'completed' 
                WHERE user_id = ? 
                  AND course_code = ? 
                  AND (status IS NULL OR status != 'completed') 
                ORDER BY assigned_date ASC 
                LIMIT ?";
                
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("isi", $user_id, $course_name, $units_logged);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // Refresh the dashboard immediately
    header("Location: ../dashboard.php");
    exit();
}
?>