<?php include('connection.php');
   $id = (isset($_GET['emp_id']) ? $_GET['emp_id'] : '');

?>
<style>
    * {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  font-family: sans-serif;
  text-decoration: none;
  list-style: none;
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
      <li><a class="active" href="index.php?emp_id=<?php echo $id?>">Home</a></li>
      <li><a href="my-profile.php?emp_id=<?php echo $id?>">My Profile</a></li>
      <li><a href="my-projects.php?emp_id=<?php echo $id?>">Task</a></li>
      <li><a href="attendance.php?emp_id=<?php echo $id?>">Attendance</a></li>
      <li><a href="apply-leave.php?emp_id=<?php echo $id?>">Apply Leave</a></li>
      <li><a href="#" id ="logout">Log Out</a></li>
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
      window.location.href = 'logout.php';
     }
  });
});
});

</script>