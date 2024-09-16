<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}


$sql = "SELECT employee.emp_id,employee.first_name,employee.last_name,salary.salary,salary.bonus,salary.total from employee,`salary` where employee.emp_id=salary.emp_id";

//echo "$sql";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel ="stylesheet" href="vendor/css/salary.css?v=1.0">
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>
<h2 class="h2">Salary Details</h2>
<div>
    <div class="input-container">
        <input list="employee-options" id="employee-input" placeholder="Search Emp ID / Name">
        <i class="fa fa-search search-icon"></i>
        <datalist id="employee-options">
            <?php
            // Fetch the employee details to populate the datalist
            $employee_query = "SELECT emp_id, first_name, last_name FROM employee";
            $employee_result = mysqli_query($conn, $employee_query);

            while ($employee = mysqli_fetch_assoc($employee_result)) {
                echo "<option value='" . htmlspecialchars($employee['emp_id'] . " - " . $employee['first_name'] . " " . $employee['last_name']) . "'>";
            }
            ?>
        </datalist>
    </div>
    <button class="add-emp">
        <a href="emp-bank.php">
            <i class="fa-solid fa-eye"></i> Bank
        </a>
    </button>
</div>

<div class="contain">
<table id="employee-table">
    <tr>
        <th align="center">S No</th>
        <th align="center">Emp. ID</th>
        <th align="center">Name</th>
        <th align="center">Base Salary</th>
        <th align="center">Bonus</th>
        <th align="center">Total Salary</th>
        <th align="center">Options</th>
    </tr>
    
    <?php
        $counter = 1; // Initialize the counter to 1
    while ($employee = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>". $counter. "</td>";  // Display the counter as the first column
        echo "<td class='emp-id'>" . htmlspecialchars($employee['emp_id']) . "</td>";
        echo "<td class='emp-name'>" . htmlspecialchars($employee['first_name']) . " " . htmlspecialchars($employee['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($employee['salary']) . "</td>";
        echo "<td>" . htmlspecialchars($employee['bonus']) . " %</td>";
        echo "<td>" . htmlspecialchars($employee['total']) . "</td>";
        echo "<td>
				<a class='edit' href=\"salary_edit.php?emp_id=".$employee['emp_id']."\">Edit</a> <br> <br>
                <a class='delete' href=\"salary_delete.php?emp_id=".$employee['emp_id']."\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a>
			</td>";
        echo "</tr>";
        $counter++; // Increment the counter after each row
    }
    ?>
</table>
</div>
<script>
    document.getElementById('employee-input').addEventListener('input', function() {
        var input = this.value.toLowerCase();
        var table = document.getElementById('employee-table');
        var rows = table.getElementsByTagName('tr');

        for (var i = 1; i < rows.length; i++) { // Start from 1 to skip header row
            var empId = rows[i].getElementsByClassName('emp-id')[0].textContent.toLowerCase();
            var empName = rows[i].getElementsByClassName('emp-name')[0].textContent.toLowerCase();
            
            if (empId.includes(input) || empName.includes(input)) {
                rows[i].style.display = ''; // Show the row
            } else {
                rows[i].style.display = 'none'; // Hide the row
            }
        }
    });
</script>

</body>
</html>