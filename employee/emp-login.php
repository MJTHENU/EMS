<?php
 session_start();

 include('vendor/inc/connection.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Management</title>
 
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel ="stylesheet" href = "vendor/css/login.css">
    <link rel ="stylesheet" href = "../vendor/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!--Stylesheet-->
</head>
<body>
  <?php include('../vendor/inc/nav.php') ?>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form action ="vendor/process/eprocess.php" method="POST">
        <h3>Login Here</h3>

        <label for="username">Username</label>
        <input type="text" placeholder="Email" name ="email" id="username">

        <label for="password">Password</label>
        <input type="password" placeholder="Password" name = "password" id="password">

        <button class ="btn1">Log In</button>
        
    </form>
</body>
</html>
<!-- partial -->
  
</body>
</html>
