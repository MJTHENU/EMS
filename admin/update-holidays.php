<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $holidayDate = $_POST['holiday-date'];
    
    // Validate the date format
    if (DateTime::createFromFormat('Y-m-d', $holidayDate) !== FALSE) {
        // Check if the date already exists in the holidays table
        $stmt = $conn->prepare("SELECT * FROM holidays WHERE holiday_date = ?");
        $stmt->bind_param("s", $holidayDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // If the holiday already exists, remove it
            $stmt = $conn->prepare("DELETE FROM holidays WHERE holiday_date = ?");
            $stmt->bind_param("s", $holidayDate);
            $stmt->execute();
            echo "Holiday removed successfully.";
        } else {
            // If the holiday doesn't exist, add it
            $stmt = $conn->prepare("INSERT INTO holidays (holiday_date) VALUES (?)");
            $stmt->bind_param("s", $holidayDate);
            $stmt->execute();
            echo "Holiday added successfully.";
        }
        
        $stmt->close();
    } else {
        echo "Invalid date format.";
    }
}
?>
