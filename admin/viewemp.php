<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Ensure that 'whatsapp_no' is the correct column name as per your database schema
$sql = "SELECT 
            employee.emp_id, 
            employee.img, 
            employee.first_name, 
            employee.last_name, 
            employee.email, 
            employee.date_of_birth, 
            employee.gender, 
            employee.contact, 
            employee.whatsapp_no,  -- Correct column name
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="vendor/css/view-emp.css">
    <?php include('vendor/inc/head.php'); ?>
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>
<h2 class="h2">Employee Details</h2>
<button class="add-emp"><a href="add-employee.php">Add Employee</a></button>
<div class="contain">
    <table>
        <tr>
            <th align="center">Emp. ID</th>
            <th align="center">Picture</th>
            <th align="center">Name</th>
            <th align="center">Email</th>
            <th align="center">Date Of Birth</th>
            <th align="center">Gender</th>
            <th align="center">Contact</th>
            <th align="center">WhatsApp No</th> <!-- Updated header -->
            <th align="center">Address</th>
            <th align="center">Role</th>
            <th align="center">Qualification</th>
            <th align="center">Status</th>
            <th align="center">Options</th>
        </tr>

        <?php
            while ($employee = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$employee['emp_id']."</td>";
                echo "<td><img src='vendor/images/".$employee['img']."' height='60px' width='60px' alt='Employee Image'></td>";
                echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
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
            }
        ?>

    </table>
</div>
</body>
</html>
