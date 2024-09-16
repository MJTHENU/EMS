<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require_once('vendor/inc/connection.php'); 

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Use session employee ID if GET parameter is not set or empty
$id = isset($_GET['a_id']) && !empty($_GET['a_id']) ? mysqli_real_escape_string($conn, $_GET['a_id']) : $_SESSION['a_id'];

// Query to get employee information
$sql1 = "SELECT * FROM admin WHERE a_id = '$id'";
$result1 = mysqli_query($conn, $sql1);

// Debugging: Check query execution
if (!$result1) {
    die('Query Error: ' . mysqli_error($conn));
}

// Check if data is fetched
if (mysqli_num_rows($result1) > 0) {
    $employeen = mysqli_fetch_array($result1);
    $empName = $employeen['first_name'];
} else {
    echo "No employee found with the given ID.";
    exit();
}

// Fetch "Present" and "Absent" lists for trainee (type 3)
$attendance_sql_left_present = "
    SELECT employee.emp_id, employee.first_name, attendance.check_in, attendance.status
    FROM employee 
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id
    WHERE employee.type = 3 
    AND attendance.status = 'present' 
    AND attendance.att_date = CURDATE()
    ORDER BY attendance.check_in DESC, employee.first_name";

$attendance_sql_left_absent = "
    SELECT employee.emp_id, employee.first_name
    FROM employee 
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id AND attendance.att_date = CURDATE()
    WHERE employee.type = 3 
    AND (attendance.status IS NULL OR attendance.status != 'present')
    AND employee.status = 'active'
    ORDER BY employee.first_name";

$attendance_result_left_present = mysqli_query($conn, $attendance_sql_left_present);
$attendance_result_left_absent = mysqli_query($conn, $attendance_sql_left_absent);

// Fetch "Present" and "Absent" lists for employee (type 2)
$attendance_sql_right_present = "
    SELECT employee.emp_id, employee.first_name, attendance.check_in, attendance.status
    FROM employee 
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id
    WHERE employee.type = 2 
    AND attendance.status = 'present' 
    AND attendance.att_date = CURDATE()
    ORDER BY attendance.check_in DESC, employee.first_name";

$attendance_sql_right_absent = "
    SELECT employee.emp_id, employee.first_name
    FROM employee 
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id AND attendance.att_date = CURDATE()
    WHERE employee.type = 2 
    AND (attendance.status IS NULL OR attendance.status != 'present')
    AND employee.status = 'active'
    ORDER BY employee.first_name";

$attendance_result_right_present = mysqli_query($conn, $attendance_sql_right_present);
$attendance_result_right_absent = mysqli_query($conn, $attendance_sql_right_absent);

// Debugging: Check query execution for right (employee type 2)
if (!$attendance_result_right_present || !$attendance_result_right_absent) {
    die('Query Error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php') ?>
<link rel="stylesheet" href="vendor/css/index.css">
<body>
    <?php include('vendor/inc/nav.php') ?>

    <div class="main">
        <div class="left">
            <h2>Today's Trainee Attendance Report</h2>

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
                    while ($row = mysqli_fetch_assoc($attendance_result_left_present)) {
                        echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td>{$row['check_in']}</td>
                                <td><span class='status-icon online'></span></td>
                              </tr>";
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
                    while ($row = mysqli_fetch_assoc($attendance_result_left_absent)) {
                        echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td><span class='status-icon offline'></span></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="right">
            <h2>Today's Employee Attendance Report</h2>

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
                    while ($row = mysqli_fetch_assoc($attendance_result_right_present)) {
                        echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td>{$row['check_in']}</td>
                                <td><span class='status-icon online'></span></td>
                              </tr>";
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
                    while ($row = mysqli_fetch_assoc($attendance_result_right_absent)) {
                        echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td><span class='status-icon offline'></span></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>