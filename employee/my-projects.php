<?php
 session_start();

 if (!isset($_SESSION['emp_id'])) {
     header("Location: emp-login.php");
     exit();
 }

 $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
 include('vendor/inc/connection.php');
	$sql = "SELECT * FROM `project` where emp_id = '$id'";
	$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
	<link rel ="stylesheet" href="vendor/css/project.css">
</head>
<body>
    <?php include('vendor/inc/nav.php') ?>
<div class="contain">
<h2 class="h2">My Projects</h2>

    <table>
			<tr>

				<th align = "center">Project ID</th>
				<th align = "center">Project Name</th>
				<th align = "center">Due Date</th>
				<th align = "center">Sub Date</th>
				<th align = "center">Mark</th>
				<th align = "center">Status</th>
				<th align = "center">Option</th>
			</tr>

			<?php
				while ($employee = mysqli_fetch_assoc($result)) {

					echo "<tr>";
					echo "<td>".$employee['project_id']."</td>";
					echo "<td>".$employee['project_name']."</td>";
					echo "<td>".$employee['due_date']."</td>";
					echo "<td>".$employee['sub_date']."</td>";
					echo "<td>".$employee['mark']."</td>";
					echo "<td>".$employee['status']."</td>";
					

					 echo "<td><a class = 'submit' href=\"project-submit.php?project_id=$employee[project_id]&emp_id=$employee[emp_id]\">Submit</a>";

				}


			?>

		</table>

    
</body>
</html>