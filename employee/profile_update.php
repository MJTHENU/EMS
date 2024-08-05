<?php
 session_start();

 if (!isset($_SESSION['emp_id'])) {
     header("Location: emp-login.php");
     exit();
 }
?>
<?php

require_once ('vendor/inc/connection.php');
$sql = "SELECT * FROM `employee` WHERE 1";

//echo "$sql";
$result = mysqli_query($conn, $sql);
if(isset($_POST['update']))
{

  $id = mysqli_real_escape_string($conn, $_POST['emp_id']);
  $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
  $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $contact = mysqli_real_escape_string($conn, $_POST['contact']);
  $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $dept = mysqli_real_escape_string($conn, $_POST['dept']);
  $degree = mysqli_real_escape_string($conn, $_POST['degree']);
  $status = mysqli_real_escape_string($conn, $_POST['status']);
 


 $result = mysqli_query($conn, "UPDATE `employee` SET `first_name`='$first_name', `last_name`='$last_name', `email`='$email',`contact`='$contact', `date_of_birth`='$date_of_birth', `address`='$address', `gender`='$gender', `dept`='$dept', `degree`='$degree' WHERE emp_id='$id'");

 echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Succesfully Updated')
    window.location.href='my-profile.php?emp_id=$id  ';
    </SCRIPT>");

  
}
?>
<?php
  $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
  $sql = "SELECT * from `employee` WHERE emp_id='$id'";
  $result = mysqli_query($conn, $sql);
  if($result){
  while($res = mysqli_fetch_assoc($result)){
  $firstname = $res['first_name'];
  $lastname = $res['last_name'];
  $email = $res['email'];
  $contact = $res['contact'];
  $address = $res['address'];
  $gender = $res['gender'];
  $birthday = $res['date_of_birth'];
  $dept = $res['dept'];
  $degree = $res['degree'];
  $status = $res['status'];
  // $salary = $res['salary'];
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php');  ?>
   <link rel ="stylesheet" href = "vendor/css/profile.css">
</head>
<body>
<?php include('vendor/inc/nav.php');  ?>


<div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2">Update Employee Info</h2>
                    <form id = "registration" action="profile_update.php" method="POST">


                    <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                     <input class="input--style-1" type="text" placeholder="First Name" name="first_name" value="<?php echo $firstname;?>" required="required">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" placeholder="Last Name" name="last_name" value="<?php echo $lastname;?>" required="required">
                                </div>
                            </div>
                        </div>

                        <div class="input-group">
                          <p>Email</p>
                            <input class="input--style-1" type="email"  name="email" value="<?php echo $email;?>">
                        </div>
                       
                        
                        <div class="input-group">
                          <p>Contact</p>
                            <input class="input--style-1" type="number" name="contact" value="<?php echo $contact;?>">
                        </div>

                        <p>Date Of Birth</p>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" name="date_of_birth" value="<?php echo $birthday;?>" required="required">
                                   
                                </div>
                            </div>
                            <div class="col-2">
                                        <select class = "gender" name="gender">
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
                            <input class="input--style-1" type="text"  name="address" value="<?php echo $address;?>">
                        </div>

                        <div class="input-group">
                          <p>Department</p>
                            <input class="input--style-1" type="text"  name="dept" value="<?php echo $dept;?>">
                        </div>

                        <div class="input-group">
                          <p>Degree</p>
                            <input class="input--style-1" type="text"  name="degree" value="<?php echo $degree;?>">
                        </div>

                       
                        <input type="hidden" name="emp_id" id="textField" value="<?php echo $id;?>" required="required"><br><br>
                        <div class="submit" style="text-align:center;">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Update</button>
                        </div>
                        
                    </form>
                    <div class="change">
                    <button class="btn btn--radius btn--green" onclick="window.location.href = 'change-pass.php?emp_id=<?php echo $id?>';">Change Password</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>