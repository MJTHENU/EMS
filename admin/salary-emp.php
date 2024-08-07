<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}


$sql = "SELECT employee.emp_id,employee.first_name,employee.last_name,salary.salary,salary.bonus,salary.total from employee,`salary` where employee.emp_id=salary.emp_id";

//echo "$sql";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
	<link rel ="stylesheet" href="vendor/css/salary.css">
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>
<h2 class="h2">Salary Details</h2>

<button class="add-emp"><a href="emp-bank.php">View Bank Details</a></button>
<div class="contain">
<table>
    <tr>
        <th align="center">Emp. ID</th>
        <th align="center">Name</th>
        <th align="center">Base Salary</th>
        <th align="center">Bonus</th>
        <th align="center">Total Salary</th>
        <th align="center">Options</th>
    </tr>
    
    <?php
    while ($employee = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($employee['emp_id']) . "</td>";
        echo "<td>" . htmlspecialchars($employee['first_name']) . " " . htmlspecialchars($employee['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($employee['salary']) . "</td>";
        echo "<td>" . htmlspecialchars($employee['bonus']) . " %</td>";
        echo "<td>" . htmlspecialchars($employee['total']) . "</td>";
        echo "<td>
				<a class='edit' href=\"salary_edit.php?emp_id=".$employee['emp_id']."\">Edit</a> <br> <br>
                <a class='delete' href=\"salary_delete.php?emp_id=".$employee['emp_id']."\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a>
			</td>";
        echo "</tr>";
    }
    ?>
</table>

</div>
</body>
</html>