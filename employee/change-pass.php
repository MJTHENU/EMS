<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

require_once('vendor/inc/connection.php');

if (isset($_POST['update'])) {
    $id = $_POST['emp_id'];
    $old = $_POST['oldpass'];
    $new = $_POST['newpass'];

    // Fetch the current hashed password from the database
    $result = mysqli_query($conn, "SELECT password FROM employee WHERE emp_id = '$id'");
    $employee = mysqli_fetch_assoc($result);

    if ($employee && password_verify($old, $employee['password'])) {
        // Hash the new password
        $hashed_new_password = password_hash($new, PASSWORD_BCRYPT);

        // Update the password in the database
        $sql = "UPDATE employee SET password = '$hashed_new_password' WHERE emp_id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo ("<SCRIPT LANGUAGE='JavaScript'>
                window.alert('Password Updated')
                window.location.href='my-profile.php?emp_id=$id';
            </SCRIPT>");
        } else {
            echo ("<SCRIPT LANGUAGE='JavaScript'>
                window.alert('Failed to Update Password')
                window.location.href='javascript:history.go(-1)';
            </SCRIPT>");
        }
    } else {
        echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.alert('Old Password is Incorrect')
            window.location.href='javascript:history.go(-1)';
        </SCRIPT>");
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
                <h2 class="h2">Update Password</h2>
                <form id="registration" action="change-pass.php" method="POST">
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <p>Old Password</p>
                                <input class="input--style-1" type="password" name="oldpass" required>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group">
                                <p>New Password</p>
                                <input class="input--style-1" type="password" name="newpass" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="emp_id" id="textField" value="<?php echo $id; ?>" required="required"><br><br>
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
