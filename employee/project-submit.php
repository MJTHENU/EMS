<?php

require_once ('vendor/inc/connection.php');
//$id = (isset($_GET['id']) ? $_GET['id'] : '');
$pid = $_GET['project_id'];
$id = $_GET['emp_id'];
$date = date('Y-m-d');
//echo "$date";
$sql = "UPDATE `project` SET `sub_date`='$date',`status`='Submitted' WHERE project_id = '$pid';";
$result = mysqli_query($conn , $sql);
header("Location: my-projects.php?emp_id=$id");
?>