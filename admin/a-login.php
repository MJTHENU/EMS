<!DOCTYPE html>
<html lang="en">
<head>
<?php include('../vendor/inc/head.php'); ?>
 <link rel = "stylesheet" href = "vendor/css/login.css">
</head>
<body>
<?php include('../vendor/inc/nav.php'); ?>
    <section class="container1">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <img src="https://raw.githubusercontent.com/hicodersofficial/glassmorphism-login-form/master/assets/illustration.png" alt="illustration" class="illustration" />
                <h1 class="opacity">LOGIN</h1>
                <form action ="vendor/process/aprocess.php" method ="post">
                    <input type="text" name = "email" placeholder="EMAIL" />
                    <input type="password" name ="password" placeholder="PASSWORD" />
                    <button class="opacity">SUBMIT</button>
                </form>
                <div class="register-forget opacity">
                    <a href="">FORGOT PASSWORD</a>
                </div>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>
</body>
</html>