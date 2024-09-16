<?php
// Including the database connection file
require_once('../inc/connection.php');

// Getting id of the data from URL
$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieving and sanitizing form inputs
    $reason = trim($_POST['reason']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $leave_type = trim($_POST['leave_type']);

    // Validate reason
    if (empty($reason)) {
        $errors[] = 'Reason is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $reason)) {
        $errors[] = 'Reason must contain only letters and spaces.';
    } elseif (strlen($reason) > 500) {
        $errors[] = 'Reason cannot exceed 500 characters.';
    }

    // Validate leave_type
    if (empty($leave_type)) {
        $errors[] = 'Leave Type is required.';
    }

    // Validate start date
    $current_date = date('Y-m-d');
    if (empty($start_date)) {
        $errors[] = 'Start Date is required.';
    } elseif ($start_date < $current_date) {
        $errors[] = 'Start Date cannot be in the past.';
    }

    // Validate end date
    if (empty($end_date)) {
        $errors[] = 'End Date is required.';
    } elseif ($end_date < $start_date) {
        $errors[] = 'End Date cannot be before Start Date.';
    }

    if (count($errors) > 0) {
        // Print errors as JavaScript alerts
        echo "<script type='text/javascript'>";

        foreach ($errors as $error) {
            echo "alert('$error');";
        }

        echo "window.history.back();"; // Redirect back to the previous page
        echo "</script>";

    } else {
        // Sanitize inputs to prevent SQL injection
        $id = mysqli_real_escape_string($conn, $id);
        $reason = mysqli_real_escape_string($conn, $reason);
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $leave_type = mysqli_real_escape_string($conn, $leave_type);

        // Preparing SQL query
        $sql = "INSERT INTO `employee_leave` (`emp_id`, `token`, `start_date`, `end_date`, `reason`, `leave_type`, `status`) VALUES ('$id', '', '$start_date', '$end_date', '$reason', '$leave_type', 'Pending')";

        // Executing SQL query
        if (mysqli_query($conn, $sql)) {
            echo "<script type='text/javascript'>
                    alert('Leave request submitted successfully.');
                    window.location.href = '../../index.php?emp_id=$id';
                  </script>";
        } else {
            // Handling SQL execution error
            echo "<script type='text/javascript'>
                    alert('Error: " . mysqli_error($conn) . "');
                    window.history.back();
                  </script>";
        }
    }
} else {
    echo "<script type='text/javascript'>
            alert('Invalid request method.');
            window.history.back();
          </script>";
}
?>
