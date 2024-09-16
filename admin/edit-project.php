<?php
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

if (isset($_GET['project_id'])) {
    $project_id = mysqli_real_escape_string($conn, $_GET['project_id']);
    $sql = "SELECT * FROM project WHERE project_id = '$project_id'";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $project = mysqli_fetch_assoc($result);
    
    if (!$project) {
        echo "Project not found!";
        exit();
    }
} else {
    echo "No project ID provided!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data
    $project_id = mysqli_real_escape_string($conn, trim($_POST['project_id']));
    $p_name = mysqli_real_escape_string($conn, trim($_POST['p_name']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['desc']));
    $client = mysqli_real_escape_string($conn, trim($_POST['client']));
    $code_lan = mysqli_real_escape_string($conn, trim($_POST['code_lan']));
    $start_date = mysqli_real_escape_string($conn, trim($_POST['start_date']));
    $end_date = mysqli_real_escape_string($conn, trim($_POST['end_date']));
    $status = mysqli_real_escape_string($conn, trim($_POST['status']));
    $priority = mysqli_real_escape_string($conn, trim($_POST['priority']));

    // Validate the data
    $errors = [];

     if (empty($errors)) {
        // Proceed with the update query
        $sql = "UPDATE project SET
                    project_id = '$project_id',
                    p_name = '$p_name',
                    `desc` = '$desc',
                    client = '$client',
                    code_lan = '$code_lan',
                    start_date = '$start_date',
                    end_date = '$end_date',
                    status = '$status',
                    priority = '$priority',
                    updated_at = NOW()
                WHERE project_id = '$project_id'";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Project updated successfully!'); window.location.href='assign.php';</script>";
        } else {
            die("Update failed: " . mysqli_error($conn));
            echo "<script>alert('Update failed');</script>";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
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
                    <h2 class="h2">Update Project</h2>
                    <hr>
                    <form id="project" action="edit-project.php?project_id=<?php echo htmlspecialchars($project_id); ?>" method="POST">
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" id="project_id" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>" require>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" id="client" name="client" value="<?php echo htmlspecialchars($project['client']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                <input class="input--style-1" type="text" id="p_name" name="p_name" value="<?php echo htmlspecialchars($project['p_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="text" id="code_lan" name="code_lan" value="<?php echo htmlspecialchars($project['code_lan']); ?>" required>
                                </div>
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
                            <textarea class="input--style" id="desc" name="desc" required minlength="10" maxlength="500" title="Description should be between 10 and 500 characters."><?php echo htmlspecialchars($project['desc']); ?></textarea>
                        </div>
                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" required>

                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" required>
                                </div>
                            </div>                            
                        </div>
                        <div class="p-t-20">
                            <button class="btn btn--green btn--radius" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
