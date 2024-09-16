<?php

// $servername = "localhost";
// $dBUsername = "kite-emp";
// $dbPassword = "(Oy(zrM&S[us";
// $dBName = "kite-emp";

$servername = "localhost";
$dBUsername = "root";
$dbPassword = "";
$dBName = "kite_emp";

$conn = mysqli_connect($servername, $dBUsername, $dbPassword, $dBName);

if(!$conn){
	echo "Databese Connection Failed";
}

?>
