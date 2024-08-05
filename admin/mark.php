<?php 
session_start();
 include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}
?>
<?php

$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';

// Query to retrieve project information
$sql = "SELECT project_id, project.emp_id, project_name, due_date, sub_date, mark, points, first_name, last_name, salary, bonus, total
        FROM `project`, `rank`, `employee`, `salary`
        WHERE project.emp_id = ? AND project_id = ? AND project.emp_id = rank.emp_id AND salary.emp_id = project.emp_id AND employee.emp_id = project.emp_id AND employee.emp_id = rank.emp_id";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $id, $pid);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    // Handle the error
    echo "Error: " . mysqli_error($conn);
} else {
    // Process the result set
}
if(isset($_POST['update']))
{

  $eid = mysqli_real_escape_string($conn, $_POST['emp_id']);
  $pid = mysqli_real_escape_string($conn, $_POST['project_id']);
  

  $mark = mysqli_real_escape_string($conn, $_POST['mark']);
  $points = mysqli_real_escape_string($conn, $_POST['points']);
  $base = mysqli_real_escape_string($conn, $_POST['salary']);
  $bonus = mysqli_real_escape_string($conn, $_POST['bonus']);
  $total = mysqli_real_escape_string($conn, $_POST['total']);

  $upPoint = $points+$mark;
  
  $upBonus = $bonus +  $mark;
  $upSalary = $base + ($upBonus*$base)/100; 
  echo "$upBonus";
  echo "string";
  echo "$upSalary";
 
 $result = mysqli_query($conn, "UPDATE `project` SET `mark`='$mark' WHERE emp_id='$eid' and project_id = $pid");

 $result = mysqli_query($conn, "UPDATE `rank` SET `points`='$upPoint' WHERE emp_id='$eid'");
 $result = mysqli_query($conn, "UPDATE `salary` SET `bonus`='$upBonus' ,`total`='$upSalary' WHERE emp_id='$eid'");




 echo ("<SCRIPT LANGUAGE='JavaScript'>
   
    window.location.href='assignproject.php  ';
    </SCRIPT>");

  
}
?>

<?php
// Query to retrieve project details
$sql1 = "SELECT project_id, project.emp_id, project.project_name, project.due_date, project.sub_date, project.mark, rank.points, employee.first_name, employee.last_name, salary.salary, salary.bonus, salary.total
         FROM `project`, `rank`, `employee`, `salary`
         WHERE project.emp_id = ? AND project.project_id = ? AND project.emp_id = rank.emp_id AND salary.emp_id = project.emp_id AND employee.emp_id = project.emp_id AND employee.emp_id = rank.emp_id";

$stmt1 = mysqli_prepare($conn, $sql1);
mysqli_stmt_bind_param($stmt1, "si", $id, $pid);
mysqli_stmt_execute($stmt1);

$result1 = mysqli_stmt_get_result($stmt1);

if (!$result1) {
    // Handle the error
    echo "Error: " . mysqli_error($conn);
} else {
    // Process the result set
    while ($res = mysqli_fetch_assoc($result1)) {
        $pname = $res['project_name'];
        $duedate = $res['due_date'];
        $subdate = $res['sub_date'];
        $firstName = $res['first_name'];
        $lastName = $res['last_name'];
        $mark = $res['mark'];
        $points = $res['points'];
        $base = $res['salary'];
        $bonus = $res['bonus'];
        $total = $res['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <?php  include('vendor/inc/head.php');  ?>
   <link rel = "stylesheet" href = "vendor/css/emp-edit.css">
</head>
<body>
   <?php  include('vendor/inc/nav.php');  ?>

   <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">Project Mark</h2>
                    <form id = "registration" action="mark.php" method="POST">



                        <div class="input-group">
                          <p>Project Name</p>
                            <input class="input--style-1" type="text"  name="project_name" value="<?php echo $pname;?>" readonly>
                        </div>
                       
                        
                        <div class="input-group">
                          <p>Employee Name</p>
                            <input class="input--style-1" type="text" name="first_name" value="<?php echo $firstName;?> <?php echo $lastName;?>" readonly>
                        </div>

                       


                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Due Date</p>
                                     <input class="input--style-1" type="text" name="due_date" value="<?php echo $duedate;?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Submission Date</p>
                                    <input class="input--style-1" type="text"  name="sub_date" value="<?php echo $subdate;?>" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="input-group">
                          <p>Assign Mark</p>
                            <input class="input--style-1" type="text"  name="mark" value="<?php echo $mark;?>" required>
                        </div>

                       
                        <input type="hidden" name="emp_id" id="textField" value="<?php echo $id;?>" required="required">
                        <input type="hidden" name="project_id" id="textField" value="<?php echo $pid;?>" required="required">
                         <input type="hidden" name="points" id="textField" value="<?php echo $points;?>" required="required">
                        <input type="hidden" name="salary" id="textField" value="<?php echo $base;?>" required="required">
                        <input type="hidden" name="bonus" id="textField" value="<?php echo $bonus;?>" required="required">
                        <input type="hidden" name="total" id="textField" value="<?php echo $total;?>" required="required">
                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Assign Mark</button>
                        </div>
                        
                    </form>
                    <br>
                    
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>