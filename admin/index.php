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

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Use session employee ID if GET parameter is not set or empty
$id = isset($_GET['a_id']) && !empty($_GET['a_id']) ? mysqli_real_escape_string($conn, $_GET['a_id']) : $_SESSION['a_id'];

// Query to get employee information
$sql1 = "SELECT * FROM `admin` WHERE a_id = '$id'";
$result1 = mysqli_query($conn, $sql1);

if (mysqli_num_rows($result1) > 0) {
    $employeen = mysqli_fetch_array($result1);
    $empName = $employeen['first_name'];
} else {
    echo "No employee found with the given ID.";
    exit();
}

// Updated query to get both present and absent employees for type 2 (Employee) and type 3 (Trainee)
$attendance_sql = "
    SELECT employee.emp_id, employee.first_name, employee.type, attendance.check_in, attendance.status
    FROM employee
    LEFT JOIN attendance ON employee.emp_id = attendance.emp_id AND attendance.att_date = CURDATE()
    WHERE employee.type IN (2, 3)
    ORDER BY employee.type, attendance.check_in DESC, employee.first_name";

// Execute the query
$attendance_result = mysqli_query($conn, $attendance_sql);

// Initialize arrays to separate employees and trainees
$employees = [];
$trainees = [];

// Categorize the data into employees and trainees
while ($row = mysqli_fetch_assoc($attendance_result)) {
    if ($row['type'] == 2) {
        $employees[] = $row;
    } elseif ($row['type'] == 3) {
        $trainees[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php') ?>

<link rel="stylesheet" href="vendor/css/index.css">
    <body>
        <?php include('vendor/inc/nav.php') ?>
        
        <!-- Center-aligned header section -->
    <div class="header-section">
        <h2>Today's Attendance Report</h2>
        <p class="date-display"><?php echo date('l, F j, Y'); ?></p>
    </div>

        <div class="main">
            <div class="left">
                <h2>Trainee </h2>

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
                        foreach ($trainees as $row) {
                            if ($row['status'] == 'present') {
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
                        foreach ($trainees as $row) {
                            if ($row['status'] != 'present' || is_null($row['status'])) {
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

            <div class="right">
                <h2>Employee </h2>

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
                        foreach ($employees as $row) {
                            if ($row['status'] == 'present') {
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
                        foreach ($employees as $row) {
                            if ($row['status'] != 'present' || is_null($row['status'])) {
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
