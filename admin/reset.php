<?php 
session_start();
 include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$sql1 = "UPDATE rank SET points = 0";
$sql2 = "UPDATE `salary` SET `total` = `salary` ,`bonus` = 0";

mysqli_query($conn , $sql1);
mysqli_query($conn , $sql2);

header("Location:index.php");
?>