<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['emp_id']) && isset($_GET['token'])) {
    $emp_id = mysqli_real_escape_string($conn, $_GET['emp_id']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Update bank details status
    $update_query = "UPDATE employee_bank_details SET status = 'Approved' WHERE emp_id = ? AND token = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'ss', $emp_id, $token);

    if (mysqli_stmt_execute($stmt)) {
        // Update employee table to reflect that account has been approved
        $update_emp_query = "UPDATE employee SET account_approved = 1 WHERE emp_id = ?";
        $stmt_emp = mysqli_prepare($conn, $update_emp_query);
        mysqli_stmt_bind_param($stmt_emp, 's', $emp_id);
        mysqli_stmt_execute($stmt_emp);

        header("Location: emp-bank.php");
    } else {
        echo "Error approving bank request: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
