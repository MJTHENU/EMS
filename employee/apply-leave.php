<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

$id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
require_once('vendor/inc/connection.php');
$sql = "SELECT * FROM `employee` where emp_id = '$id'";
$result = mysqli_query($conn, $sql);
$employee = mysqli_fetch_array($result);
$empName = ($employee['first_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/leave.css?v=1.0">
    <style>
        
    </style>
    <script>
        // Function to show alert messages
        function showAlerts(errors) {
            errors.forEach(function(error) {
                alert(error);
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            <?php
            if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
                $errors = json_encode($_SESSION['errors']);
                echo "showAlerts($errors);";
                // Clear errors after showing them
                unset($_SESSION['errors']);
            }
            if (isset($_SESSION['success_message'])) {
                $success_message = json_encode($_SESSION['success_message']);
                echo "alert($success_message);";
                unset($_SESSION['success_message']);
            }
            ?>
        });
    </script>
</head>
<body>
<?php include('vendor/inc/nav.php') ?>
    <div class="page-wrapper">
        <div class="form-section">
            <h2>Apply for Leave</h2>
            <form action="vendor/process/leave-process.php?emp_id=<?php echo $id ?>" method="POST">
                <div class="form-group-row">
                    <!-- First Row -->
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <input type="text" id="reason" name="reason" placeholder="Reason" required>
                    </div>
                </div>
                <div class="form-group-row">
                    <!-- Second Row -->
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" required>
                    </div>
                </div>
                <div class="form-group form-group-radio">
                    <label>Leave Type</label>
                    <div class="radio-group">
                        <input type="radio" id="full_day" name="leave_type" value="Full Day" checked required>
                        <label for="full_day">Full Day</label>
                        <input type="radio" id="half_day" name="leave_type" value="Half Day" required>
                        <label for="half_day">Half Day</label>
                    </div>
                </div>

                <div class="form-button-group">
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h2>Leave Details</h2>
            <table>
                <tr>
                    <th>Emp. ID</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Days</th>
                    <th>Reason</th>
                    <th>Leave Type</th>
                    <th>Status</th>
                </tr>

                <?php
                    $sql = "SELECT employee.emp_id, employee.first_name, employee.last_name, employee_leave.start_date, employee_leave.end_date, employee_leave.reason, employee_leave.leave_type, employee_leave.status 
                            FROM employee, employee_leave 
                            WHERE employee.emp_id = '$id' AND employee_leave.emp_id = '$id' 
                            ORDER BY employee_leave.token";
                    $result = mysqli_query($conn, $sql);
                    while ($employee = mysqli_fetch_assoc($result)) {
                        $date1 = new DateTime($employee['start_date']);
                        $date2 = new DateTime($employee['end_date']);
                        $interval = $date1->diff($date2);

                        echo "<tr>";
                        echo "<td>".$employee['emp_id']."</td>";
                        echo "<td>".$employee['first_name']." ".$employee['last_name']."</td>";
                        echo "<td>".$employee['start_date']."</td>";
                        echo "<td>".$employee['end_date']."</td>";
                        echo "<td>".$interval->days."</td>";
                        echo "<td>".$employee['reason']."</td>";
                        echo "<td>".$employee['leave_type']."</td>";
                        echo "<td>".$employee['status']."</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>
