<?php
session_start();
require_once ('../../vendor/inc/connection.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * from `admin` WHERE email = '$email' AND password = '$password'";
$sqlid = "SELECT a_id from `admin` WHERE email = '$email' AND password = '$password'";

//echo "$sql";

$result = mysqli_query($conn, $sql);
$id = mysqli_query($conn , $sqlid);

$aid ="";

if(mysqli_num_rows($result) == 1){
	
    $employee = mysqli_fetch_array($id);
	$aid = ($employee['a_id']);
    $_SESSION['a_id'] = $aid;

	//echo ("logged in");
	header("Location: ../../index.php?a_id=$aid");
}

else{
	echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Invalid Email or Password')
    window.location.href='javascript:history.go(-1)';
    </SCRIPT>");
}
?>