<?php
//including the database connection file
include("vendor/inc/connection.php");

//getting id of the data from url
$id = $_GET['emp_id'];
$token = $_GET['token'];

//deleting the row from table
$result = mysqli_query($conn, "UPDATE `employee_leave` SET `status`='Approved' WHERE emp_id = '$id' AND token = $token;");

//redirecting to the display page (index.php in our case)
header("Location:emp-leave.php");
?>

