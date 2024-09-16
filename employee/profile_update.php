<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

require_once('vendor/inc/connection.php');
$sql = "SELECT * FROM `employee` WHERE 1";

$result = mysqli_query($conn, $sql);

if (isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification']);
    
    // Handle file upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $fileType = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $img = mysqli_real_escape_string($conn, file_get_contents($_FILES['img']['tmp_name']));
            $imgUpdate = ", `img`='$img'";
        } else {
            echo "<script>alert('Only image files are allowed');</script>";
            exit();
        }
    } else {
        $imgUpdate = "";
    }
     // Validate phone numbers
     if (!preg_match("/^\d{10}$/", $contact)) {
        echo "<script>alert('Contact number must be 10 digits');</script>";
        exit();
    }
    // if (!preg_match("/^\d{10}$/", $whatsapp)) {
    //     echo "<script>alert('Whatsapp number must be 10 digits');</script>";
    //     exit();
    // }

    $sqlUpdate = "UPDATE `employee` SET 
        `last_name`='$last_name', 
        `contact`='$contact', 
        `date_of_birth`='$date_of_birth', 
        `address`='$address', 
        `qualification`='$qualification'
        $imgUpdate 
        WHERE emp_id='$id'";

    $result = mysqli_query($conn, $sqlUpdate);

    echo ("<SCRIPT LANGUAGE='JavaScript'>
        window.alert('Successfully Updated');
        window.location.href='my-profile.php?emp_id=$id';
        </SCRIPT>");
}
?>

<?php
$id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
$sql = "SELECT * from `employee` WHERE emp_id='$id'";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($res = mysqli_fetch_assoc($result)) {
        $firstname = $res['first_name'];
        $lastname = $res['last_name'];
        $email = $res['email'];
        $contact = $res['contact'];
        $address = $res['address'];
        $gender = $res['gender'];
        $birthday = $res['date_of_birth'];
        $role = $res['role'];
        $qualification = $res['qualification'];
        $img = $res['img'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/profile.css">
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>

<div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
    <div class="wrapper wrapper--w680">
        <div class="card card-1">
            <div class="card-body">
                <h2 class="h2">Update Employee Info</h2>
                <form id="registration" action="profile_update.php" method="POST" enctype="multipart/form-data">

                    <div class="input-group">
                        <input id="profile" class="custom-file-input" type="file" name="img" accept="image/*" onchange="previewImage(event)">
                        <label for="profile" class="custom-file-label">
                            <img id="profile-preview" class="image--cover" src="<?php echo !empty($img) ? 'data:image/jpeg;base64,' . base64_encode($img) : ''; ?>" alt="Profile Image Preview">
                            <span id="placeholder-text" style="display: <?php echo !empty($img) ? 'none' : 'inline-block'; ?>;">Upload Profile</span>
                        </label>
                    </div>

                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <input class="input--style-1" type="text" placeholder="First Name" name="first_name" value="<?php echo $firstname; ?>" disabled>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group">
                                <input class="input--style-1" type="text" placeholder="Last Name" name="last_name" value="<?php echo $lastname; ?>" required="required">
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <p>Email</p>
                        <input class="input--style-1" type="email" name="email" value="<?php echo $email; ?>" disabled>
                    </div>

                    <div class="input-group">
                        <p>Contact</p>
                        <input class="input--style-1" type="number" name="contact" value="<?php echo $contact; ?>">
                    </div>

                    <p>Date Of Birth</p>
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <input class="input--style-1" type="date" name="date_of_birth" value="<?php echo $birthday; ?>" required="required">
                            </div>
                        </div>
                        <div class="col-2">
                            <select class="input--style-1" name="gender" disabled>
                                <option disabled="disabled" selected="selected">GENDER</option>
                                <option value="male" <?php if ($gender === 'male') echo 'selected'; ?>>Male</option>
                                <option value="female" <?php if ($gender === 'female') echo 'selected'; ?>>Female</option>
                                <option value="other" <?php if ($gender === 'other') echo 'selected'; ?>>Other</option>
                            </select>
                            <div class="select-dropdown"></div>
                        </div>
                    </div>

                    <div class="input-group">
                        <p>Address</p>
                        <input class="input--style-1" type="text" name="address" value="<?php echo $address; ?>">
                    </div>

                    <div class="input-group">
                        <p>Role</p>
                        <input class="input--style-1" type="text" name="role" value="<?php echo $role; ?>" disabled>
                    </div>

                    <div class="input-group">
                        <p>Qualification</p>
                        <input class="input--style-1" type="text" name="qualification" value="<?php echo $qualification; ?>">
                    </div>

                    <input type="hidden" name="emp_id" id="textField" value="<?php echo $id; ?>" required="required"><br><br>
                    <div class="submit" style="text-align:center;">
                        <button class="btn btn--radius btn--green" type="submit" name="update">Update</button>
                        <button class="btn btn--radius btn--green" type="button" onclick="redirectToChangePass()">Change Password</button>

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

    function redirectToChangePass() {
        var empId = "<?php echo $id; ?>"; // Get the emp_id PHP variable
        var url = 'change-pass.php?emp_id=' + empId;
        window.location.href = url; // Redirect to the change password page
    }
    </script>

</body>
</html>
