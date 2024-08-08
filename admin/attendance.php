<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Function to get the next and previous months
function getNextMonth($month, $year) {
    $nextMonth = $month + 1;
    $nextYear = $year;
    if ($nextMonth > 12) {  
        $nextMonth = 1;
        $nextYear++;
    }
    return array($nextMonth, $nextYear);
}

function getPreviousMonth($month, $year) {
    $prevMonth = $month - 1;
    $prevYear = $year;
    if ($prevMonth < 1) {
        $prevMonth = 12;
        $prevYear--;
    }
    return array($prevMonth, $prevYear);
}

// Get the current month and year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Calculate next and previous months
list($nextMonth, $nextYear) = getNextMonth($month, $year);
list($prevMonth, $prevYear) = getPreviousMonth($month, $year);

// Get the number of days in the current month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Get the first and last date of the current month
$firstDate = date("$year-$month-01");
$lastDate = date("$year-$month-t");

// Fetch employee names
$sqlNames = "SELECT emp_id, first_name FROM employee";
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
$stmtAttendance->bind_result($emp_id, $att_date, $status);

// Organize attendance data into a 2D array
$attendanceData = array();
while ($stmtAttendance->fetch()) {
    $attendanceData[$emp_id][$att_date] = $status;
}
$stmtAttendance->close();

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

// Example usage
$holidays = holidays();

$date1 = date('t');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/css/atten.css">
    <style>
        #search-box {
            margin-bottom: 15px;
        }
        .green {
            color: green;
        }

        .red {
            color: red;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php') ?>
    <div class="container-fluid">
        <h2 class="h2">Employee Attendance</h2>
        <div class="contain">
            <table border="1">
                <tr>
                    <th colspan="<?php echo $daysInMonth + 2; ?>"> Month: <?php echo date("F Y", strtotime("$year-$month-01")); ?></th>
                </tr>
                <tr>
                    <th>Employee</th>
                    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                        <th><?php echo $day; ?></th>
                    <?php endfor; ?>
                    <th>Action</th>
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
                                        echo '<i class="fa fa-check green" aria-hidden="true"></i>';
                                    } else {
                                        echo '<i class="fa fa-times red" aria-hidden="true"></i>';
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
                        <td><button><a class="view-attendance" href="view-attendance.php?emp_id=<?php echo $emp_id ?>&month=<?php echo $month ?>&year=<?php echo $year ?>">View</a></button></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div style="text-align: center; margin-bottom: 20px;">
                <a class="Previous" href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>">&laquo; Previous</a> |
                <a class="next" href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>">Next &raquo;</a>
            </div>
        </div>
    </div>
</body>
</html>
