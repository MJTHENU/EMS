<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$emp_id = $firstname = $lastname = $email = $password = $contact = $address = $gender = $birthday = $role = $qualification = $whatsapp = $status = $type = $img = $salary = '';

// Function to generate unique employee ID
function generateEmpID($conn) {
    do {
        $emp_id = 'EMP' . rand(1000, 9999); // Generate a random ID
        $result = mysqli_query($conn, "SELECT emp_id FROM employee WHERE emp_id='$emp_id'");
    } while (mysqli_num_rows($result) > 0);
    return $emp_id;
}

// Handle form submission
if (isset($_POST['add'])) {
    // Generate unique emp_id
    $emp_id = generateEmpID($conn);

    // Sanitize input
    $firstname = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $lastname = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));
    $birthday = trim(mysqli_real_escape_string($conn, $_POST['date_of_birth']));
    $contact = trim(mysqli_real_escape_string($conn, $_POST['contact']));
    $whatsapp = trim(mysqli_real_escape_string($conn, $_POST['whatsapp_no']));
    $address = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $gender = trim(mysqli_real_escape_string($conn, $_POST['gender']));
    $role = trim(mysqli_real_escape_string($conn, $_POST['role']));
    $qualification = trim(mysqli_real_escape_string($conn, $_POST['qualification']));
    $type = trim(mysqli_real_escape_string($conn, $_POST['type']));
    $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
    $img = trim(mysqli_real_escape_string($conn, $_POST['img']));
    $salary = trim(mysqli_real_escape_string($conn, $_POST['salary']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit();
    }

    // Validate phone numbers
    if (!preg_match("/^\d{10}$/", $contact)) {
        echo "<script>alert('Contact number must be 10 digits');</script>";
        exit();
    }
    if (!preg_match("/^\d{10}$/", $whatsapp)) {
        echo "<script>alert('Whatsapp number must be 10 digits');</script>";
        exit();
    }

    // Check for duplicate email
    $check_email_query = "SELECT * FROM employee WHERE email='$email'";
    $check_email_result = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($check_email_result) > 0) {
        echo "<script>alert('Email already exists');</script>";
        exit();
    }

    // Prepare SQL query
    $insert_sql = "INSERT INTO employee (emp_id, first_name, last_name, email, password, date_of_birth, gender, contact, whatsapp_no, address, role, qualification, img, type, status, salary) 
                    VALUES ('$emp_id', '$firstname', '$lastname', '$email', '$password', '$birthday', '$gender', '$contact', '$whatsapp', '$address', '$role', '$qualification', '$img', '$type', '$status', '$salary')";

    // Execute SQL query
    if (mysqli_query($conn, $insert_sql)) {
        echo ("<script>
            alert('Successfully Added');
            window.location.href='viewemp.php';
            </script>");
    } else {
        echo "Error adding record: " . mysqli_error($conn);
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
                    <h2 class="h2" style="font-family: 'Montserrat', sans-serif; font-size: 25px; text-align: center; color: #777; padding: 10px 0;">Add New Employee</h2>
                    <form id="registration" action="add-employee.php" method="POST">
                        <div class="input-group">
                            <input class="input--style-1" type="text" name="emp_id" placeholder="ID" value="<?php echo htmlspecialchars($emp_id); ?>" required>
                        </div>
                        <div class="input-group">
                            <input class="input--style-1" type="browse" name="img" placeholder="Profile" value="<?php echo htmlspecialchars($img); ?>" required>
                        </div>
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
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="role" placeholder="Role" value="<?php echo htmlspecialchars($role); ?>">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="qualification" placeholder="Qualification" value="<?php echo htmlspecialchars($qualification); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="type" required>
                                        <option value="1" <?php echo ($type == '1') ? 'selected' : ''; ?>>1</option>
                                        <option value="2" <?php echo ($type == '2') ? 'selected' : ''; ?>>2</option>
                                        <option value="3" <?php echo ($type == '3') ? 'selected' : ''; ?>>3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="status" required>
                                        <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="text" name="salary" placeholder="Salary" value="<?php echo htmlspecialchars($salary); ?>" required>
                        </div>

                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="add">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
