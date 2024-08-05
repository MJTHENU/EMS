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
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>
<h2 class="h2">Salary Details</h2>

<button class="add-emp"><a href="emp-bank.php">View Bank Details</a></button>
<div class="contain">

<table>
			<tr>
				<th align = "center">Emp. ID</th>
				<th align = "center">Name</th>
				
				
				<th align = "center">Base Salary</th>
				<th align = "center">Bonus</th>
				<th align = "center">TotalSalary</th>
				
				
			</tr>
			
			<?php
				while ($employee = mysqli_fetch_assoc($result)) {
					echo "<tr>";
					echo "<td>".$employee['emp_id']."</td>";
					echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
					
					echo "<td>".$employee['salary']."</td>";
					echo "<td>".$employee['bonus']." %</td>";
					echo "<td>".$employee['total']."</td>";
					
					

				}


			?>
			
			</table>
</body>
</html>