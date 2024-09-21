<?php
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

if (isset($_GET['emp_id']) && isset($_GET['token'])) {
    $emp_id = $_GET['emp_id'];
    $token = $_GET['token'];

    $sql = "SELECT employee.emp_id, employee.first_name, employee.last_name, 
                   employee_bank_details.passbook_img, employee_bank_details.bank_holder_name, 
                   employee_bank_details.bank_name, employee_bank_details.acc_no, 
                   employee_bank_details.ifsc_code, employee_bank_details.branch_name, 
                   employee_bank_details.token, employee_bank_details.status 
            FROM employee 
            INNER JOIN employee_bank_details 
            ON employee.emp_id = employee_bank_details.emp_id
            WHERE employee.emp_id = ? AND employee_bank_details.token = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "No bank details found for the given employee.";
        exit();
    }
} else {
    echo "Invalid request. Emp ID and Token are required.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bank_holder_name = $_POST['bank_holder_name'];
    $bank_name = $_POST['bank_name'];
    $acc_no = $_POST['acc_no'];
    $ifsc_code = $_POST['ifsc_code'];
    $branch_name = $_POST['branch_name'];

    if (isset($_FILES['passbook_img']) && $_FILES['passbook_img']['error'] == 0) {
        $passbook_img = file_get_contents($_FILES['passbook_img']['tmp_name']);
    } else {
        $passbook_img = $employee['passbook_img'];
    }

    $update_sql = "UPDATE employee_bank_details 
                   SET bank_holder_name = ?, bank_name = ?, acc_no = ?, ifsc_code = ?, branch_name = ?, passbook_img = ?
                   WHERE emp_id = ? AND token = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssss", $bank_holder_name, $bank_name, $acc_no, $ifsc_code, $branch_name, $passbook_img, $emp_id, $token);

    if ($update_stmt->execute()) {
        echo "Bank details updated successfully!";
        header("Location: emp-bank.php?success=1");
    } else {
        echo "Error updating bank details: " . $conn->error;
    }
}

$passbook_img_src = (!empty($employee['passbook_img'])) ? "data:image/jpeg;base64," . base64_encode($employee['passbook_img']) : 'path/to/placeholder/image.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/style.css?v=1.0">
    <link rel="stylesheet" href="vendor/css/bank-edit.css?v=1.0">
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>

<div class="wrapper">
    <h2>Edit Bank Details for Employee: <?php echo $employee['first_name'] . " " . $employee['last_name']; ?></h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label for="bank_holder_name">Account Holder Name</label>
            <input type="text" id="bank_holder_name" name="bank_holder_name" value="<?php echo $employee['bank_holder_name']; ?>" class="input--style-1" required>
        </div>

        <div class="input-group">
            <label for="bank_name">Bank Name</label>
            <input type="text" id="bank_name" name="bank_name" value="<?php echo $employee['bank_name']; ?>" class="input--style-1" required>
        </div>

        <div class="input-group">
            <label for="acc_no">Account Number</label>
            <input type="text" id="acc_no" name="acc_no" value="<?php echo $employee['acc_no']; ?>" class="input--style-1" required>
        </div>

        <div class="input-group">
            <label for="ifsc_code">IFSC Code</label>
            <input type="text" id="ifsc_code" name="ifsc_code" value="<?php echo $employee['ifsc_code']; ?>" class="input--style-1" required>
        </div>

        <div class="input-group">
            <label for="branch_name">Branch Name</label>
            <input type="text" id="branch_name" name="branch_name" value="<?php echo $employee['branch_name']; ?>" class="input--style-1" required>
        </div>

        <div class="input-group">
            <label for="passbook_img">Bank Passbook Image</label>
            <input type="file" id="passbook_img" name="passbook_img" class="input--style-1">
            <img src="<?php echo $passbook_img_src; ?>" alt="Passbook Image" class="passbook-img">
        </div>

        <div class="button-container">
            <button type="submit" class="btn btn--blue">Update Details</button>
            <a href="employee-list.php" class="btn btn--red">Cancel</a>
        </div>
    </form>
</div>

<!-- <?php include('vendor/inc/footer.php') ?> -->
</body>
</html>
