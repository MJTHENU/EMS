<!DOCTYPE html>
<html lang="en">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employeee Management</title>
    <link rel = "stylesheet" href = "vendor/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>

</head>
<body>
<nav class="navbar">
        <div class="navbar-container container">
            <input type="checkbox" name="" id="">
            <div class="hamburger-lines">
                <span class="line line1"></span>
                <span class="line line2"></span>
                <span class="line line3"></span>
            </div>
            <ul class="menu-items">
                <li><a href="index.php">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <div class="dropdown">
                <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Login
                </button>
                <ul class="dropdown-menu">
                    
                    <li><a href = "./admin/a-login.php" class="dropdown-item">Admin Login</a></li>
                    <li><a href = "./employee/emp-login.php" class="dropdown-item">Emp Login</a></li>
                    <li><a href = "./trainee/t-login.php" class="dropdown-item">Trainee Login</a></li>
                </ul>
                </div>
            </ul>
            <img class = "logo" src = "vendor/images/kitecareer_logo.jpg" width="100px" height="60px">
        </div>
    </nav>
     <div class="container-fluid display">
		
		<h1 style="font-family: 'Lobster', cursive; font-weight: 200; font-size: 70px;">Kite Career</h1>
		<p style="font-family: 'Montserrat', sans-serif; font-size: 40px ;">It's Your Start Up</p>
        <p style = "font-size: 30px; font-weight: 200;">Empower your team, elevate your business"</p>

     </div>
</body>
</html>