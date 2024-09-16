<?php
session_start();

require_once('../../vendor/inc/connection.php');

$email = $_POST['email'];
$password = $_POST['password'];

// Prepare the SQL statement to prevent SQL injection
$sql = "SELECT emp_id, password, type FROM employee WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $employee = $result->fetch_assoc();
    
    // Verify the hashed password
    if (password_verify($password, $employee['password'])) {
        // Check if the role is 3 (trainee)
        if ($employee['type'] == 3) {
            $_SESSION['emp_id'] = $employee['emp_id'];
            header("Location: ../../index.php?emp_id=" . $employee['emp_id']);
            exit();
        } else {
            echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('You are not a trainee')
            window.location.href='javascript:history.go(-1)';
            </SCRIPT>");
        }
    } else {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Invalid Email or Password')
        window.location.href='javascript:history.go(-1)';
        </SCRIPT>");
    }
} else {
    echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Invalid Email or Password')
    window.location.href='javascript:history.go(-1)';
    </SCRIPT>");
}

$stmt->close();
$conn->close();
?>
