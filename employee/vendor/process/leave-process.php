<?php
//including the database connection file
require_once ('../inc/connection.php');

//getting id of the data from url
$id = $_GET['emp_id'];
//echo $id;
$reason = $_POST['reason'];

$start = $_POST['start_date'];
//echo "$reason";
$end = $_POST['end_date'];

$sql = "INSERT INTO `employee_leave`(`emp_id`,`token`, `start_date`, `end_date`, `reason`, `status`) VALUES ('$id','','$start','$end','$reason','Pending')";

$result = mysqli_query($conn, $sql);

//redirecting to the display page (index.php in our case)
header("Location:../../index.php?emp_id=$id");
?>

