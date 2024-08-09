<?php
session_start();
require_once('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

$emp_id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');

// Adjusted SQL query to select "status" from the "attendance" table
$sql = "SELECT attendance.*, employee.first_name 
        FROM attendance
        JOIN employee ON attendance.emp_id = employee.emp_id
        WHERE attendance.emp_id = '$emp_id'";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>    
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        position: relative;
    }

    .button-container {
        position: absolute;
        top: 120px;
        right: 20px;
        z-index: 1000;
    }

    .download-button {
        background-color: #4F81BD;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;   
    }

    .download-button:hover {
        background-color: #e2ed0b;
        color: black;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 60px;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #4F81BD;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #e0e0e0;
    }

    .h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    </style>
   
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <h2 class="h2">Attendance Details</h2>
    <div class="">
        <div class="button-container">
            <button class="download-button"><i class="fa fa-download"></i> Download</button>
        </div>
        <table>
            <tr>
                <th>Emp. ID</th>
                <th>Employee Name</th>
                <th>Attendance Date</th>
                <th>Log In</th>
                <th>Log Out</th>
                <th>Total Hours</th>
                <th>Status</th>
            </tr>

            <?php
            while ($employee = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$employee['emp_id']."</td>";              
                echo "<td>".$employee['first_name']."</td>";           
                echo "<td>".$employee['att_date']."</td>";
                echo "<td>".$employee['check_in']."</td>";
                echo "<td>".$employee['check_out']."</td>";
                echo "<td>".$employee['total_hours']."</td>";
                echo "<td>".$employee['status']."</td>";
                echo "</tr>";
            }
            ?>

        </table>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@latest/dist/exceljs.min.js"></script>
    <script>
    document.querySelector('.download-button').addEventListener('click', function () {
    // Create a workbook and add a worksheet
    var workbook = new ExcelJS.Workbook();
    var worksheet = workbook.addWorksheet('Attendance');

    // Get today's date
    var today = new Date();
    var formattedDate = today.toLocaleDateString('en-CA'); // YYYY-MM-DD format

    // Extract employee name from the first row of the table
    var employeeName = document.querySelector("table tr:nth-child(2) td:nth-child(2)").innerText;

    // Initialize counters
    var totalEmp = 0;
    var absentCount = 0;
    var presentCount = 0;
    var totalWorkingDays = 0; // Total working days

    // Get the table rows and cells
    var rows = document.querySelectorAll("table tr:nth-child(n+2)"); // Adjust to start from the second row
    rows.forEach(function(row) {
        totalWorkingDays++; // Increment total working days
        totalEmp++; // Increment total employee count
        var cells = row.querySelectorAll("td");
        cells.forEach(function(cell) {
            if (cell.innerHTML.includes('fa-check')) {
                presentCount++; // Increment present count
            } else if (cell.innerHTML.includes('fa-times')) {
                absentCount++; // Increment absent count
            }
        });
    });

    // Add summary information at the top of the worksheet
    worksheet.addRow(['Date:', formattedDate]);
    worksheet.addRow(['Employee Name:', employeeName]); // Use the extracted employee name
    worksheet.addRow(['Total Working Days:', totalWorkingDays]);
    worksheet.addRow(['Total Present Days:', presentCount]);
    worksheet.addRow(['Total Absent Days:', absentCount]);
    worksheet.addRow([]); // Empty row for spacing

    // Apply styling to the summary section
    worksheet.eachRow({ includeEmpty: false }, function(row, rowNumber) {
        if (rowNumber <= 5) {
            row.eachCell({ includeEmpty: false }, function(cell) {
                cell.font = { bold: true };
                cell.alignment = { horizontal: 'center' };
            });
        }
    });

    // Get the table headers
    var headers = [];
    var headerCells = document.querySelectorAll("table tr:nth-child(1) th"); // Adjust to get headers from the first row
    headerCells.forEach(function(cell) {
        headers.push(cell.innerText);
    });
    worksheet.addRow(headers);

    // Apply header styling
    worksheet.getRow(7).eachCell((cell) => {
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
        if (rowNumber > 7) { // Skip summary and header rows
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
        { width: 20 }, { width: 20 }, { width: 20 }, { width: 20 }, { width: 20 },
        { width: 20 }, { width: 20 }
    ];

    // Export the workbook as an Excel file with the employee name and today's date in the filename
    workbook.xlsx.writeBuffer().then(function(data) {
        var blob = new Blob([data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = employeeName + "_attendance_" + formattedDate + ".xlsx";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
});

    </script>
</body>
</html>
