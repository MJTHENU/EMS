<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['emp_id']) && isset($_GET['token'])) {
    $emp_id = $_GET['emp_id'];
    $token = $_GET['token'];
    
    // Assuming you have a table called employee_bank_details to store bank details
    $update_query = "UPDATE employee_bank_details SET status = 'Approved' WHERE emp_id = '$emp_id' AND token = '$token'";
    if(mysqli_query($conn, $update_query)) {
       // echo "Bank request approved successfully.";
        header("Location:emp-bank.php");
    } else {
        echo "Error approving bank request: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
