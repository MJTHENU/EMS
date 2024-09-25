<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the correct time zone
date_default_timezone_set('Asia/Kolkata'); // Change this to your correct time zone

// Include the database connection
require_once('vendor/inc/connection.php'); 

// Set MySQL time zone for the session
mysqli_query($conn, "SET time_zone = '+05:30'"); // Set this to your correct time zone

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

// Use session employee ID if GET parameter is not set or empty
$id = isset($_GET['emp_id']) && !empty($_GET['emp_id']) ? mysqli_real_escape_string($conn, $_GET['emp_id']) : $_SESSION['emp_id'];

// Query to get employee information
$sql1 = "SELECT * FROM `employee` WHERE emp_id = '$id'";
$result1 = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result1) > 0) {
    $employeen = mysqli_fetch_array($result1);
    $empName = $employeen['first_name'];
} else {
    echo "No employee found with the given ID.";
    // Optionally redirect or handle the error
    exit();
}

// Updated query to get both present and absent employees excluding inactive status
$attendance_sql = "
    SELECT employee.emp_id, employee.first_name, employee.status AS emp_status, attendance.check_in, attendance.status AS att_status
    FROM employee
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id AND attendance.att_date = CURDATE()
    WHERE employee.type = 2 AND employee.status != 'inactive'
    ORDER BY attendance.check_in DESC, employee.first_name";

// Execute the query
$attendance_result = mysqli_query($conn, $attendance_sql);
// Fetch ongoing projects
// $project_sql = "
//     SELECT project.project_id, project.p_name, project.p_lead, project.`desc`, project.start_date, project.end_date, project.status, project.priority
//     FROM `project`
//     JOIN `employee` ON project.p_lead = employee.emp_id
//     WHERE project.`status` = 'In Progress' AND employee.emp_id = '$id'
// ";
$project_sql = "
    SELECT project.project_id, project.p_name, 
           CONCAT(employee.emp_id, ' - ', employee.first_name) AS p_lead, 
           project.`desc`, project.start_date, project.end_date, 
           project.status, project.priority
    FROM `project`
    JOIN `employee` ON project.p_lead = employee.emp_id
    WHERE project.`status` = 'In Progress' AND employee.emp_id = '$id'
";




$project_result = mysqli_query($conn, $project_sql);
?>

<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php') ?>
<link rel ="stylesheet" href = "vendor/css/index.css?v=1.0">
   
    <body>
        <?php include('vendor/inc/nav.php') ?>

        <div class="main">
        <div class="left">
        <h2>Project Status</h2>
        <div class="table-wrapper">
            <?php
                if (mysqli_num_rows($project_result) > 0) {
                    echo "<table>
                            <thead>
                                <tr>
                                    <th>Project ID</th>
                                    <th>Project Name</th>
                                    <th>Project Lead</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($project = mysqli_fetch_assoc($project_result)) {
                        echo "<tr>
                                <td>{$project['project_id']}</td>
                                <td>{$project['p_name']}</td>
                                <td>{$project['p_lead']}</td>
                                <td>{$project['desc']}</td>
                                <td>{$project['start_date']}</td>
                                <td>{$project['end_date']}</td>
                                <td>{$project['status']}</td>
                                <td>{$project['priority']}</td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No ongoing projects found.</p>";
                }
            ?>
        </div>
    </div>


            <div class="right">
                <h2>Today's Attendance Report</h2>

                <p class="date-display"><?php echo date('l, F j, Y'); ?></p>

                <h4>Present</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Login Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop to display present employees
                        while ($row = mysqli_fetch_assoc($attendance_result)) {
                            if ($row['att_status'] == 'present') {
                                echo "<tr>
                                        <td>{$row['first_name']}</td>
                                        <td>{$row['check_in']}</td>
                                        <td><span class='status-icon online'></span></td>
                                      </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <h4>Absent</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset pointer and loop to display absent employees
                        mysqli_data_seek($attendance_result, 0);
                        while ($row = mysqli_fetch_assoc($attendance_result)) {
                            if (($row['att_status'] != 'present' || $row['att_status'] === null)) {
                                echo "<tr>
                                        <td>{$row['first_name']}</td>
                                        <td><span class='status-icon offline'></span></td>
                                      </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
