<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$sql = "Select employee.emp_id, employee.first_name, employee.last_name, employee_leave.start_date, employee_leave.end_date, employee_leave.reason, employee_leave.status, employee_leave.token From employee, employee_leave Where employee.emp_id = employee_leave.emp_id order by employee_leave.token";

//echo "$sql";
$result = mysqli_query($conn, $sql);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <?php  include('vendor/inc/head.php') ?>
	<link rel="stylesheet" href="vendor/css/atten.css">
</head>
<body>
    
<?php  include('vendor/inc/nav.php') ?>

<div id="divimg">
	<h2 class = "h2">Employee Leave</h2>
		<table>
			<tr>
				<th>S No</th>
				<th>Emp. ID</th>
				<th>Token</th>
				<th>Name</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Total Days</th>
				<th>Reason</th>
				<th>Status</th>
				<th>Options</th>
			</tr>

			<?php
			    $sno = 1;  // initialize serial number
				while ($employee = mysqli_fetch_assoc($result)) {

				$date1 = new DateTime($employee['start_date']);
				$date2 = new DateTime($employee['end_date']);
				$interval = $date1->diff($date2);
				$interval = $date1->diff($date2);
				//echo "difference " . $interval->days . " days ";

					echo "<tr>";
					echo "<td>".$sno++."</td>";  // increment serial number and display it
					echo "<td>".$employee['emp_id']."</td>";
					echo "<td>".$employee['token']."</td>";
					echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
					
					echo "<td>".$employee['start_date']."</td>";
					echo "<td>".$employee['end_date']."</td>";
					echo "<td>".$interval->days."</td>";
					echo "<td>".$employee['reason']."</td>";
					echo "<td>".$employee['status']."</td>";
					echo "<td><a class='approve' href=\"approve.php?emp_id=$employee[emp_id]&token=$employee[token]\"  onClick=\"return confirm('Are you sure you want to Approve the request?')\">Approve</a> | <a class='cancel' href=\"cancel.php?emp_id=$employee[emp_id]&token=$employee[token]\" onClick=\"return confirm('Are you sure you want to Canel the request?')\">Cancel</a></td>";
					$sno++; // Increment the counter after each row

				}


			?>

		</table>
		
	</div>
</body>
</html>