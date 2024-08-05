<?php
session_start();

// Include your database connection file
require_once('vendor/inc/connection.php');

date_default_timezone_set('Asia/Kolkata');

$t_hours = "";


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
function checkExistingRecord($conn, $emp_id, $att_date)
{
    $sql = "SELECT * FROM attendance WHERE emp_id = ? AND att_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $att_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->num_rows > 0;
}

// Check if the user is logged in
if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

// Handle attendance recording
if (isset($_POST['submit'])) {
    $emp_id = $_SESSION['emp_id'];
    $att_date = date('Y-m-d');
    $status = $_POST['status'];

    // Check if the user has already submitted attendance for the current date
    $existingRecord = checkExistingRecord($conn, $emp_id, $att_date);

    if (!$existingRecord) {
        // User has not submitted attendance for the current date, proceed with insertion
        $check_in = ($status == 'present') ? $_POST['check_in'] : null;
        $check_out = ($status == 'present') ? null : null; // For initial submission, set check_out to null
        $total_hours = $_POST['total_hours'];

        $sql = "INSERT INTO attendance (emp_id, att_date, check_in, check_out, total_hours, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $emp_id, $att_date, $check_in, $check_out, $total_hours, $status);
        $stmt->execute();
        $stmt->close();
    } else {
        // User has already submitted attendance for the current date
        // You can handle this case as needed (display an error message, redirect, etc.)
        echo "Attendance already submitted for today.";
    }
}

$error = array();

// Handle attendance update
if (isset($_POST['update'])) {
    $emp_id = $_SESSION['emp_id'];
    $today = date('Y-m-d');
    // Check if the user has already logged out for today
    $sql1 = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND att_date = '$today' AND check_out IS NOT NULL";
    $run1 = mysqli_query($conn, $sql1);

    // Check if the user is on leave today
    $sql2 = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND att_date = '$today' AND check_out IS NULL AND status = 'absent'";
    $run2 = mysqli_query($conn, $sql2);

    if ($run1 && mysqli_num_rows($run1) > 0) {
        $error['check_out'] = "Already logged out for today.";
    } elseif ($run2 && mysqli_num_rows($run2) > 0) {
        $error['check_out'] = "You are on leave today.";
    }
else {
        // Proceed with your existing update logic
        $sql1 = "SELECT * FROM attendance WHERE emp_id = '$emp_id' AND att_date = '$today'";
        $run = mysqli_query($conn, $sql1);

        if ($run && mysqli_num_rows($run) > 0) {
            $row = mysqli_fetch_assoc($run);
            $check_in = $row['check_in'];
            $check_out = $_POST['check_out'];
            $emp_id = $_SESSION['emp_id'];
            $att_date = date('Y-m-d');

            $date = date('m/d/Y H:i:s', time());

            $starttime = strtotime($check_in);
            $endtime = strtotime($date);
            $diff = $endtime - $starttime;

            $hours = $diff / 3600; // Convert seconds to hours

            $t_hours = gmdate("H:i:s", $hours * 3600);

            // Check if the user has already submitted attendance for the current date
            $existingRecord = checkExistingRecord($conn, $emp_id, $att_date);

            if ($existingRecord && empty($error)) {
                // User has already submitted attendance for the current date, proceed with update
                $sql = "UPDATE attendance SET check_out = ?, total_hours = ? WHERE emp_id = ? AND att_date = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $check_out, $t_hours, $emp_id, $att_date);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "Cannot update. No attendance submitted for today.";
            }
        }
    }
}

// Display the total hours
echo "$t_hours";



