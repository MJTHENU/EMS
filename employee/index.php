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
?>

<!DOCTYPE html>
<html>
<?php include('vendor/inc/head.php') ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
        }

        .main {
            display: flex;
            margin: 20px;
        }

        .left {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
            border-right: 1px solid #ddd;
        }

        .right {
            flex: 2;
            padding: 20px;
            background-color: lightblue;
        }
        /* Center-align the date display */
        .date-display {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            font-size: 22px;
            background-color: #0223f7;
            color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        h2, h4 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: black;
            font-weight: bold;
        }

        .status-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
        }

        .online {
            background-color: green;
        }

        .offline {
            background-color: red;
        }

        /* Animation styles */
        tbody tr {
            transition: transform 0.3s, background-color 0.3s;
        }

        tbody tr:hover {
            transform: scale(1.02);
            background-color: #f0f8ff;
        }
    </style>
    <body>
        <?php include('vendor/inc/nav.php') ?>

        <div class="main">
            <div class="left">
                <!-- Left side content goes here -->
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
