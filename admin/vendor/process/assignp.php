<?php

require_once ('../inc/connection.php');

$pname = $_POST['project_name'];
$eid = $_POST['emp_id'];
$subdate = $_POST['due_date'];

$sql = "INSERT INTO `project`(`emp_id`, `project_name`, `due_date` , `status`) VALUES ('$eid' , '$pname' , '$subdate' , 'Due')";

$result = mysqli_query($conn, $sql);


if(($result) == 1){
    
    
    header("Location: ../../assignproject.php");
}

else{
    echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Failed to Assign')
    window.location.href='javascript:history.go(-1)';
    </SCRIPT>");
}




?>