<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$pname = $duedate = $subdate = $firstName = $lastName = $mark = $points = $base = $bonus = $total = "";

// Retrieve IDs from GET request
$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';

// Query to retrieve project details
$sql1 = "SELECT project.project_id, project.emp_id, project.project_name, project.due_date, project.sub_date, project.mark, rank.points, employee.first_name, employee.last_name, salary.salary, salary.bonus, salary.total
         FROM project
         JOIN rank ON project.emp_id = rank.emp_id
         JOIN employee ON project.emp_id = employee.emp_id
         JOIN salary ON project.emp_id = salary.emp_id
         WHERE project.emp_id = ? AND project.project_id = ?";

$stmt1 = mysqli_prepare($conn, $sql1);
mysqli_stmt_bind_param($stmt1, "si", $id, $pid);
mysqli_stmt_execute($stmt1);

$result1 = mysqli_stmt_get_result($stmt1);

// Check if the query was successful and data is retrieved
if ($result1 && mysqli_num_rows($result1) > 0) {
    $res = mysqli_fetch_assoc($result1);
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
} else {
    echo "<p>No data found for the given employee and project.</p>";
    exit();
}

if (isset($_POST['update'])) {
    $eid = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $pid = mysqli_real_escape_string($conn, $_POST['project_id']);
    $mark = mysqli_real_escape_string($conn, $_POST['mark']);
    $points = mysqli_real_escape_string($conn, $_POST['points']);
    $base = mysqli_real_escape_string($conn, $_POST['salary']);
    $bonus = mysqli_real_escape_string($conn, $_POST['bonus']);
    $total = mysqli_real_escape_string($conn, $_POST['total']);

    $upPoint = $points + $mark;
    $upBonus = $bonus + $mark;
    $upSalary = $base + ($upBonus * $base) / 100; 

    $updateProject = mysqli_query($conn, "UPDATE `project` SET `mark`='$mark' WHERE emp_id='$eid' AND project_id='$pid'");
    $updateRank = mysqli_query($conn, "UPDATE `rank` SET `points`='$upPoint' WHERE emp_id='$eid'");
    $updateSalary = mysqli_query($conn, "UPDATE `salary` SET `bonus`='$upBonus', `total`='$upSalary' WHERE emp_id='$eid'");

    // Redirect after successful update
    echo "<SCRIPT LANGUAGE='JavaScript'>
        window.location.href='assignproject.php';
    </SCRIPT>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <?php include('vendor/inc/head.php'); ?>
   <link rel="stylesheet" href="vendor/css/emp-edit.css">
</head>
<body>
   <?php include('vendor/inc/nav.php'); ?>

   <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">Project Mark</h2>
                    <form id="registration" action="mark.php" method="POST">
                        <div class="input-group">
                          <p>Project Name</p>
                            <input class="input--style-1" type="text" name="project_name" value="<?php echo htmlspecialchars($pname); ?>" readonly>
                        </div>
                        <div class="input-group">
                          <p>Employee Name</p>
                            <input class="input--style-1" type="text" name="first_name" value="<?php echo htmlspecialchars($firstName . ' ' . $lastName); ?>" readonly>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Due Date</p>
                                     <input class="input--style-1" type="text" name="due_date" value="<?php echo htmlspecialchars($duedate); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Submission Date</p>
                                    <input class="input--style-1" type="text" name="sub_date" value="<?php echo htmlspecialchars($subdate); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                          <p>Assign Mark</p>
                            <input class="input--style-1" type="text" name="mark" value="<?php echo htmlspecialchars($mark); ?>" required>
                        </div>
                        <input type="hidden" name="emp_id" value="<?php echo htmlspecialchars($id); ?>" required>
                        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($pid); ?>" required>
                        <input type="hidden" name="points" value="<?php echo htmlspecialchars($points); ?>" required>
                        <input type="hidden" name="salary" value="<?php echo htmlspecialchars($base); ?>" required>
                        <input type="hidden" name="bonus" value="<?php echo htmlspecialchars($bonus); ?>" required>
                        <input type="hidden" name="total" value="<?php echo htmlspecialchars($total); ?>" required>
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
