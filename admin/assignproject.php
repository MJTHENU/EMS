<?php 
session_start();
 include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}
$sql = "SELECT * from `project` order by end_date desc";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
	<link rel="stylesheet" href="vendor/css/project.css">
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <h2 class = "h2">Assign Project</h2>
    <table>
			<tr>
				<th align="center">S No</th>
				<th align = "center">Project ID</th>
				<th align = "center">Emp. ID</th>
				<th align = "center">Project Name</th>
				<th align = "center">Due Date</th>
				<th align = "center">Submission Date</th>
				<th align = "center">Mark</th>
				<th align = "center">Status</th>
				<th align = "center">Option</th>
				
			</tr>

			<?php
				$counter = 1; // Initialize the counter to 1
			    // Fetch all the rows from the table and display them in the table
				while ($employee = mysqli_fetch_assoc($result)) {
					echo "<tr>";
					echo "<td>".$counter."</td>"; // Display the counter in the first column for each row
					echo "<td>".$employee['project_id']."</td>";
					echo "<td>".$employee['emp_id']."</td>";
					echo "<td>".$employee['p_name']."</td>";
					echo "<td>".$employee['start_date']."</td>";
					echo "<td>".$employee['end_date']."</td>";
					echo "<td>".$employee['mark']."</td>";
					echo "<td>".$employee['status']."</td>";
					echo "<td><a href=\"mark.php?emp_id=$employee[emp_id]&pid=$employee[project_id]\">Mark</a>"; 
					$counter++; // Increment the counter after each row
				}


			?>

		</table>
		
	
    
</body>
</html>