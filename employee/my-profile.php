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


  $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');
  $sql = "SELECT * from `employee` WHERE emp_id='$id'";
  $sql2 = "SELECT total from `salary` WHERE emp_id = '$id'";
  $result = mysqli_query($conn, $sql);
  $result2 = mysqli_query($conn , $sql2);
  $salary = mysqli_fetch_array($result2);
  $empS = ($salary['total']);
 
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
  $pic = $res['img'];
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
                    <h2 class="h2">My Info</h2>
                    <form method="POST" action="profile_update.php?emp_id=<?php echo $id?>" >

                        <div class="info-img">
                          <img src="../admin/vendor/process/<?php echo $pic;?>" height = 150px width = 150px>
                        </div>


                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                  <p>First Name</p>
                                     <input class="input--style-1" type="text" name="first_name" value="<?php echo $firstname;?>" readonly >
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Last Name</p>
                                    <input class="input--style-1" type="text" name="last_name" value="<?php echo $lastname;?>" readonly>
                                </div>
                            </div>
                        </div>





                        <div class="input-group">
                          <p>Email</p>
                            <input class="input--style-1" type="email"  name="email" value="<?php echo $email;?>" readonly>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Date of Birth</p>
                                    <input class="input--style-1" type="text" name="date_of_birth" value="<?php echo $birthday;?>" readonly>
                                   
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                  <p>Gender</p>
                                  <input class="input--style-1" type="text" name="gender" value="<?php echo $gender;?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="input-group">
                          <p>Contact Number</p>
                            <input class="input--style-1" type="number" name="contact" value="<?php echo $contact;?>" readonly>
                        </div>
                        
                         <div class="input-group">
                          <p>Address</p>
                            <input class="input--style-1" type="text"  name="address" value="<?php echo $address;?>" readonly>
                        </div>

                        <div class="input-group">
                          <p>Department</p>
                            <input class="input--style-1" type="text" name="dept" value="<?php echo $dept;?>" readonly>
                        </div>

                        <div class="input-group">
                          <p>Degree</p>
                            <input class="input--style-1" type="text" name="degree" value="<?php echo $degree;?>" readonly>
                        </div>


                        <div class="input-group">
                          <p>Total Salary</p>
                            <input class="input--style-1" type="text" name="degree" value="<?php echo $empS;?>" readonly>
                        </div>

                        <input type="hidden" name="id" id="textField" value="<?php echo $id;?>" required="required"><br><br>

                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green"  name="send" >Update Info</button>
                        </div>

                        
                    </form>
                    
                    <div class="p-t-20">
                    <button class="btn btn--radius btn--green" onclick="window.location.href = 'add-bank.php?emp_id=<?php echo $id?>';">Add Account</button>
                    </div>  
                </div>
            </div>
        </div>
    </div>


    
</body>
</html>