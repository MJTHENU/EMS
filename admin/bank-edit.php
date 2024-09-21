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
    $status = $_POST['status'];

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/css/style.css?v=1.0">
    <link rel="stylesheet" href="vendor/css/bank-edit.css?v=1.0">
</head>
<body>
    
<?php include('vendor/inc/nav.php') ?>

<div class="page-wrapper">
    <div class="wrapper">
        <div class="card">
            <div class="card-body">
                <h2 class="h2">Edit Bank Details for Employee: <?php echo $employee['first_name'] . " " . $employee['last_name']; ?></h2>
                <form action="" method="POST" enctype="multipart/form-data" class="bank-edit-form">
                    <div class="row main">
                        <div class="col left">
                            <div class="custom-file-upload">
                                <img src="<?php echo $passbook_img_src; ?>" alt="Passbook Image" class="passbook-img">
                                <div class="camera-icon" onclick="document.getElementById('passbook-upload').click();">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <input type="file" id="passbook-upload" name="passbook_img" accept=".jpeg, .jpg, .png">
                            </div>

                        </div>
                        <div class="col right">
                            <div class="input-group">
                                <p>Account Holder Name</p>
                                <input type="text" name="bank_holder_name" value="<?php echo $employee['bank_holder_name']; ?>" class="input--style-1" required>
                            </div>
                            <div class="input-group">
                                <p>Bank Name</p>
                                <input type="text" name="bank_name" value="<?php echo $employee['bank_name']; ?>" class="input--style-1" required>
                            </div>
                            <div class="input-group">
                                <p>Account Number</p>
                                <input type="text" name="acc_no" value="<?php echo $employee['acc_no']; ?>" class="input--style-1" required>
                            </div>
                            <div class="input-group">
                                <p>IFSC Code</p>
                                <input type="text" name="ifsc_code" value="<?php echo $employee['ifsc_code']; ?>" class="input--style-1" required>
                            </div>
                            <div class="input-group">
                                <p>Branch Name</p>
                                <input type="text" name="branch_name" value="<?php echo $employee['branch_name']; ?>" class="input--style-1" required>
                            </div>
                            <div class="input-group">
                                <p>Status</p>
                                <select name="status" class="input--style-1">
                                    <option value="pending" <?php echo ($employee['status'] == 'pending' || empty($employee['status'])) ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo ($employee['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="cancelled" <?php echo ($employee['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="button-container">
                                <button type="submit" class="btn btn--blue">Update Details</button>
                                <a href="employee-list.php" class="btn btn--red">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Trigger file input click when camera icon is clicked
    document.querySelector(".camera-icon").addEventListener("click", function() {
        document.getElementById("passbook-upload").click(); // Trigger file input click
    });

    // File input change event to handle validations
    document.getElementById("passbook-upload").addEventListener("change", function() {
        const file = this.files[0]; // Get the selected file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']; // Allowed file types
        
        if (file) {
            const fileType = file.type; // Get file type
            const fileSizeInKB = file.size / 1024; // Convert file size to KB

            // Validate file type
            if (!allowedTypes.includes(fileType)) {
                alert("Only JPEG, JPG, or PNG files are allowed.");
                this.value = ""; // Clear the input
                return;
            }
            
            // Validate file size
            if (fileSizeInKB < 300 || fileSizeInKB > 350) {
                alert("File size must be between 300KB and 350KB.");
                this.value = ""; // Clear the input
                return;
            }
            
            // If both validations pass, optionally display the file name
            const fileName = file.name;
            document.getElementById("file-chosen").textContent = fileName; // Show file name (optional)
        }
    });
</script>

</body>
</html>
