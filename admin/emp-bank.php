<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Fetch employee data
$sql = "SELECT employee.emp_id, employee.first_name, employee.last_name, 
               employee_bank_details.passbook_img, employee_bank_details.bank_holder_name, 
               employee_bank_details.bank_name, employee_bank_details.acc_no, 
               employee_bank_details.ifsc_code, employee_bank_details.branch_name, 
               employee_bank_details.token, employee_bank_details.status 
        FROM employee 
        INNER JOIN employee_bank_details 
        ON employee.emp_id = employee_bank_details.emp_id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/style.css?v=1.0">
    <link rel="stylesheet" href="vendor/css/emp-bank.css?v=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>

<div id="divimg">
    <input type="text" id="search-bar" placeholder="Search by Emp ID or First Name" onkeyup="searchTable()">
    <table id="employee-table">
        <thead>
            <tr>
                <th>S No</th>
                <th>Emp. ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Account Holder Name</th>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>IFSC Code</th>
                <th>Branch</th>
                <th>Token</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sno = 1;
        while ($employee = mysqli_fetch_assoc($result)) {
            // Check if the image data is not empty
            if (!empty($employee['passbook_img'])) {
                // Convert binary data to base64
                $passbook_img_data = base64_encode($employee['passbook_img']);
                $passbook_img_src = "data:image/jpeg;base64,{$passbook_img_data}";
            } else {
                // Set a placeholder image if no data
                $passbook_img_src = 'path/to/placeholder/image.jpg'; // Update with your placeholder image path
            }

            // Standardize the status value (trim and convert to lowercase)
            $status = strtolower(trim($employee['status']));

            echo "<tr>";
            echo "<td>".$sno++."</td>";
            echo "<td>".$employee['emp_id']."</td>";
            echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
            echo "<td>
                    <a href='{$passbook_img_src}' download='{$employee['emp_id']}_{$employee['first_name']}.jpg'>
                        <img src='{$passbook_img_src}' alt='Passbook Image' style='width:100px; height:auto;'>
                    </a>
                  </td>";
            echo "<td>".$employee['bank_holder_name']."</td>";
            echo "<td>".$employee['bank_name']."</td>";
            echo "<td>".$employee['acc_no']."</td>";
            echo "<td>".$employee['ifsc_code']."</td>";
            echo "<td>".$employee['branch_name']."</td>";
            echo "<td>".$employee['token']."</td>";
            echo "<td>".ucfirst($status)."</td>";
            
            // Actions section
            echo "<td>";
            if ($status == 'pending') {
                echo "<a class='approve' href='bank-approve.php?emp_id={$employee['emp_id']}&token={$employee['token']}' onClick=\"return confirm('Are you sure you want to Approve the request?')\">Approve</a>  
                      <a class='cancel' href='bank-cancel.php?emp_id={$employee['emp_id']}&token={$employee['token']}' onClick=\"return confirm('Are you sure you want to Cancel the request?')\">Cancel</a>";
            } elseif ($status == 'approved') {
                echo "<a class='edit' href='bank-edit.php?emp_id={$employee['emp_id']}&token={$employee['token']}'>Edit</a> 
                      <a class='delete' href='bank-delete.php?emp_id={$employee['emp_id']}&token={$employee['token']}' 
      onClick=\"return confirm('Are you sure you want to cancel the bank details for Emp ID: {$employee['emp_id']} ({$employee['first_name']})?')\">Delete</a>";
            } elseif ($status == 'cancelled') {
                echo "<span>Cancelled</span>";
            }
            echo "</td>";
            echo "</tr>";
            
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    // Search function for filtering the table based on Emp ID or First Name
    function searchTable() {
        var input = document.getElementById("search-bar").value.toLowerCase();
        var table = document.getElementById("employee-table");
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var tdEmpId = tr[i].getElementsByTagName("td")[1];
            var tdName = tr[i].getElementsByTagName("td")[2];
            if (tdEmpId || tdName) {
                var empIdText = tdEmpId.textContent || tdEmpId.innerText;
                var nameText = tdName.textContent || tdName.innerText;
                if (empIdText.toLowerCase().indexOf(input) > -1 || nameText.toLowerCase().indexOf(input) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

</body>
</html>
