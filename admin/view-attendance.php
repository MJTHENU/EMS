<?php
session_start();
require_once('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$emp_id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');

// Adjusted SQL query to select "status" from the "attendance" table
$sql = "SELECT attendance.*, employee.first_name 
        FROM attendance
        JOIN employee ON attendance.emp_id = employee.emp_id
        WHERE attendance.emp_id = '$emp_id'";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/atten.css"> 
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <h2 class="h2">Attendance Details</h2>
    <div class="contain">
        <table>
            <tr>
                <th align="center">Emp. ID</th>
                <th align="center">Employee Name</th>
                <th align="center">Attendance Date</th>
                <th align="center">Log In</th>
                <th align="center">Log Out</th>
                <th align="center">Total Hours</th>
                <th align="center">Status</th>
            </tr>

            <?php
            while ($employee = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$employee['emp_id']."</td>";              
                echo "<td>".$employee['first_name']."</td>";           
                echo "<td>".$employee['att_date']."</td>";
                echo "<td>".$employee['check_in']."</td>";
                echo "<td>".$employee['check_out']."</td>";
                echo "<td>".$employee['total_hours']."</td>";
                echo "<td>".$employee['status']."</td>";
                echo "</tr>";
            }
            ?>

        </table>
    </div>
</body>
</html>
