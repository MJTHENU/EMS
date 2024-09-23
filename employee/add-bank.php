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
        .error {
            color: red;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .custom-file-label {
            display: inline-block;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        .custom-file-label span {
            display: block;
        }

        .input--error {
            border-color: red;
        }
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
                        <input class="input--style-1" type="text" placeholder="Account Holder Name" name="bank_holder_name" id="bank_holder_name">
                        <span id="error-holder-name" class="error"></span>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Bank Name" name="bank_name" id="bank_name">
                        <span id="error-bank-name" class="error"></span>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Account Number" name="acc_no" id="acc_no">
                        <span id="error-account-number" class="error"></span>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="IFSC Code" name="ifsc_code" id="ifsc_code">
                        <span id="error-ifsc-code" class="error"></span>
                    </div>
                    <div class="input-group">
                        <input class="input--style-1" type="text" placeholder="Branch Name" name="branch_name" id="branch_name">
                        <span id="error-branch-name" class="error"></span>
                    </div>
                    
                    <div class="input-group">
                        <label for="passbook_img" class="custom-file-label">
                            <input type="file" name="passbook_img" id="passbook_img" accept=".jpeg,.png,.jpg,.pdf" style="display: none;" onchange="validateFile()">
                            <span id="placeholder-text">Bank Passbook FrontPage Upload</span>
                        </label>
                        <span id="error-passbook-img" class="error"></span>
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
//     function validateFile() {
//     var passbookImage = document.getElementById('passbook_img');
//     var allowedExtensions = /(\.jpeg|\.jpg|\.png)$/i;
//     var file = passbookImage.files[0];
//     var minSize = 300 * 1024; // 300 KB
//     var maxSize = 500 * 1024; // 500 KB

//     // Clear previous error messages
//     document.getElementById("error-passbook-img").textContent = '';

//     if (file) {
//         // Validate file type
//         if (!allowedExtensions.exec(file.name)) {
//             showError("error-passbook-img", "Only .jpeg, .jpg, and .png files are allowed.");
//             passbookImage.value = ''; // Clear the input
//             return false;
//         }

//         // Validate file size
//         if (file.size <= minSize) {
//             showError("error-passbook-img", "File size must be at least 300 KB.");
//             passbookImage.value = ''; // Clear the input
//             return false;
//         }

//         if (file.size => maxSize) {
//             showError("error-passbook-img", "File size must not exceed 500 KB.");
//             passbookImage.value = ''; // Clear the input
//             return false;
//         }
//     }
//     return true;
// }
function validateFile() {
    var passbookImage = document.getElementById('passbook_img');
    var allowedExtensions = /(\.jpeg|\.jpg|\.png)$/i;
    var file = passbookImage.files[0];
    var minSize = 300 * 1024; // 300 KB
    var maxSize = 500 * 1024; // 500 KB

    // Clear previous error messages
    document.getElementById("error-passbook-img").textContent = '';

    if (file) {
        // Validate file type
        if (!allowedExtensions.exec(file.name)) {
            showError("error-passbook-img", "Only .jpeg, .jpg, and .png files are allowed.");
            passbookImage.value = ''; // Clear the input
            document.getElementById('placeholder-text').textContent = 'Bank Passbook FrontPage Upload';
            return false;
        }

        // Validate file size
        if (file.size <= minSize) {
            showError("error-passbook-img", "File size must be at least 300 KB.");
            passbookImage.value = ''; // Clear the input
            document.getElementById('placeholder-text').textContent = 'Bank Passbook FrontPage Upload';
            return false;
        }

        if (file.size >= maxSize) {
            showError("error-passbook-img", "File size must not exceed 500 KB.");
            passbookImage.value = ''; // Clear the input
            document.getElementById('placeholder-text').textContent = 'Bank Passbook FrontPage Upload';
            return false;
        }

        // Show the selected file name
        document.getElementById('placeholder-text').textContent = file.name;
    } else {
        // Reset the placeholder text if no file is selected
        document.getElementById('placeholder-text').textContent = 'Bank Passbook FrontPage Upload';
    }
    return true;
}



    function validateForm() {
        let isValid = true;

        // Clear previous error messages
        clearErrors();

        // Validate form fields
        if (!validateTextField("bank_holder_name", "error-holder-name", "Account Holder Name is required")) {
            isValid = false;
        }
        if (!validateTextField("bank_name", "error-bank-name", "Bank Name is required")) {
            isValid = false;
        }
        if (!validateTextField("acc_no", "error-account-number", "Account Number is required")) {
            isValid = false;
        }
        if (!validateTextField("ifsc_code", "error-ifsc-code", "IFSC Code is required")) {
            isValid = false;
        }
        if (!validateTextField("branch_name", "error-branch-name", "Branch Name is required")) {
            isValid = false;
        }
        if (!validateTextField("passbook_img", "error-branch-name", "passbook_img is required")) {
            isValid = false;
        }

        // Validate file upload
        if (!validateFile()) {
            isValid = false;
        }

        return isValid;
    }

    function validateTextField(fieldId, errorId, errorMessage) {
        let field = document.getElementById(fieldId);
        if (field.value.trim() === "") {
            showError(errorId, errorMessage);
            field.classList.add('input--error');
            return false;
        }
        return true;
    }

    function showError(id, message) {
        document.getElementById(id).textContent = message;
    }

    function clearErrors() {
        let errorElements = document.querySelectorAll('.error');
        errorElements.forEach(function(el) {
            el.textContent = "";
        });

        let inputElements = document.querySelectorAll('.input--style-1');
        inputElements.forEach(function(el) {
            el.classList.remove('input--error');
        });
    }
</script>

</body>
</html>

