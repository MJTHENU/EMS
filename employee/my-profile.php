<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

require_once('vendor/inc/connection.php');

$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
$sql = "SELECT * FROM `employee` WHERE emp_id='$id'";
$result = mysqli_query($conn, $sql);
$employee = mysqli_fetch_assoc($result);

if ($employee) {
    $accountApproved = $employee['account_approved'];
}

// Fetch salary data
$sql2 = "SELECT salary FROM salary WHERE emp_id = '$id'";
$result2 = mysqli_query($conn, $sql2);

if ($result2 && mysqli_num_rows($result2) > 0) {
    $salary = mysqli_fetch_assoc($result2);
    $empS = $salary['salary'];
} else {
    $empS = 'Not Available'; // Fallback value or error handling
    echo "<script>console.log('Salary query returned no results or failed');</script>";
}


// Handle file upload (if applicable)
if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
    $fileType = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $img = mysqli_real_escape_string($conn, file_get_contents($_FILES['img']['tmp_name']));
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
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-body">
                    <h2 class="h2">My Info</h2>
                    <form method="POST" action="profile_update.php?emp_id=<?php echo $id; ?>" enctype="multipart/form-data">

                        <div class="input-group">
                            <input id="profile" class="custom-file-input" type="file" name="img" accept="image/*" onchange="previewImage(event)" disabled>
                            <label for="profile" class="custom-file-label">
                                <img id="profile-preview" class="image--cover" src="<?php echo $imageSrc; ?>" alt="Profile Image Preview">
                                <span id="placeholder-text" style="display: <?php echo !empty($imageSrc) ? 'none' : 'inline-block'; ?>;">Upload Profile</span>
                            </label>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <p>First Name</p>
                                    <input class="input--style-1" type="text" name="first_name" value="<?php echo $employee['first_name']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Last Name</p>
                                    <input class="input--style-1" type="text" name="last_name" value="<?php echo $employee['last_name']; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <p>Email</p>
                            <input class="input--style-1" type="email" name="email" value="<?php echo $employee['email']; ?>" readonly>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Date of Birth</p>
                                    <input class="input--style-1" type="text" name="date_of_birth" value="<?php echo $employee['date_of_birth']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <p>Gender</p>
                                    <input class="input--style-1" type="text" name="gender" value="<?php echo $employee['gender']; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                            <p>Contact Number</p>
                            <input class="input--style-1" type="number" name="contact" value="<?php echo $employee['contact']; ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Address</p>
                            <input class="input--style-1" type="text" name="address" value="<?php echo $employee['address']; ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Role</p>
                            <input class="input--style-1" type="text" name="role" value="<?php echo $employee['role']; ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Qualification</p>
                            <input class="input--style-1" type="text" name="qualification" value="<?php echo $employee['qualification']; ?>" readonly>
                        </div>

                        <div class="input-group">
                            <p>Salary</p>
                            <input class="input--style-1" type="text" name="salary" value="<?php echo htmlspecialchars($empS); ?>" readonly>
                        </div>

                        <input type="hidden" name="id" value="<?php echo $id; ?>" required="required"><br><br>

                        <div class="button-row">
                            <!-- <button class="btn btn--radius btn--blue" name="send" <?php echo $accountApproved ? 'disabled' : ''; ?>>Update Info</button> -->
                            <button class="btn btn--radius btn--blue" name="send">Update Info</button>
                            <button class="btn btn--radius btn--blue" type="button" onclick="window.location.href='add-bank.php?emp_id=<?php echo $id; ?>';" <?php echo $accountApproved ? 'style="display:none;"' : ''; ?>>Add Account</button>
                            <button class="btn btn--radius btn--blue" type="button" onclick="window.location.href='view-account.php?emp_id=<?php echo $id; ?>';" <?php echo !$accountApproved ? 'style="display:none;"' : ''; ?>>View Account</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</body>
</html>
