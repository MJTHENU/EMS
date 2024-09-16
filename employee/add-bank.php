<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

$id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
require_once('vendor/inc/connection.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel ="stylesheet" href = "vendor/css/bank.css?v=1.0">
    <style>
      
    </style>
</head>
<body>
<?php include('vendor/inc/nav.php') ?>

<div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
    <div class="wrapper wrapper--w680">
        <div class="card card-1">
            <div class="card-heading"></div>
            <div class="card-body">
                <h2 class="h2">Add Bank Details</h2>
                <form action="vendor/process/bank-process.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">

                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Account Holder Name" name="bank_holder_name">
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Bank Name" name="bank_name">
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Account Number" name="acc_no">
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="IFSC Code" name="ifsc_code">
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Branch Name" name="branch_name">
                    </div>
                    
                    <div class="input-group">
                        <label for="passbook_img" class="custom-file-label">
                            <input type="file" name="passbook_img" id="passbook_img" accept="image/*" style="display: none;">
                            <span id="placeholder-text">Bank Passbook FrontPage Upload</span>
                        </label>
                    </div>


                    <div class="p-t-20">
                        <button class="btn btn--radius btn--green" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        var holderName = document.querySelector('input[name="bank_holder_name"]').value;
        var bankName = document.querySelector('input[name="bank_name"]').value;
        var accountNumber = document.querySelector('input[name="acc_no"]').value;
        var ifscCode = document.querySelector('input[name="ifsc_code"]').value;
        var branch_name = document.querySelector('input[name="branch_name"]').value;
        var passbookImage = document.querySelector('input[name="passbook_img"]').files.length;

        if (holderName.trim() === "" || bankName.trim() === "" || accountNumber.trim() === "" || ifscCode.trim() === "" || branch_name.trim() === "" || passbookImage === 0) {
            alert("All fields are required.");
            return false;
        }
        
        return true;
    }
</script>

</body>
</html>
