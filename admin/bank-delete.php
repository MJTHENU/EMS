<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Check if the emp_id and token are set
if (isset($_GET['emp_id']) && isset($_GET['token'])) {
    $emp_id = mysqli_real_escape_string($conn, $_GET['emp_id']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Fetch the employee details
    $sql = "SELECT employee.first_name, employee_bank_details.status 
            FROM employee 
            INNER JOIN employee_bank_details 
            ON employee.emp_id = employee_bank_details.emp_id 
            WHERE employee.emp_id = '$emp_id' AND employee_bank_details.token = '$token'";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $employee = mysqli_fetch_assoc($result);
        $first_name = $employee['first_name'];
        $status = strtolower(trim($employee['status']));

        // Check if the status is not already cancelled
        if ($status != 'cancelled') {
            // Update the status to 'cancelled'
            $update_sql = "UPDATE employee_bank_details 
                           SET status = 'cancelled' 
                           WHERE emp_id = '$emp_id' AND token = '$token'";
            if (mysqli_query($conn, $update_sql)) {
                // Redirect back with success message
                $_SESSION['message'] = "Bank details for Emp ID: {$emp_id} ({$first_name}) have been cancelled successfully.";
                header("Location: emp-bank.php");
                exit();
            } else {
                $_SESSION['error'] = "Error cancelling the bank details. Please try again.";
            }
        } else {
            $_SESSION['error'] = "The request for Emp ID: {$emp_id} ({$first_name}) has already been cancelled.";
        }
    } else {
        $_SESSION['error'] = "No employee found with the provided details.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to the previous page in case of errors
header("Location: emp-bank.php");
exit();
?>
