<?php
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$emp_id = $firstname = $lastname = $email = $password = $contact = $address = $gender = $birthday = $role = $qualification = $whatsapp = $status = $type = $img = $salary = '';
$salary = '0.00'; // Set the default salary to 0.00

// Handle form submission
if (isset($_POST['add'])) {
    // Convert the date from DD-MM-YYYY to YYYY-MM-DD for saving
    if (!empty($_POST['date_of_birth'])) {
        $birthday = date('Y-m-d', strtotime($_POST['date_of_birth']));
    }

    // Get input values and escape for SQL
    $emp_id = trim(mysqli_real_escape_string($conn, $_POST['emp_id']));
    $firstname = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $lastname = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = isset($_POST['password']) && !empty(trim($_POST['password']))
     ? password_hash(trim(mysqli_real_escape_string($conn, $_POST['password'])), PASSWORD_BCRYPT) : password_hash('kite@123', PASSWORD_BCRYPT);
    $contact = trim(mysqli_real_escape_string($conn, $_POST['contact']));
    $whatsapp = trim(mysqli_real_escape_string($conn, $_POST['whatsapp_no']));
    $address = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $gender = trim(mysqli_real_escape_string($conn, $_POST['gender']));
    $role = trim(mysqli_real_escape_string($conn, $_POST['role']));
    $qualification = trim(mysqli_real_escape_string($conn, $_POST['qualification']));
    $type = trim(mysqli_real_escape_string($conn, $_POST['type']));
    $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
    // $salary = trim(mysqli_real_escape_string($conn, $_POST['salary']));
    $salary = isset($_POST['salary']) && !empty(trim($_POST['salary'])) ? trim(mysqli_real_escape_string($conn, $_POST['salary'])) : '0.00';

    // Handle file upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $img = mysqli_real_escape_string($conn, file_get_contents($_FILES['img']['tmp_name']));
    } else {
        echo "<script>alert('Error uploading image');</script>";
        exit();
    }

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

    if (!$check_email_result) {
        die("Error checking email: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_email_result) > 0) {
        echo "<script>alert('Email already exists');</script>";
        exit();
    }

    // Check for duplicate emp_id
    $check_emp_id_query = "SELECT * FROM employee WHERE emp_id='$emp_id'";
    $check_emp_id_result = mysqli_query($conn, $check_emp_id_query);

    if (!$check_emp_id_result) {
        die("Error checking employee ID: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_emp_id_result) > 0) {
        echo "<script>alert('Employee ID already exists');</script>";
        exit();
    }

    // Prepare and execute SQL query for employee
    $insert_employee_sql = "INSERT INTO employee (emp_id, first_name, last_name, email, password, date_of_birth, gender, contact, whatsapp_no, address, role, qualification, img, type, status) 
                            VALUES ('$emp_id', '$firstname', '$lastname', '$email', '$password', '$birthday', '$gender', '$contact', '$whatsapp', '$address', '$role', '$qualification', '$img', '$type', '$status')";

    if (mysqli_query($conn, $insert_employee_sql)) {
        // Insert salary into the salary table
        $insert_salary_sql = "INSERT INTO salary (emp_id, salary) VALUES ('$emp_id', '$salary')";

        if (mysqli_query($conn, $insert_salary_sql)) {
            echo "<script>alert('Successfully Added'); window.location.href='viewemp.php';</script>";
        } else {
            echo "Error adding salary record: " . mysqli_error($conn);
        }
    } else {
        echo "Error adding employee record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/emp-edit.css?v=1.0">
</head>
<style>

</style>
<body>
    <?php include('vendor/inc/nav.php'); ?>
    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2" style="font-family: 'Montserrat', sans-serif; font-size: 25px; text-align: center; color: #777; padding: 10px;">Add New Employee</h2>
                    <hr>
                    <form id="registration" action="add-employee.php" method="POST" enctype="multipart/form-data">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style" type="text" name="emp_id" placeholder="ID" value="<?php echo htmlspecialchars($emp_id); ?>" style="margin-top: 80px;" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div>
                                    <input id="profile" class="custom-file-input" type="file" name="img" accept="image/*" onchange="previewImage(event)">
                                    <label for="profile" class="custom-file-label">
                                        <img id="profile-preview" class="image--cover" src="./vendor/images/profile.png" alt="Profile Image Preview">
                                        <span id="placeholder-text">Upload Profile</span>
                                    </label>
                                </div>
                            </div>
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
                                    <input class="input--style-1" type="date" name="date_of_birth" placeholder="Date of Birth (DD-MM-YYYY)" value="<?php echo htmlspecialchars(date('d-m-Y', strtotime($birthday))); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="gender" required>
                                        <option value="" <?php echo ($gender == '') ? 'selected' : ''; ?>>Select</option>
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
                                        <option value="2" <?php echo ($type == '2') ? 'selected' : ''; ?>>Employee</option>
                                        <option value="3" <?php echo ($type == '3') ? 'selected' : ''; ?>>Trainee</option>
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
                            <input class="input--style-1" type="text" name="salary" placeholder="Salary" value="<?php echo htmlspecialchars($salary ?? '0.00'); ?>" required>
                        </div>

                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="add">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profile-preview');
                output.src = reader.result;
                document.getElementById('placeholder-text').style.display = 'none'; // Hide placeholder text when an image is selected
                output.style.display = 'block'; // Display the preview image
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
    <script>
    // Get URL query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');

    // Set the default value for the type dropdown
    if (type === 'employee') {
        document.querySelector('select[name="type"]').value = '2'; // Employee
    } else if (type === 'trainee') {
        document.querySelector('select[name="type"]').value = '3'; // Trainee
    }
</script>

</body>
</html>
