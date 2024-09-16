<?php
session_start();
session_destroy();
// echo '<script>alert("Logout Success");window.location.assign("../../a-login.php");</script>';
echo '<script>alert("Logout Success");window.location.assign("../../../index.php");</script>';
?>