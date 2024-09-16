<?php 
session_start();
include('vendor/inc/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Fetch employee names with type = 2
$employee_query = "SELECT emp_id, first_name FROM employee WHERE type = 2";
$employee_result = mysqli_query($conn, $employee_query);

$employees = [];
while ($row = mysqli_fetch_assoc($employee_result)) {
    $employees[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = mysqli_real_escape_string($conn, $_POST['project_id']);
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_lead = mysqli_real_escape_string($conn, $_POST['p_lead']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $client = mysqli_real_escape_string($conn, $_POST['client']);
    $code_lan = mysqli_real_escape_string($conn, $_POST['code_lan']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : NULL;
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    // Check if project_id or p_name already exists
    $check_query = "SELECT * FROM project WHERE project_id = '$project_id' OR p_name = '$p_name'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Project ID or Project Name already exists! Please choose a different one.'); window.history.back();</script>";
    } else {
        // Insert query
        $sql = "INSERT INTO project (project_id, p_name, p_lead, `desc`, client, code_lan, start_date, end_date, status, priority, created_at, updated_at) 
                VALUES ('$project_id', '$p_name', '$p_lead', '$desc', '$client', '$code_lan', '$start_date', ".($end_date ? "'$end_date'" : "NULL").", '$status', '$priority', NOW(), NOW())";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Project added successfully'); window.location.href = 'assign.php';</script>";
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Form</title>
    <link rel="stylesheet" href="vendor/css/add-project.css?v=1.0">
    <?php include('vendor/inc/head.php'); ?>
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>
    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2">Add New Project</h2>
                    <hr>
                    <form id="project" action="add-project.php" method="POST">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="project_id" placeholder="Project Id" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="client" placeholder="Client Name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="p_name" placeholder="Project Name" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" name="code_lan" placeholder="Coding Language" required>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="input-group">
                                 <select class="input--style" name="p_lead" required>
                                    <option value="" disabled selected>Select Project Lead</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?php echo htmlspecialchars($employee['emp_id']); ?>">
                                            <?php echo htmlspecialchars($employee['first_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="status" required>
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="On Hold">On Hold</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <select class="input--style" name="priority" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium" selected>Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <textarea class="input--style" name="desc" placeholder="Description" rows="4" required></textarea>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" name="start_date" placeholder="Start Date" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" name="end_date" placeholder="End Date">
                                </div>
                            </div>                            
                        </div>
                        <div class="p-t-20">
                            <button class="btn btn--green btn--radius" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
