<?php
session_start(); // Required to get the logged-in user
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['courseName'];
    $new_workload = (int)$_POST['newWorkload'];
    
    // Fallback in case sessions aren't fully set up yet in this file
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

    // Securely update the workload where the course name matches
    $stmt = $conn->prepare("UPDATE courses SET workload = ? WHERE course_name = ?");
    $stmt->bind_param("is", $new_workload, $course_name);

    if ($stmt->execute()) {
        
        // ==========================================
        // NEW: DISTRIBUTE UNITS INTO study_units
        // ==========================================
        
        // If your frontend JS doesn't send daysRemaining yet, we default to 7 days
        $days_remaining = isset($_POST['daysRemaining']) ? (int)$_POST['daysRemaining'] : 7;
        
        $base_units_per_day = floor($new_workload / $days_remaining); 
        $leftover_units = $new_workload % $days_remaining;             

        $insert_stmt = $conn->prepare("INSERT INTO study_units (user_id, course_code, assigned_date, status) VALUES (?, ?, ?, 'pending')");
        $current_date = new DateTime(); 

        for ($day = 0; $day < $days_remaining; $day++) {
            $units_for_this_day = $base_units_per_day + ($day < $leftover_units ? 1 : 0);
            $formatted_date = $current_date->format('Y-m-d');
            
            for ($unit = 0; $unit < $units_for_this_day; $unit++) {
                $insert_stmt->bind_param("iss", $user_id, $course_name, $formatted_date);
                $insert_stmt->execute();
            }
            $current_date->modify('+1 day');
        }
        $insert_stmt->close();
        
        // ==========================================
        
        // Return the exact same JSON success message your frontend expects
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>