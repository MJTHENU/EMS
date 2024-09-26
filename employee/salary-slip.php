<?php 
session_start(); 

// Check if the user is logged in
if (!isset($_SESSION['emp_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit; 
}

// Include database connection
require_once('vendor/inc/connection.php'); 

// Get the logged-in employee ID from the session
$emp_id = $_SESSION['emp_id'];

// Set the pay period dynamically
$pay_period = date('Y-M'); // Format: 2024-Apr

// Fetch employee details, bank details, and attendance for the logged-in employee
$query = "
    SELECT 
        e.emp_id, e.first_name, e.last_name, e.role, 
        b.bank_name, b.acc_no,
        (SELECT COUNT(*) FROM attendance a 
         WHERE a.emp_id = e.emp_id 
           AND DATE_FORMAT(a.att_date, '%Y-%m') = ?
           AND a.status = 'present') AS present_days,
        (SELECT COUNT(*) FROM attendance 
         WHERE emp_id = e.emp_id 
           AND DATE_FORMAT(att_date, '%Y-%m') = ?) AS total_working_days,
        s.basic_pay, s.medical_allowance, s.special_allowance, s.provident_fund, s.professional_tax
    FROM employee e
    LEFT JOIN employee_bank_details b ON e.emp_id = b.emp_id
    LEFT JOIN salary s ON e.emp_id = s.emp_id
    WHERE e.emp_id = ?";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $pay_period, $pay_period, $emp_id);
$stmt->execute();
$stmt->bind_result($emp_id, $first_name, $last_name, $role, $bank_name, $acc_no, $present_days, $total_working_days, $basic_pay, $medical_allowance, $special_allowance, $provident_fund, $professional_tax);

if (!$stmt->fetch()) {
    echo "Employee details not found.";
    exit; 
}
$stmt->close(); 

// Calculate totals
$total_earnings = $basic_pay + $medical_allowance + $special_allowance;
$total_deductions = $provident_fund + $professional_tax;
$net_pay = $total_earnings - $total_deductions;

// Close the connection
$conn->close(); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip for <?= htmlspecialchars($first_name) ?></title>
    <link rel="stylesheet" href="vendor/css/salary.css?v=1.0">
    <?php include('vendor/inc/head.php'); ?>    
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>
    <div class="payslip">
        <div class="header">
            <img src="vendor/images/Logo.png" alt="Company Logo">
            <div class="address">
                <h1>KiteCareer</h1>
                <p>472/126J, Railway feeder road, Tenkasi - 627811</p>
            </div>
        </div>
        <div class="month">
            Pay Slip For <?= date('F - Y', strtotime("-1 month")) ?>
        </div>


        <section class="employee-info">
            <div>
                <p>Employee ID: <span><?= htmlspecialchars($emp_id) ?></span></p>
                <p>Pay Period: <span><?= htmlspecialchars($pay_period) ?></span></p>
                <p>Bank Name: <span><?= htmlspecialchars($bank_name) ?></span></p>
                <p>Payable Days: <span><?= htmlspecialchars($total_working_days) ?></span></p>
            </div>
            <div>                
                <p>Employee Name: <span><?= htmlspecialchars($first_name . ' ' . $last_name) ?></span></p>
                <p>Designation: <span><?= htmlspecialchars($role) ?></span></p>
                <p>Account No: <span><?= htmlspecialchars($acc_no) ?></span></p>
                <p>Worked Days: <span><?= htmlspecialchars($present_days) ?></span></p>
            </div>
        </section>
        <section class="salary-details">
            <table>
                <thead>
                    <tr>
                        <th>Earnings</th>
                        <th>Amount</th>
                        <th>Deductions</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Pay</td>
                        <td><?= number_format($basic_pay, 2) ?></td>
                        <td>Provident Fund</td>
                        <td><?= number_format($provident_fund, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Medical Allowance</td>
                        <td><?= number_format($medical_allowance, 2) ?></td>
                        <td>Professional Tax</td>
                        <td><?= number_format($professional_tax, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Special Allowance</td>
                        <td><?= number_format($special_allowance, 2) ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Total Earnings</td>
                        <td><?= number_format($total_earnings, 2) ?></td>
                        <td>Total Deductions</td>
                        <td><?= number_format($total_deductions, 2) ?></td>
                    </tr>
                    <tr class="net-pay-row">
                        <td colspan="4">Net Pay: <?= number_format($net_pay, 2) ?></td>
                    </tr>
                    <tr class="net-pay-row">
                        <td colspan="4">In words: <?= htmlspecialchars($netPayInWords) ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
