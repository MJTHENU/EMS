<?php

require_once('vendor/inc/connection.php');

// Getting id of the data from URL
$id = $_GET['emp_id'];
$token = $_GET['token'];

// Updating the status to 'Cancelled' in the database
$result = mysqli_query($conn, "UPDATE `employee_bank_details` SET `status`='Cancelled' WHERE `emp_id`='$id' and `token` = '$token'");

// Redirecting to the display page (emp-bank.php in our case)
header("Location: emp-bank.php");
?>
