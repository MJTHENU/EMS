<?php 
session_start();
 include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}
$sql = "SELECT * from `project` order by sub_date desc";
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
				while ($employee = mysqli_fetch_assoc($result)) {
					echo "<tr>";
					echo "<td>".$employee['project_id']."</td>";
					echo "<td>".$employee['emp_id']."</td>";
					echo "<td>".$employee['project_name']."</td>";
					echo "<td>".$employee['due_date']."</td>";
					echo "<td>".$employee['sub_date']."</td>";
					echo "<td>".$employee['mark']."</td>";
					echo "<td>".$employee['status']."</td>";
					echo "<td><a href=\"mark.php?emp_id=$employee[emp_id]&pid=$employee[project_id]\">Mark</a>"; 

				}


			?>

		</table>
		
	
    
</body>
</html>