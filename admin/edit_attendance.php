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

$emp_id = $_GET['emp_id'];
$month = $_GET['month'];
$year = $_GET['year'];

// Fetch the holidays for the selected month
$holidays = holidays();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Loop through the posted days to update their status
    foreach ($_POST['days'] as $day => $status) {
        $date = "$year-$month-$day";
        
        // Update holiday/working day in the database
        $sqlUpdate = "UPDATE attendance SET status = ? WHERE emp_id = ? AND att_date = ?";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param('sis', $status, $emp_id, $date);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to the attendance page
    header("Location: attendance.php?month=$month&year=$year");
    exit();
}

// Fetch current attendance data for the employee
$sqlAttendance = "SELECT att_date, status FROM attendance WHERE emp_id = ? AND att_date BETWEEN ? AND ?";
$stmtAttendance = $conn->prepare($sqlAttendance);
$firstDate = "$year-$month-01";
$lastDate = date("$year-$month-t");
$stmtAttendance->bind_param("iss", $emp_id, $firstDate, $lastDate);
$stmtAttendance->execute();
$stmtAttendance->bind_result($att_date, $status);

$attendanceData = [];
while ($stmtAttendance->fetch()) {
    $attendanceData[$att_date] = $status;
}
$stmtAttendance->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Holidays or Working Days</title>
</head>
<body>
    <h2>Edit Holidays / Working Days for Employee: <?php echo $emp_id; ?></h2>
    <form method="POST">
        <table>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>
            <?php for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++): ?>
                <?php $date = "$year-$month-" . sprintf("%02d", $day); ?>
                <tr>
                    <td><?php echo $date; ?></td>
                    <td>
                        <select name="days[<?php echo $day; ?>]">
                            <option value="present" <?php echo (isset($attendanceData[$date]) && $attendanceData[$date] == 'present') ? 'selected' : ''; ?>>Working Day</option>
                            <option value="holiday" <?php echo (in_array($date, $holidays)) ? 'selected' : ''; ?>>Holiday</option>
                            <option value="absent" <?php echo (isset($attendanceData[$date]) && $attendanceData[$date] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                        </select>
                    </td>
                </tr>
            <?php endfor; ?>
        </table>
        <button type="submit">Save Changes</button>
    </form>
    <a href="attendance.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>">Back to Attendance</a>
</body>
</html>
