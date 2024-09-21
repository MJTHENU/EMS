<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

require_once('vendor/inc/connection.php');

// Retrieve employee ID from session
$id = $_SESSION['emp_id'];

// Sanitize and fetch employee ID
$id = mysqli_real_escape_string($conn, $id);

if (empty($id)) {
    echo "<p>Employee ID is missing.</p>";
    exit();
}

// Query to get employee information
$sql = "SELECT * FROM `employee` WHERE emp_id='$id'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "<p>Query Error: " . mysqli_error($conn) . "</p>";
    exit();
}

$employee = mysqli_fetch_assoc($result);

if ($employee) {
    $accountApproved = $employee['account_approved'];
} else {
    echo "<p>No employee found with the given ID.</p>";
    echo "<p>SQL Query: $sql</p>";
    exit();
}

// Fetch salary data
$sql2 = "SELECT salary FROM salary WHERE emp_id = '$id'";
$result2 = mysqli_query($conn, $sql2);

if (!$result2) {
    echo "<p>Query Error: " . mysqli_error($conn) . "</p>";
    exit();
}

if (mysqli_num_rows($result2) > 0) {
    $salary = mysqli_fetch_assoc($result2);
    $empS = $salary['salary'];
} else {
    $empS = 'Not Available';
    echo "<p>Salary data not found.</p>";
}

// Handle file upload (if applicable)
if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
    $fileType = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $img = mysqli_real_escape_string($conn, file_get_contents($_FILES['img']['tmp_name']));
        // Save the image data to the database or handle it as needed
    } else {
        echo "<script>alert('Only image files are allowed');</script>";
        exit();
    }
}

// Check if the image data exists and set the image source accordingly
$imageSrc = !empty($employee['img']) ? 'data:image/jpeg;base64,' . base64_encode($employee['img']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/profile.css?v=1.0">
    <style>
        /* Additional styles for profile page */
        .image--cover {
            border-radius: 50%;
        }
        .input--style-1 {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .button-row {
            display: flex;
            justify-content: space-between;
        }
        .btn {
            padding: 10px 20px;
            color: #fff;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-body">
                    <h2 class="h2">My Info</h2>
                    <form method="POST" action="profile_update.php?emp_id=<?php echo htmlspecialchars($id); ?>" enctype="multipart/form-data">

                        <div class="input-group">
                            <input id="profile" class="custom-file-input" type="file" name="img" accept="image/*" onchange="previewImage(event)" disabled>
                            <label for="profile" class="custom-file-label">
                                <img id="profile-preview" class="image--cover" src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Profile Image Preview">
                                <span id="placeholder-text" style="display: <?php echo !empty($imageSrc) ? 'none' : 'inline-block'; ?>;">Upload Profile</span>
                            </label>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <p>First Name</p>
                                    <input class="input--style-1" type="text" name="first_name" value="<?php echo htmlspecialchars($employee['first_name']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Last Name</p>
                                    <input class="input--style-1" type="text" name="last_name" value="<?php echo htmlspecialchars($employee['last_name']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <p>Email</p>
                            <input class="input--style-1" type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" readonly>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Date of Birth</p>
                                    <input class="input--style-1" type="text" name="date_of_birth" value="<?php echo htmlspecialchars($employee['date_of_birth']); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Gender</p>
                                    <input class="input--style-1" type="text" name="gender" value="<?php echo htmlspecialchars($employee['gender']); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <p>Contact Number</p>
                            <input class="input--style-1" type="number" name="contact" value="<?php echo htmlspecialchars($employee['contact']); ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Address</p>
                            <input class="input--style-1" type="text" name="address" value="<?php echo htmlspecialchars($employee['address']); ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Role</p>
                            <input class="input--style-1" type="text" name="role" value="<?php echo htmlspecialchars($employee['role']); ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Qualification</p>
                            <input class="input--style-1" type="text" name="qualification" value="<?php echo htmlspecialchars($employee['qualification']); ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Salary</p>
                            <input class="input--style-1" type="text" name="salary" value="<?php echo htmlspecialchars($empS); ?>" readonly>
                        </div>

                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>" required="required"><br><br>

                        <div class="button-row">
                            <button class="btn" name="send">Update Info</button>
                            <button class="btn" type="button" onclick="window.location.href='add-bank.php?emp_id=<?php echo htmlspecialchars($id); ?>';" <?php echo $accountApproved ? 'style="display:none;"' : ''; ?>>Add Account</button>
                            <button class="btn" type="button" onclick="window.location.href='view-account.php?emp_id=<?php echo htmlspecialchars($id); ?>';" <?php echo !$accountApproved ? 'style="display:none;"' : ''; ?>>View Account</button>
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
                document.getElementById('placeholder-text').style.display = 'none';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>
</html>
