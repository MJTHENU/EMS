<?php
 session_start();

 if (!isset($_SESSION['emp_id'])) {
     header("Location: emp-login.php");
     exit();
 }

 $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
 require_once ('vendor/inc/connection.php');
 $sql = "SELECT * FROM `employee` where emp_id = '$id'";
 $result = mysqli_query($conn, $sql);
 $employee = mysqli_fetch_array($result);
 $empName = ($employee['first_name']);
 //echo "$id";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel ="stylesheet" href = "vendor/css/leave.css">
</head>
<body>
<?php include('vendor/inc/nav.php') ?>

<div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
            <div class="card-img">
                    <img src="vendor/images/leave.jpg" >
                </div>
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2">Apply Leave Form</h2>
                    <form action="vendor/process/leave-process.php?emp_id=<?php echo $id?>" method="POST">


                        <div class="input-group">
                            <input class="input--style-1" type="text" placeholder="Reason" name="reason">
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                            	<p>Start Date</p>
                                <div class="input-group">
                                    <input class="input--style-1" type="date" placeholder="start_date" name="start_date">
                                   
                                </div>
                            </div>
                            <div class="col-2">
                            	<p>End Date</p>
                                <div class="input-group">
                                    <input class="input--style-1" type="date" placeholder="end_date" name="end_date">
                                   
                                </div>
                            </div>
                        </div>
                        



                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
		

    <div class="contain">
        <h2 class="h2">Leave Details </h2>
    <table>
			<tr>
				<th align = "center">Emp. ID</th>
				<th align = "center">Name</th>
				<th align = "center">Start Date</th>
				<th align = "center">End Date</th>
				<th align = "center">Total Days</th>
				<th align = "center">Reason</th>
				<th align = "center">Status</th>
			</tr>


			<?php


				$sql = "Select employee.emp_id, employee.first_name, employee.last_name, employee_leave.start_date, employee_leave.end_date, employee_leave.reason, employee_leave.status From employee, employee_leave Where employee.emp_id = '$id' and employee_leave.emp_id = '$id' order by employee_leave.token";
				$result = mysqli_query($conn, $sql);
				while ($employee = mysqli_fetch_assoc($result)) {
					$date1 = new DateTime($employee['start_date']);
					$date2 = new DateTime($employee['end_date']);
					$interval = $date1->diff($date2);
					$interval = $date1->diff($date2);

					echo "<tr>";
					echo "<td>".$employee['emp_id']."</td>";
					echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
					
					echo "<td>".$employee['start_date']."</td>";
					echo "<td>".$employee['end_date']."</td>";
					echo "<td>".$interval->days."</td>";
					echo "<td>".$employee['reason']."</td>";
					echo "<td>".$employee['status']."</td>";
					
				}


			?>


		</table>
            </div>

</body>
</html>