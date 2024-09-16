<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

$id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');

// Include the database connection file
require_once('../inc/connection.php');

// Handle form submission for bank details application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $bank_holder_name = mysqli_real_escape_string($conn, $_POST['bank_holder_name']);
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $acc_no = mysqli_real_escape_string($conn, $_POST['acc_no']);
    $ifsc_code = mysqli_real_escape_string($conn, $_POST['ifsc_code']);
    $branch_name = mysqli_real_escape_string($conn, $_POST['branch_name']);

    // Validate required fields
    if (empty($bank_holder_name) || empty($bank_name) || empty($acc_no) || empty($ifsc_code) || empty($branch_name) || empty($_FILES['passbook_img']['name'])) {
        echo "All fields are required.";
        exit();
    }

    // Get emp_id from session
    $emp_id = $_SESSION['emp_id'];

    // Check if emp_id exists in employee table
    $check_emp_id_query = "SELECT * FROM employee WHERE emp_id = '$emp_id'";
    $check_emp_id_result = mysqli_query($conn, $check_emp_id_query);
    
    if (mysqli_num_rows($check_emp_id_result) == 0) {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('Invalid Employee ID.')
            window.location.href='../../add-bank.php?emp_id=$emp_id';
            </SCRIPT>");
        exit();
    }

    // Check if bank details already exist for the emp_id
    $check_bank_details_query = "SELECT * FROM employee_bank_details WHERE emp_id = '$emp_id'";
    $check_bank_details_result = mysqli_query($conn, $check_bank_details_query);

    if (mysqli_num_rows($check_bank_details_result) > 0) {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('Bank details already exist for this employee.')
            window.location.href='../../add-bank.php?emp_id=$emp_id';
            </SCRIPT>");
        exit();
    }

    // Generate a unique token value
    $token = uniqid();

    // Handle file upload for passbook image
    $passbook_img = $_FILES['passbook_img']['tmp_name'];
    $passbook_img_data = file_get_contents($passbook_img); // Read file content as binary data

    // Insert bank details into the database
    $sql = "INSERT INTO `employee_bank_details` (token, emp_id, bank_holder_name, bank_name, acc_no, ifsc_code, branch_name, passbook_img) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $token, $emp_id, $bank_holder_name, $bank_name, $acc_no, $ifsc_code, $branch_name, $passbook_img_data);

    if ($stmt->execute()) {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Successfully Updated')
        window.location.href='../../my-profile.php?emp_id=$emp_id';
        </SCRIPT>");
        exit(); // Ensure script termination after redirection
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
