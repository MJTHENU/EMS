<?php
 session_start();

 if (!isset($_SESSION['emp_id'])) {
     header("Location: emp-login.php");
     exit();
 }

require_once ('vendor/inc/connection.php');
$sql = "SELECT * FROM `employee` WHERE 1";

//echo "$sql";
$result = mysqli_query($conn, $sql);
if(isset($_POST['update']))
{

  $id = $_POST['emp_id'];
  $old = $_POST['oldpass'];
  $new = $_POST['newpass'];
  
  $result = mysqli_query($conn, "select employee.password from employee WHERE emp_id = '$id'");
     $employee = mysqli_fetch_assoc($result);
          if($old == $employee['password']){
            $sql = "UPDATE `employee` SET `password`='$new' WHERE emp_id = '$id'";
            mysqli_query($conn, $sql);
             echo ("<SCRIPT LANGUAGE='JavaScript'>
                  window.alert('Password Updated')
                window.location.href='my-profile.php?emp_id=$id';</SCRIPT>"); 
          
        }

        else{
          echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Failed to Update Password')
    window.location.href='javascript:history.go(-1)';
    </SCRIPT>");
        }

  
}
?>
 <?php
//   $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
//   $sql = "SELECT * from `employee` WHERE emp_id=$id";
//   $result = mysqli_query($conn, $sql);
//   if($result){
//   while($res = mysqli_fetch_assoc($result)){
//   $old = $res['password'];
//   echo "$old";
// }
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
   <link rel ="stylesheet" href = "vendor/css/profile.css">
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>

<div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2">Update Password</h2>
                    <form id = "registration" action="change-pass.php" method="POST">

                          <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Old Password</p>
                                     <input class="input--style-1" type="Password" name = "oldpass" required >
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                  <p>New Password</p>
                                    <input class="input--style-1" type="Password" name="newpass" required>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="emp_id" id="textField" value="<?php echo $id;?>" required="required"><br><br>
                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Submit</button>
                        </div>
                        
                    </form>
                    
            </div>
        </div>
    </div>

</body>
</html>