<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Query to fetch employee details for the table
$sql = "SELECT 
            employee.type, 
            employee.emp_id, 
            employee.img, 
            employee.first_name, 
            employee.last_name, 
            employee.email, 
            employee.date_of_birth, 
            employee.gender, 
            employee.contact, 
            employee.whatsapp_no, 
            employee.address, 
            employee.role,  
            employee.qualification,  
            employee.status
        FROM employee 
        LEFT JOIN rank ON employee.emp_id = rank.emp_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Query to fetch employee ID and first name for datalist options
$sql_employee_options = "SELECT emp_id, first_name FROM employee";
$employee_options_result = mysqli_query($conn, $sql_employee_options);

if (!$employee_options_result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/css/view-emp.css?v=1.0">
    <?php include('vendor/inc/head.php'); ?>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>
<h2 class="h2">Employee & Trianee Details</h2>
<div>
    <div class="input-container">
        <input list="employee-options" id="employee-input" placeholder="Search Emp ID / Name">
        <i class="fa fa-search search-icon"></i>
        <datalist id="employee-options">
            <?php foreach ($employees as $emp_id => $emp_name): ?>
                <option value="<?php echo $emp_id . ' - ' . $emp_name; ?>"></option>
            <?php endforeach; ?>
        </datalist>
    </div>
    <button class="add-emp">
        <a href="add-employee.php">
            <i class="fa-solid fa-plus"></i> Add Employee
        </a>
    </button>
</div>

<div class="contain">
    <table id="employee-table">
        <tr>
            <th align="center">S No</th>
            <th align="center">Emp. ID</th>
            <th align="center">Picture</th>
            <th align="center">Name</th>
            <th align="center">Email</th>
            <th align="center">Date Of Birth</th>
            <th align="center">Gender</th>
            <th align="center">Contact</th>
            <th align="center">WhatsApp No</th>
            <th align="center">Address</th>
            <th align="center">Role</th>
            <th align="center">Qualification</th>
            <th align="center">Status</th>
            <th align="center">Options</th>
        </tr>

        <?php
            $counter = 1; // Initialize the counter to 1
            while ($employee = mysqli_fetch_assoc($result)) {
                $img_src = !empty($employee['img']) ? 'data:image/jpeg;base64,' . base64_encode($employee['img']) : 'vendor/images/default-avatar.png';
                echo "<tr>";
                echo "<td class='emp-id'>{$counter}</td>"; // Use the counter value for S ID
                echo "<td class='emp-id'>".$employee['emp_id']."</td>";
                echo "<td><img src='$img_src' height='60px' width='60px' alt='Employee Image'></td>";
                echo "<td class='emp-name'>".$employee['first_name']." ".$employee['last_name']."</td>";
                echo "<td>".$employee['email']."</td>";
                echo "<td>".$employee['date_of_birth']."</td>";
                echo "<td>".$employee['gender']."</td>";
                echo "<td>".$employee['contact']."</td>";
                echo "<td>".$employee['whatsapp_no']."</td>";
                echo "<td>".$employee['address']."</td>";
                echo "<td>".$employee['role']."</td>";
                echo "<td>".$employee['qualification']."</td>";
                echo "<td>".$employee['status']."</td>";
                echo "<td><a class='edit' href=\"edit.php?emp_id=".$employee['emp_id']."\">Edit</a> <br> <br>
                      <a class='delete' href=\"delete.php?emp_id=".$employee['emp_id']."\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
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

        for (var i = 1; i < rows.length; i++) {
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