// Display attendance records
$emp_id = $_SESSION['emp_id'];
$sql = "SELECT * FROM attendance WHERE emp_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <div class="att" style = "margin: 5%;">
    <h1>Attendance System</h1>

    <?php
    // Check if the user has already checked in for today
    $existingRecord = checkExistingRecord($conn, $emp_id, date('Y-m-d'));

    if (!$existingRecord) {
        // User has not checked in for today, display check-in form
    ?>
    <form method="post" action="">
        <label for="status">Select Status:</label>
        <select name="status" id="status" onchange="toggleCheckFields()">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
        </select>

        <div id="checkInField">
            <label for="check_in">Log In</label>
            <input type="datetime-local" class="input--style-1 check_in" name="check_in" id="check_in" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
        </div>

        <input type="hidden" class="input--style-1" name="total_hours" id="total_hours" readonly>

        <button type="submit" name="submit">Login</button>
    </form>
    <?php } else {
        // User has already checked in for today, display check-out form
    ?>
    <form method="post" action="">
        <div id="checkOutField">
            <label for="check_out">Log Out</label>
            <input type="datetime-local" class="input--style-1 check_out" name="check_out" id="check_out"  value="<?php echo date('Y-m-d\TH:i'); ?>" required>
            <?php if(isset($error['check_out'])) echo "<span class='error'>* " . $error['check_out']. "</span>"  ?>
           </div>

        <!-- <label for="total_hours">Total Hours</label> -->
        <input type="hidden" class="input--style-1" name="total_hours"  id="total_hours" readonly>

        <button type="submit" name="update">Logout</button>
    </form>
    <?php } echo "$t_hours";  ?>

    <?php  
    
$month = date('m');
$year = date('Y');

// Get the number of days in the current month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Get the first and last date of the current month
$firstDate = date('Y-m-01');
$lastDate = date('Y-m-t');

// Fetch employee names
$sqlNames = "SELECT emp_id, first_name FROM employee where emp_id = '$emp_id'";
$resultNames = mysqli_query($conn, $sqlNames);
$employees = array();
while ($row = mysqli_fetch_assoc($resultNames)) {
    $employees[$row['emp_id']] = $row['first_name'];
}

// Fetch attendance data for the current month
$sqlAttendance = "SELECT emp_id, att_date, status FROM attendance WHERE att_date BETWEEN ? AND ?";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->bind_param("ss", $firstDate, $lastDate);
$stmtAttendance->execute();
$resultAttendance = $stmtAttendance->get_result();

// Organize attendance data into a 2D array
$attendanceData = array();
while ($row = $resultAttendance->fetch_assoc()) {
    $attendanceData[$row['emp_id']][$row['att_date']] = $row['status'];
}

    ?>

    <!-- Attendance Records -->
    <h2>Your Attendance Records:</h2>
    <table border="1">
    <tr>
        <th colspan="<?php echo $daysInMonth + 2; ?>"> Month: <?php echo date("F Y"); ?></th>
    </tr>
        <tr>
            <th>Employee</th>
            <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
       
                <th><?php echo $day; ?></th>
            <?php endfor; ?>
        </tr>
        <?php foreach ($employees as $emp_id => $emp_name): ?>
            <tr>
                <td><?php echo $emp_name; ?></td>
                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                    <td>
                    <?php
                $date = date("Y-m-d", strtotime("$year-$month-$day"));
                if (isset($attendanceData[$emp_id][$date])) {
                    $status = $attendanceData[$emp_id][$date];
                    if ($status == 'present') {
                        echo '<i class="fa fa-check" style = "color: green;" aria-hidden="true"></i>';
                    } else {
                        echo '<i class="fa fa-times" style = "color: red;" aria-hidden="true"></i>';
                    }
                } elseif (in_array($date, $holidays)) {
                    // Display holidays in black color
                    echo '<span style="color: red;">Holiday</span>';
                } else {
                    echo '-';
                }
                ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
    </table>
   

    <script>
    

    function toggleCheckFields() {
        var status = document.getElementById("status").value;
        var checkInField = document.getElementById("checkInField");
        var checkOutField = document.getElementById("checkOutField");

        if (status === "present") {
            checkInField.style.display = "block";
            checkOutField.style.display = "none"; // Initially hide check-out
        } else if (status === "absent") {
            checkInField.style.display = "none";
            checkOutField.style.display = "none"; // Display check-out
        }
    }

</script>

</body>
</html>
