<?php

// $servername = "localhost";
// $dBUsername = "emp";
// $dbPassword = "SIZl[)XXIf5B";
// $dBName = "employee";

$servername = "localhost";
$dBUsername = "root";
$dbPassword = "";
$dBName = "kite_emp";

$conn = mysqli_connect($servername, $dBUsername, $dbPassword, $dBName);

if(!$conn){
	echo "Databese Connection Failed";
}

?>

