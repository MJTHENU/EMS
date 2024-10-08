<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your database connection file
require_once('vendor/inc/connection.php');

date_default_timezone_set('Asia/Kolkata');

$t_hours = "";
$error = [];

// Function to generate a list of holidays
function holidays() {
    $begin = new DateTime('2024-01-01');
    $end = new DateTime('2034-12-30');
    $end = $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($begin, $interval, $end);
    $holidayDates = [];

    foreach ($daterange as $date) {
        $dayOfWeek = date('w', strtotime($date->format("Y-m-d")));

        // Check for Sundays (day of the week is 0) or Fourth Saturdays
        if ($dayOfWeek == 0 || isFourthSaturday($date)) {
            $holidayDates[] = $date->format("Y-m-d");
        }
    }

    return $holidayDates;
}

// Function to check if a given date is the fourth Saturday
function isFourthSaturday($date) {
    $dayOfMonth = $date->format("j");
    $month = $date->format("n");
    $year = $date->format("Y");

    // Calculate the fourth Saturday of the month
    $fourthSaturday = date('j', strtotime("fourth saturday $year-$month"));

    // Check if the given date is the fourth Saturday
    return $dayOfMonth == $fourthSaturday;
}

$holidays = holidays(); 

// Function to check if the user has already submitted attendance for the current date
function checkExistingRecord($conn, $emp_id, $att_date) {
    $sql = "SELECT emp_id, att_date, status FROM attendance WHERE emp_id = ? AND att_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $att_date);
    $stmt->execute();
    $stmt->store_result(); // Store result set
    $stmt->bind_result($att_emp_id, $att_att_date, $att_status); // Bind result variables
    $stmt->fetch(); // Fetch the first row
    
    // Check if any row is fetched
    $rowCount = $stmt->num_rows;
    $stmt->close();

    return $rowCount > 0;
}

// Check if the user is logged in
if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

