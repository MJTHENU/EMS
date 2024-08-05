<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$sql = "SELECT employee.emp_id, employee.first_name, employee.last_name, employee_bank_details.bank_holder_name, employee_bank_details.bank_name, employee_bank_details.acc_no, employee_bank_details.ifsc_code, employee_bank_details.branch_name, employee_bank_details.token, employee_bank_details.status FROM employee INNER JOIN employee_bank_details ON employee.emp_id = employee_bank_details.emp_id";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/atten.css">
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>

<div id="divimg">
    <table>
        <tr>
            <th>Emp. ID</th>
            <th>Name</th>
            <th>Account Holder Name</th>
            <th>Bank Name</th>
            <th>Account Number</th>
            <th>IFSC Code</th>
            <th>Branch</th>
            <th>Token</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        while ($employee = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$employee['emp_id']."</td>";
            echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
            echo "<td>".$employee['bank_holder_name']."</td>";
            echo "<td>".$employee['bank_name']."</td>";
            echo "<td>".$employee['acc_no']."</td>";
            echo "<td>".$employee['ifsc_code']."</td>";
            echo "<td>".$employee['branch_name']."</td>";
            echo "<td>".$employee['token']."</td>";
            echo "<td>".$employee['status']."</td>";
            echo "<td><a class='approve' href=\"bank-approve.php?emp_id={$employee['emp_id']}&token={$employee['token']}\"  onClick=\"return confirm('Are you sure you want to Approve the request?')\">Approve</a> | <a class='cancel' href=\"bank_cancel.php?emp_id={$employee['emp_id']}&token={$employee['token']}\" onClick=\"return confirm('Are you sure you want to Cancel the request?')\">Cancel</a></td>";
            echo "</tr>";
        }
        ?>

    </table>
</div>
</body>
</html>
