<?php
session_start();
require_once('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $task_type = isset($_POST['task_type']) ? filter_var($_POST['task_type'], FILTER_SANITIZE_STRING) : '';
    $task_title = isset($_POST['task_title']) ? filter_var(trim($_POST['task_title']), FILTER_SANITIZE_STRING) : '';
    $task_description = isset($_POST['task_description']) ? filter_var(trim($_POST['task_description']), FILTER_SANITIZE_STRING) : '';
    $due_date = isset($_POST['due_date']) ? filter_var(trim($_POST['due_date']), FILTER_SANITIZE_STRING) : '';
    $status = isset($_POST['status']) ? filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING) : '';
    $priority = isset($_POST['priority']) ? filter_var(trim($_POST['priority']), FILTER_SANITIZE_STRING) : '';

    // Conditional fields based on task type
    if ($task_type === 'Single') {
        $emp_id = isset($_POST['emp_id']) ? filter_var(trim($_POST['emp_id']), FILTER_SANITIZE_STRING) : '';
        $project_id = isset($_POST['project_id']) ? filter_var(trim($_POST['project_id']), FILTER_SANITIZE_STRING) : '';
    } elseif ($task_type === 'Group') {
        $emp_id = isset($_POST['emp_id_group']) ? filter_var(trim($_POST['emp_id']), FILTER_SANITIZE_STRING) : '';
        $project_id = isset($_POST['project_id_group']) ? filter_var(trim($_POST['project_id']), FILTER_SANITIZE_STRING) : '';
        $team_name = isset($_POST['team_name']) ? filter_var(trim($_POST['team_name']), FILTER_SANITIZE_STRING) : '';
        $team_count = isset($_POST['team_count']) ? filter_var(trim($_POST['team_count']), FILTER_SANITIZE_NUMBER_INT) : '';
        $team_leader = isset($_POST['team_leader']) ? filter_var(trim($_POST['team_leader']), FILTER_SANITIZE_STRING) : '';
        $assigned_to = isset($_POST['assigned_to']) ? $_POST['assigned_to'] : [];
    }

    // Validation
    $errors = [];
    if (strlen($task_title) < 3) {
        $errors[] = 'Task title must be at least 3 characters long.';
    }
    if (strlen($task_description) < 10) {
        $errors[] = 'Task description must be at least 10 characters long.';
    }
    if (empty($due_date)) {
        $errors[] = 'Due date is required.';
    }
    if (empty($status)) {
        $errors[] = 'Status is required.';
    }
    if (empty($priority)) {
        $errors[] = 'Priority is required.';
    }
    if ($task_type === 'Group') {
        if (empty($team_name)) {
            $errors[] = 'Team name is required for group tasks.';
        }
        if (empty($team_count) || !filter_var($team_count, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            $errors[] = 'Team count must be a valid natural number.';
        }
        if (empty($assigned_to)) {
            $errors[] = 'At least one team member must be selected.';
        }
    }

    // If there are no errors, process the form data
    if (empty($errors)) {
        if ($task_type === 'Single') {
            $stmt = $conn->prepare("INSERT INTO tasks (task_title, task_description, emp_id, project_id, due_date, status, priority) VALUES (?, ?, ?, ?, ?, , 'In Progress', 'Medium')");
            $stmt->bind_param("sssssss", $task_title, $task_description, $emp_id, $project_id, $due_date, $status, $priority);
        } elseif ($task_type === 'Group') {
            $assigned_to_json = json_encode($assigned_to);
            // $stmt = $conn->prepare("INSERT INTO tasks (emp_id, project_id, task_title, task_description, team_name, team_count, team_leader, assigned_to, due_date, status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'In Progress', 'Medium')");
            // $stmt->bind_param("iissssiss", $emp_id, $project_id, $task_title, $task_description, $team_name, $team_count, $team_leader, $assigned_to, $due_date, $status, $priority);
            $stmt = $conn->prepare("INSERT INTO tasks (emp_id, project_id, task_title, task_description, team_name, team_count, team_leader, assigned_to, due_date, status, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'In Progress', 'Medium')");

            $stmt->bind_param("iissssiss", $emp_id, $project_id, $task_title, $task_description, $team_name, $team_count, $team_leader, $assigned_to, $due_date);
            
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Task successfully added!";
        } else {
            $_SESSION['errors'][] = "Failed to add task. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['errors'] = $errors;
    }

    header("Location: add_task.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('vendor/inc/head.php') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Task</title>
    <link rel="stylesheet" href="vendor/css/add-task.css?v=1.0">
</head>

<body>
    <?php include('vendor/inc/nav.php') ?>
    <div class="form-container">
        <h2>Add New Task</h2>

        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form id="addTaskForm" action="add_task.php" method="POST">
            <!-- Task Type -->
            <label>Task Type</label>
            <div class="radio-group">
                <input type="radio" name="task_type" value="Single" onclick="toggleTaskType('single')" required> Single Task
                <input type="radio" name="task_type" value="Group" onclick="toggleTaskType('group')" required> Group Task
            </div>

            <!-- Single Task Form -->
            <div id="single-task-form" style="display: none;">
                
                <!--Employee & Project Choose any one -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="emp_id">Employee</label>
                        <select id="emp_id" name="emp_id" required>
                            <option value="" disabled selected>Select Employee</option>
                            <?php
                            // Fetch employee first_name and emp_id from the database
                            $query = "SELECT emp_id, first_name FROM employee";
                            $result = $conn->query($query);

                            // Populate dropdown options with employee data
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['emp_id'] . '">' . $row['first_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="project_id">Project</label>
                        <input list="project-options" id="project_id" name="project_id" required placeholder="Select Project">
                        <datalist id="project-options">
                            <?php
                            // Fetch project names from the database
                            $project_query = "SELECT project_id, p_name FROM project";
                            $project_result = $conn->query($project_query);

                            // Populate datalist options with project data
                            while ($row = $project_result->fetch_assoc()) {
                                // Display the project_id and p_name in one row
                                echo '<option value="' . $row['project_id'] . ' - ' . $row['p_name'] . '">' . $row['project_id'] . ' - ' . $row['p_name'] . '</option>';
                            }
                            ?>
                        </datalist>
                    </div>
                </div>

                <!-- Task Title and Task Description -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="task_title">Task Title</label>
                        <input type="text" id="task_title" name="task_title" required minlength="3">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="task_description">Task Description</label>
                        <textarea id="task_description" name="task_description" required minlength="10"></textarea>
                    </div>
                </div>

                <!-- Due Date, Status, and Priority -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="text" id="due_date" name="due_date" required placeholder="DD/MM/YYYY">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <!-- <option value="" disabled selected>Select status</option> -->
                            <option value="Pending">Pending</option>
                            <option value="In Progress" selected>In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority" required>
                            <!-- <option value="" disabled selected>Select priority</option> -->
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Group Task Form -->
            <div id="group-task-form" style="display: none;">                
                <!-- Lead & Project Choose any one -->
            <div class="form-row">
                <div class="form-group">
                    <label for="emp_id_group">Team Lead</label>
                        <select id="emp_id_group" name="emp_id_group" required>
                            <option value="" disabled selected>Select Lead</option>
                            <?php
                            // Fetch employee first_name and emp_id from the database where type = 2
                            $query = "SELECT emp_id, first_name FROM employee WHERE type = 2";
                            $result = $conn->query($query);

                            // Populate dropdown options with filtered employee data
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['emp_id'] . '">' . $row['first_name'] . '</option>';
                            }
                            ?>
                        </select>
                </div>

                <div class="form-group">
                    <label for="project_id_group">Project</label>
                    <input list="project-options" id="project_id_group" name="project_id_group" required placeholder="Select Project">
                    <datalist id="project-options">
                            <?php
                            // Fetch project names from the database
                            $project_query = "SELECT project_id, p_name FROM project";
                            $project_result = $conn->query($project_query);

                            // Populate datalist options with project data
                            while ($row = $project_result->fetch_assoc()) {
                                // Display the project_id and p_name in one row
                                echo '<option value="' . $row['project_id'] . ' - ' . $row['p_name'] . '">' . $row['project_id'] . ' - ' . $row['p_name'] . '</option>';
                            }
                            ?>
                    </datalist>
                </div>
            </div>
                <!-- Team Name, Team Title -->
            <div class="form-row">
                <div class="form-group">
                    <label for="team_name">Team Name</label>
                    <input type="text" id="team_name" name="team_name" required>
                </div>

                <div class="form-group">
                    <label for="task_title">Task Title</label>
                    <input type="text" id="task_title" name="task_title" required minlength="3">
                </div>
            </div>
                <!-- Task Description -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="task_description">Task Description</label>
                        <textarea id="task_description" name="task_description" required minlength="10"></textarea>
                    </div>
                </div>     
                <!-- Due Date , Status & Priority  --> 
                <div class="form-row">
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="text" id="due_date" name="due_date" required placeholder="DD/MM/YYYY">
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <!-- <option value="" disabled selected>Select status</option> -->
                            <option value="Pending">Pending</option>
                            <option value="In Progress" selected>In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority" required>
                            <!-- <option value="" disabled selected>Select priority</option> -->
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>         

                <!-- Assign to Team Members -->
            <div class="form-row">
                    
                <div class="form-group">
                    <label for="assigned_to[]">Assign to Team Members</label>
                    <div id="employee-checkboxes">
                        <?php
                        // Fetch employee first_name and emp_id from the database
                        $query = "SELECT emp_id, first_name FROM employee";
                        $result = $conn->query($query);

                        // Generate checkboxes for each employee
                        while ($row = $result->fetch_assoc()) {
                            $emp_id = htmlspecialchars($row['emp_id'], ENT_QUOTES, 'UTF-8');
                            $first_name = htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8');
                            echo '<div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="assigned_to[]" value="' . $emp_id . '" onchange="updateTeamCount()"> ' . $first_name . '
                                    </label>
                                </div>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Team Count -->
                <div class="form-group">
                    <label for="team_count">Team Count</label>
                    <input type="number" id="team_count" name="team_count" value="0" required min="0" readonly>
                </div>
            </div>
        </div>
                <div class="form-row">
                    <button type="submit">Add Task</button>
                </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
    // Function to toggle visibility of task type forms
    function toggleTaskType(type) {
        document.getElementById('single-task-form').style.display = (type === 'Single') ? 'block' : 'none';
        document.getElementById('group-task-form').style.display = (type === 'Group') ? 'block' : 'none';
    }

    // Ensure correct form is displayed on page load
    const selectedType = document.querySelector('input[name="task_type"]:checked');
    if (selectedType) {
        toggleTaskType(selectedType.value);
    }

    // Attach event listeners to radio buttons
    document.querySelectorAll('input[name="task_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleTaskType(this.value);
        });
    });

    // Auto-update team count based on selected team members (Group task only)
    function updateTeamCount() {
        const checkedBoxes = document.querySelectorAll('#employee-checkboxes input[type="checkbox"]:checked');
        document.getElementById('team_count').value = checkedBoxes.length;
    }

    // Attach change event to checkboxes for team member counting
    document.querySelectorAll('#employee-checkboxes input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateTeamCount);
    });
});

    </script>
</body>

</html>