// Function to handle attendance recording
if (isset($_POST['submit'])) {
    $emp_id = $_SESSION['emp_id'];
    $att_date = date('Y-m-d'); // Attendance date in Y-m-d format
    $current_time = date('H:i:s'); // Get the current time

    // Determine the greeting message based on the current time
    if ($current_time >= "00:00:00" && $current_time < "12:00:00") {
        $greeting = "Good Morning";
    } elseif ($current_time >= "12:00:00" && $current_time < "18:00:00") {
        $greeting = "Good Afternoon";
    } else {
        $greeting = "Error: Invalid time for check-in.";
        echo "<script>alert('$greeting');</script>";
        exit;
    }

    echo "<script>alert('$greeting');</script>";

    // Check if the user has already submitted attendance for the current date
    $existingRecord = checkExistingRecord($conn, $emp_id, $att_date);

    if (!$existingRecord) {
        // Get status from the form
        $status = $_POST['status']; // Retrieve the status from the form
        // Store check_in with full datetime format
        $check_in = ($status == 'present') ? date('Y-m-d H:i:s', strtotime($_POST['check_in'])) : null;
        $check_out = null; // For initial submission, set check_out to null
        $total_hours = $_POST['total_hours'];

        $sql = "INSERT INTO attendance (emp_id, att_date, check_in, check_out, total_hours, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $emp_id, $att_date, $check_in, $check_out, $total_hours, $status);
        $stmt->execute();
        $stmt->close();

        // Update the status in the employee table
        if ($status == 'present') {
            $status_update_query = "UPDATE employee SET status = 'active' WHERE emp_id = ?";
            $stmt = $conn->prepare($status_update_query);
            $stmt->bind_param("s", $emp_id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        echo "<script>alert('Attendance already submitted for today.');</script>";
    }
}

// After fetching the existing attendance record, modify the check-out handling
if (isset($_POST['update'])) {
    $emp_id = $_SESSION['emp_id'];
    // Store check_out with full datetime format
    $check_out_time = date('Y-m-d H:i:s', strtotime($_POST['check_out']));
    $today = date('Y-m-d');

    $sql3 = "SELECT * FROM attendance WHERE emp_id = ? AND att_date = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("ss", $emp_id, $today);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    if ($result3 && mysqli_num_rows($result3) > 0) {
        $row = mysqli_fetch_assoc($result3);
        $check_in = $row['check_in'];
        $existing_check_out = $row['check_out']; // Check if check_out already exists

        if (!empty($existing_check_out)) {
            echo "<script>alert('Error: You have already checked out today.');</script>";
        } else {
            // Calculate time difference in hours
            $starttime = strtotime($check_in);
            $endtime = strtotime($check_out_time);
            $diff = $endtime - $starttime;
            $hours = $diff / 3600; // Convert seconds to hours

            if ($hours < 1) {
                echo "<script>alert('Error: Check-out time must be at least one hour after check-in.');</script>";
            } elseif ($hours < 7) {
                echo "<script>alert('Today marked as Half Day Leave.');</script>";
            } else {
                // Proceed with updating check_out
                $t_hours = gmdate("H:i:s", $hours * 3600);
                $sql4 = "UPDATE attendance SET check_out = ?, total_hours = ? WHERE emp_id = ? AND att_date = ?";
                $stmt4 = $conn->prepare($sql4);
                $stmt4->bind_param("ssss", $check_out_time, $t_hours, $emp_id, $today);
                $stmt4->execute();
                $stmt4->close();

                // Update the status in the employee table
                $status_update_query = "UPDATE employee SET status = 'inactive' WHERE emp_id = ?";
                $stmt5 = $conn->prepare($status_update_query);
                $stmt5->bind_param("s", $emp_id);
                $stmt5->execute();
                $stmt5->close();
            }
        }
    } else {
        echo "<script>alert('Cannot update. No attendance submitted for today.');</script>";
    }
}



// Display the total hours
echo "$t_hours";

$emp_id = $_SESSION['emp_id'];
$sql6 = "SELECT * FROM attendance WHERE emp_id = ?";
$stmt6 = $conn->prepare($sql6);
$stmt6->bind_param("s", $emp_id);
$stmt6->execute();
$stmt6->store_result(); // Store result set
$stmt6->bind_result($att_id, $att_emp_id, $att_date, $att_check_in, $att_check_out, $att_total_hours, $att_status); // Bind result variables
$stmt6->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/css/atten.css?v=1.0">
    <style>
        .input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        /* Button Styling */
.view-button {
    display: inline-block; /* Allows for padding and margins */
    padding: 10px 20px; /* Adjust padding as needed */
    margin: 10px; /* Adjust margin as needed */
    background-color: #007bff; /* Default background color */
    color: #ffffff; /* Default text color */
    border: none; /* Removes default border */
    border-radius: 15px; /* Rounded corners */
    font-size: 16px; /* Adjust font size as needed */
    text-align: center; /* Center align text */
    text-decoration: none; /* Remove underline from links */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transition for background color and scale */
}

/* Hover Effect */
.view-button:hover {
    background-color: yellow; /* Background color on hover */
    color: black; /* Text color on hover */
    transform: scale(1.05); /* Slightly enlarge the button */
}

/* Center Align in Parent Container */
.parent-container {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    height: 100vh; /* Adjust based on your layout */
}

    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <div class="att">
        <div class="att-form">
            <h1>Attendance System</h1>

            <?php
            // Check if the user has already checked in for today
            $existingRecord = checkExistingRecord($conn, $emp_id, date('Y-m-d'));

            if (!$existingRecord) {
                // User has not checked in for today, display check-in form
            ?>
            <form class="attform1" method="post" action="">
                <label for="status">Select Status:</label>
                <select name="status" id="status" onchange="toggleCheckFields()">
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>

                <div id="checkInField">
                    <label for="check_in">Log In</label>
                    <input type="datetime-local" class="input--style-1 check_in" name="check_in" id="check_in" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly required>
                    <?php if (isset($error['check_in'])) echo "<span class='error'>* " . $error['check_in'] . "</span>" ?>
                </div>

                <input type="hidden" class="input--style-1" name="total_hours" id="total_hours" readonly>

                <button type="submit" name="submit">Login</button>
            </form>
            
             

            <script>
                function toggleCheckFields() {
                    const statusSelect = document.getElementById('status');
                    const checkInField = document.getElementById('checkInField');

                    if (statusSelect.value === 'present') {
                        checkInField.style.display = 'block';
                    } else {
                        checkInField.style.display = 'none';
                    }
                }
            </script>

            <?php } else { ?>
            
            <form class="attform1" method="post" action="">
                <label for="check_out">Check Out</label>
                <input type="datetime-local" class="input--style-1 check_out" name="check_out" id="check_out" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                <?php if (isset($error['check_out'])) echo "<span class='error'>* " . $error['check_out'] . "</span>" ?>
                <button type="submit" name="update" id="checkOutButton">Check Out</button>
            </form>
            <button class="view-button" onclick="location.href='view-attendance.php'">View Attendance</button>
            <?php } ?>
        </div>

        <!-- Display attendance records for the current month -->
        <div class="att-record">
            <h2>Your Attendance Record for <?php echo date('F Y'); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check-In</th>   
                        <th>Check-Out</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch and display attendance records for the current month
                $current_month = date('Y-m');
                $sql7 = "SELECT * FROM attendance WHERE emp_id = ? AND att_date LIKE '$current_month%' ORDER BY att_date DESC";
                $stmt7 = $conn->prepare($sql7);
                $stmt7->bind_param("s", $emp_id);
                $stmt7->execute();
                $result7 = $stmt7->get_result();

                while ($row7 = $result7->fetch_assoc()) {
                    $status = isset($row7['status']) ? $row7['status'] : ''; // Initialize $status safely
                    $icon = '';
                    $color = '';

                    if ($status == 'present') {
                        $icon = 'fa-check';
                        $color = 'green';
                    } elseif ($status == 'absent') {
                        $icon = 'fa-times';
                        $color = 'red';
                    } elseif ($status == 'half-day') {
                        $icon = 'fa-adjust';
                        $color = 'orange';
                    }

                    echo "<tr>
                        <td>{$row7['att_date']}</td>
                        <td>{$row7['check_in']}</td>
                        <td>{$row7['check_out']}</td>
                        <td>{$row7['total_hours']}</td>
                        <td><i class='fa {$icon}' style='color:{$color};'></i></td>
                    </tr>";
                }

                $stmt7->close();
                ?>

                </tbody>
            </table>
        </div>
    </div>
    <!-- <?php include('vendor/inc/footer.php'); ?> -->
</body>
</html>
