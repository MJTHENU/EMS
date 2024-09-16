<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$emp_id = $firstname = $lastname = $email = $password = $contact = $address = $gender = $birthday = $role = $qualification = $whatsapp = $status = $type = $img = $salary = '';

// Fetch employee and salary data
if (isset($_GET['emp_id'])) {
    $emp_id = trim(mysqli_real_escape_string($conn, $_GET['emp_id']));
    $query = "SELECT e.*, s.salary FROM employee e LEFT JOIN salary s ON e.emp_id = s.emp_id WHERE e.emp_id='$emp_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $firstname = $row['first_name'];
        $lastname = $row['last_name'];
        $email = $row['email'];
        $birthday = $row['date_of_birth'];
        $contact = $row['contact'];
        $whatsapp = $row['whatsapp_no'];
        $address = $row['address'];
        $gender = $row['gender'];
        $role = $row['role'];
        $qualification = $row['qualification'];
        $type = $row['type'];
        $status = $row['status'];
        $salary = isset($row['salary']) ? $row['salary'] : '';
        $img = isset($row['img']) ? base64_encode($row['img']) : ''; // Encode image data
    } else {
        echo "<script>alert('Employee not found'); window.location.href='viewemp.php';</script>";
        exit();
    }
}

// Handle form submission
if (isset($_POST['update'])) {
    // Get input values
    $emp_id = trim(mysqli_real_escape_string($conn, $_POST['emp_id']));
    $img = isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK ? file_get_contents($_FILES['img']['tmp_name']) : null;
    $firstname = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $lastname = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = !empty($_POST['password']) ? password_hash(trim(mysqli_real_escape_string($conn, $_POST['password'])), PASSWORD_BCRYPT) : '';
    $birthday = trim(mysqli_real_escape_string($conn, $_POST['date_of_birth']));
    $contact = trim(mysqli_real_escape_string($conn, $_POST['contact']));
    $whatsapp = trim(mysqli_real_escape_string($conn, $_POST['whatsapp_no']));
    $address = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $gender = trim(mysqli_real_escape_string($conn, $_POST['gender']));
    $role = trim(mysqli_real_escape_string($conn, $_POST['role']));
    $qualification = trim(mysqli_real_escape_string($conn, $_POST['qualification']));
    $type = trim(mysqli_real_escape_string($conn, $_POST['type']));
    $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
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

    // Check email uniqueness
    $check_email_query = "SELECT * FROM employee WHERE email='$email' AND emp_id != '$emp_id'";
    $check_email_result = mysqli_query($conn, $check_email_query);
    if (mysqli_num_rows($check_email_result) > 0) {
        echo "<script>alert('Email already exists');</script>";
        exit();
    }

    // Prepare SQL query for employee table
    $update_employee_sql = "UPDATE employee SET 
                            first_name='$firstname', 
                            last_name='$lastname', 
                            email='$email', 
                            date_of_birth='$birthday', 
                            gender='$gender', 
                            contact='$contact', 
                            whatsapp_no='$whatsapp', 
                            address='$address', 
                            role='$role', 
                            qualification='$qualification', 
                            type='$type', 
                            status='$status'";
    
    if (!empty($password)) {
        $update_employee_sql .= ", password='$password'";
    }
    
    if ($img !== null) {
        $update_employee_sql .= ", img='" . mysqli_real_escape_string($conn, $img) . "'";
    }

    $update_employee_sql .= " WHERE emp_id='$emp_id'";
    
    // Execute SQL query for employee table
    if (!mysqli_query($conn, $update_employee_sql)) {
        echo "Error updating employee record: " . mysqli_error($conn);
        exit();
    }

    // Prepare SQL query for salary table
    $update_salary_sql = "UPDATE salary SET salary='$salary' WHERE emp_id='$emp_id'";
    
    // Execute SQL query for salary table
    if (mysqli_query($conn, $update_salary_sql)) {
        echo ("<script>
            alert('Successfully Updated');
            window.location.href='viewemp.php';
            </script>");
    } else {
        echo "Error updating salary record: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/emp-edit.css?v=1.0">
</head>

<body>
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2" style="font-family: 'Montserrat', sans-serif; font-size: 25px; text-align: center; color: #777; padding: 10px;">Edit Employee</h2>
                    <hr>
                    <form id="registration" action="edit.php?emp_id=<?php echo htmlspecialchars($emp_id); ?>" method="POST" enctype="multipart/form-data">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style input-margin-top" type="text" name="emp_id" placeholder="ID" value="<?php echo htmlspecialchars($emp_id); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="">
                                    <input id="profile" class="custom-file-input" type="file" name="img" accept="image/*" onchange="previewImage(event)">
                                    <label for="profile" class="custom-file-label">
                                        <img id="profile-preview" class="image--cover" src="<?php echo !empty($img) ? 'data:image/jpeg;base64,' . htmlspecialchars($img) : ''; ?>" alt="Profile Image Preview" style="display: <?php echo !empty($img) ? 'block' : 'none'; ?>;">
                                        <span id="placeholder-text" style="display: <?php echo !empty($img) ? 'none' : 'inline-block'; ?>;">Upload Profile</span>
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
                            <input class="input--style-1" type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" name="date_of_birth" placeholder="Date Of Birth" value="<?php echo htmlspecialchars($birthday); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="gender">
                                        <option value="Male" <?php echo strcasecmp($gender, 'Male') == 0 ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo strcasecmp($gender, 'Female') == 0 ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="contact" placeholder="Contact Number" value="<?php echo htmlspecialchars($contact); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="whatsapp_no" placeholder="Whatsapp Number" value="<?php echo htmlspecialchars($whatsapp); ?>" required>
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
                            <input class="input--style-1" type="text" name="salary" placeholder="Salary" value="<?php echo htmlspecialchars($salary); ?>">
                        </div>

                        <div class="input-group">
                            <input class="input--style-1" type="password" name="password" placeholder="Password">
                        </div>

                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Update Employee</button>
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
            output.style.display = 'block';
            document.getElementById('placeholder-text').style.display = 'none';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>
