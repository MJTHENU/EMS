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

// Fetch holiday dates from the database
$sqlHolidays = "SELECT holiday_date FROM holidays";
$resultHolidays = mysqli_query($conn, $sqlHolidays);
$holidays = array();
while ($row = mysqli_fetch_assoc($resultHolidays)) {
    $holidays[] = $row['holiday_date'];
}

$date1 = date('t');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/css/atten.css?v=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@latest/dist/exceljs.min.js"></script>
</head>
<body>
    <?php include('vendor/inc/nav.php') ?>
    <div class="container-fluid">
        <h2 class="h2">Employee & Trainee Attendance</h2>
        <div class="contain">
            <div class="input-container">
                <input list="employee-options" id="employee-input" placeholder="Search Emp ID / Name">
                <i class="fa fa-search search-icon"></i>
                <datalist id="employee-options">
                    <?php foreach ($employees as $emp_id => $emp_name): ?>
                        <option value="<?php echo $emp_id . ' - ' . $emp_name; ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="button-container">         
                <button class="download-button"><i class="fa fa-download"></i> Download</button>
            </div> 
            <div class="holiday-form">
                <div style="flex: 0; margin: 30px 10px 0 0;">
                    <form id="holiday-form" method="post" action="update-holidays.php">
                        <div class="form-group">
                            <input type="date" id="holiday-date" name="holiday-date" required>
                        </div>
                        <div>
                            <button type="submit" name="action" value="add">Add</button>
                            <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>    
        <table border="1">
            <tr>
                <th colspan="<?php echo $daysInMonth + 2; ?>">Month: <?php echo date("F Y", strtotime("$year-$month-01")); ?></th>
            </tr>
            <tr>
                <th>S No</th>
                <th>Employee</th>
                <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                    <th><?php echo $day; ?></th>
                <?php endfor; ?>
                <th>Action</th>
            </tr>

            <?php
            $counter = 1;
            foreach ($employees as $emp_id => $emp_name): ?>
                <tr>
                    <td><?php echo $counter++; ?></td>
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
                                echo '<span style="color: red;">Holiday</span>';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    <?php endfor; ?>
                    <td>
                        <button>
                            <a class="view-attendance" href="view-attendance.php?emp_id=<?php echo $emp_id ?>&month=<?php echo $month ?>&year=<?php echo $year ?>">
                                <i class="fa fa-eye"></i> 
                            </a>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
        <a class="Previous" href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>">&laquo; Previous</a> |
        <a class="next" href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>">Next &raquo;</a>
    </div>
</div>

            <script>
            document.querySelector('.download-button').addEventListener('click', function () {
            // Create a workbook and add a worksheet
            var workbook = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Attendance');

            // Get today's date
            var today = new Date();
            var formattedDate = today.toLocaleDateString('en-CA'); // YYYY-MM-DD format

            // Initialize counters
            var totalEmp = 0;
            var absentCount = 0;
            var presentCount = 0;

            // Get the table rows and cells
            var rows = document.querySelectorAll("table tr:nth-child(n+3)");
            rows.forEach(function(row) {
                totalEmp++; // Increment total employee count
                var cells = row.querySelectorAll("td");
                cells.forEach(function(cell, index) {
                    if (index === today.getDate()) { // Check if the current cell corresponds to today's date
                        if (cell.innerHTML.includes('fa-check')) {
                            presentCount++; // Increment present count
                        } else if (cell.innerHTML.includes('fa-times')) {
                            absentCount++; // Increment absent count
                        }
                    }
                });
            });

            // Add summary information at the top of the worksheet
            worksheet.addRow(['Date:', formattedDate]);
            worksheet.addRow(['Total Employees:', totalEmp]);
            worksheet.addRow(['Present Count:', presentCount]);
            worksheet.addRow(['Absent Count:', absentCount]);
            worksheet.addRow([]); // Empty row for spacing

            // Apply styling to the summary section
            worksheet.eachRow({ includeEmpty: false }, function(row, rowNumber) {
                if (rowNumber <= 4) {
                    row.eachCell({ includeEmpty: false }, function(cell) {
                        cell.font = { bold: true };
                        cell.alignment = { horizontal: 'center' };
                    });
                }
            });

            // Get the table headers (e.g., Employee, 1, 2, 3, ...)
            var headers = [];
            var headerCells = document.querySelectorAll("table tr:nth-child(2) th");
            headerCells.forEach(function(cell) {
                headers.push(cell.innerText);
            });
            worksheet.addRow(headers);

            // Apply header styling
            worksheet.getRow(6).eachCell((cell) => {
                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF4F81BD' }
                };
                cell.alignment = { horizontal: 'center' };
            });

            // Fill in the data for each employee
            rows.forEach(function(row) {
                var rowData = [];
                var cells = row.querySelectorAll("td, th");
                cells.forEach(function(cell) {
                    if (cell.innerHTML.includes('fa-check')) {
                        rowData.push("✔ Present");
                    } else if (cell.innerHTML.includes('fa-times')) {
                        rowData.push("✖ Absent");
                    } else if (cell.innerText.includes('Holiday')) {
                        rowData.push("Holiday");
                    } else {
                        rowData.push(cell.innerText); // For other cells, keep the text as it is
                    }
                });
                worksheet.addRow(rowData);
            });

            // Apply data styling
            worksheet.eachRow({ includeEmpty: false }, function(row, rowNumber) {
                if (rowNumber > 6) { // Skip summary and header rows
                    row.eachCell(function(cell) {
                        cell.alignment = { horizontal: 'center' };
                        if (cell.value === "✖ Absent") {
                            cell.font = { color: { argb: 'FFFF0000' } };
                        } else if (cell.value === "✔ Present") {
                            cell.font = { color: { argb: 'FF008000' } };
                        }
                    });
                }
            });

            // Set column widths
            worksheet.columns = [
                { width: 20 }, { width: 12 }, { width: 12 }, { width: 12 }, { width: 12 }
            ];

            // Export the workbook as an Excel file with today's date in the filename
            workbook.xlsx.writeBuffer().then(function(data) {
                var blob = new Blob([data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = "attendance_" + formattedDate + ".xlsx";
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        });

</script>

<!-- Auto-fill the employee dropdown with employee names -->
<script>
    document.getElementById('employee-input').addEventListener('input', function() {
        var input = this.value;
        var selectedOption = document.querySelector('#employee-options option[value="' + input + '"]');

        if (selectedOption) {
            var empId = input.split(' - ')[0];
            window.location.href = 'view-attendance.php?emp_id=' + empId + '&month=<?php echo $month; ?>&year=<?php echo $year; ?>';
        }
    });
</script>

<script>
document.getElementById('holiday-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    fetch('update-holidays.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.text(); // Assuming your PHP returns plain text
    })
    .then(data => {
        console.log(data); // For debugging, log the response in the console
        alert(data); // Show success message in alert
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message); // Display error message to the user
    });
});

</script>



</body>
</html>