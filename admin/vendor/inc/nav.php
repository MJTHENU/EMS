<style>
 * {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  font-family: sans-serif;
  text-decoration: none;
  list-style: none;
}

.header {
  background-color: #2d8ad3;
  color: #fff;
}

.logo {
  display: inline-block;
}

.menu {
  display: flex;
  align-items: center;
  background-color: #2d8ad3;
}

.menu li {
  position: relative;
}

.menu a {
  color: #fff;
  padding: 15px 20px;
  display: block;
  text-align: center;
}

.menu a:hover {
  background-color: #2d8ad3;
}

.dropdown-menu {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background-color: #2d8ad3;
  min-width: 160px;
}

.dropdown-menu li {
  display: block;
}

.dropdown-menu a {
  padding: 10px;
  color: #fff;
}

.dropdown-menu a:hover {
  background-color: #2d8ad3;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

</style>
<header class="header">
  <nav>
    <div class="logo">
    <img class = "logo" src = "../vendor/images/kite-logo.png" width="150px" height="60px">
    </div>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="menu-icon">&#9776;</label>
    <ul class="menu">
      <li><a href="index.php">Home</a></li>
      <!-- <li><a href="viewemp.php">View Employee</a></li> -->
      <li class="dropdown">
        <a href="view.php" class="dropdown-toggle">View</a>
        <ul class="dropdown-menu">
          <li><a href="viewemp.php">Employee</a></li>
          <li><a href="viewtr.php">Trainee</a></li>
        </ul>
      </li>
      <li><a href="assign.php">Project</a></li>
      <li><a href="task.php">Task</a></li>
      <li><a href="assignproject.php">Project Status</a></li>
      <li><a href="salary-emp.php">Salary</a></li>
      <!-- <li><a href="attendance.php">Attendance</a></li> -->
      <li class="dropdown">
        <a href="attendance.php" class="dropdown-toggle">Attendance</a>
        <ul class="dropdown-menu">
          <li><a href="attemp.php">Employee</a></li>
          <li><a href="atttr.php">Trainee</a></li>
        </ul>
      </li>
      <li><a href="emp-leave.php">Employee Leave</a></li>
      <li><a href="#" id = "logout">Log Out</a></li>
    </ul>
  </nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

document.addEventListener("DOMContentLoaded", function(){

 const logout = document.getElementById('logout');

 logout.addEventListener('click', function(event){
  event.preventDefault();
  Swal.fire({
    title: 'Are you sure?',
    text: "You will be logou.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, log me out!'
  }).then((result) => {
     if(result.isConfirmed) {
      window.location.href = 'vendor/inc/logout.php';
     }
  });
});
});

</script>