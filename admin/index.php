<?php 
session_start();
require_once ('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$sql = "SELECT employee.emp_id, employee.first_name, employee.last_name, rank.points 
        FROM employee, rank 
        WHERE rank.emp_id = employee.emp_id 
        ORDER BY employee.emp_id ASC, rank.points DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body>
    
<?php include('vendor/inc/nav.php'); ?>
<div class="divider"></div>
    <div id="divimg">
        <h2 class = "h2">Employee Leaderboard </h2>
        <table>
            <tr>
                <th align = "center">Seq.</th>
                <th align = "center">Emp. ID</th>
                <th align = "center">Name</th>
                <th align = "center">Points</th>
            </tr>

            <?php
                $seq = 1;
                while ($employee = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$seq."</td>";
                    echo "<td>".$employee['emp_id']."</td>";
                    echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
                    echo "<td>".$employee['points']."</td>";
                    $seq += 1;
                }
            ?>
        </table>

        <div class="p-t-20">
            <button class="btn btn--radius btn--green" type="submit" style="float: right; margin-right: 60px">
                <a href="reset.php" style="text-decoration: none; color: white"> Reset Points</a>
            </button>
        </div>
    </div>

</body>
</html>
