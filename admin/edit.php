<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$firstname = $lastname = $email = $contact = $address = $gender = $birthday = $role = $qualification = $whatsapp = $status = '';
// Fetch employee data if emp_id is provided
if (isset($_GET['emp_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['emp_id']);
    $sql = "SELECT * FROM employee WHERE emp_id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $res = mysqli_fetch_assoc($result);

        if ($res) {
            $firstname = $res['first_name'];
            $lastname = $res['last_name'];
            $email = $res['email'];
            $contact = $res['contact'];
            $address = $res['address'];
            $gender = $res['gender'];
            $birthday = $res['date_of_birth'];
            $role = isset($res['role']) ? $res['role'] : ''; // Handle missing data
            $qualification = isset($res['qualification']) ? $res['qualification'] : ''; // Handle missing data
            $whatsapp = isset($res['whatsapp_no']) ? $res['whatsapp_no'] : ''; // Handle missing data
            $status = isset($res['status']) ? $res['status'] : '';
        } else {
            die("Employee not found.");
        }
    } else {
        die("Error fetching employee data: " . mysqli_error($conn));
    }
} else {
    die("Employee ID not provided.");
}

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $firstname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $birthday = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE employee SET 
                    first_name='$firstname',
                    last_name='$lastname',
                    email='$email',
                    date_of_birth='$birthday',
                    gender='$gender',
                    contact='$contact',
                    whatsapp_no='$whatsapp',
                    address='$address',
                    role='$role',
                    qualification='$qualification'
                    status='$status'
                    WHERE emp_id='$id'";

    if (mysqli_query($conn, $update_sql)) {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('Successfully Updated')
            window.location.href='viewemp.php';
            </SCRIPT>");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/emp-edit.css">
</head>
<style>
.input--style {
    width: 100%; /* Make the select box span the full width of its container */
    padding: 10px; /* Add some padding for better appearance */
    border: 1px solid #ccc; /* Border styling */
    border-radius: 4px; /* Rounded corners */
    font-size: 16px; /* Font size for better readability */
    box-sizing: border-box; /* Include padding and border in the element's total width and height */
}

.input--style option {
    padding: 10px; /* Add padding inside options for better readability */
}
.p-t-20 {
    text-align: center; /* Center-aligns the content within the container */
}

.btn {
    display: inline-block; /* Ensure button is treated as inline-block for centering */
}

</style>
<body>    
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2" style="font-family: 'Montserrat', sans-serif; font-size: 25px; text-align: center; color: #777; padding: 10px 0;">Update Employee Info</h2>
                    <form id="registration" action="edit.php" method="POST">
                        <input type="hidden" name="emp_id" value="<?php echo htmlspecialchars($id); ?>">

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($firstname); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($lastname); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" name="date_of_birth" placeholder="Date Of Birth" value="<?php echo htmlspecialchars($birthday); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                            <div class="input-group">
                                <select class="input--style" name="gender" required>
                                    <option value="male" <?php echo ($gender == 'male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="female" <?php echo ($gender == 'female') ? 'selected' : ''; ?>>Female</option>
                                    <!-- <option value="other" <?php echo ($gender == 'other') ? 'selected' : ''; ?>>Other</option> -->
                                </select>
                            </div>

                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="contact" placeholder="Mobile No" value="<?php echo htmlspecialchars($contact); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="whatsapp_no" placeholder="Whatsapp No" value="<?php echo htmlspecialchars($whatsapp); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($address); ?>" required>
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="text" name="role" placeholder="Role" value="<?php echo htmlspecialchars($role); ?>">
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="text" name="qualification" placeholder="Qualification" value="<?php echo htmlspecialchars($qualification); ?>">
                        </div>

                        <div class="input-group">
                                <select class="input--style" name="status" required>
                                    <option value="active" <?php echo ($gender == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($gender == 'inactive') ? 'selected' : ''; ?>>InActive</option>
                                </select>
                            </div>


                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
