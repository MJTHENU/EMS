<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';

include('vendor/inc/connection.php');

// Use prepared statements for better security
$stmt = $conn->prepare("SELECT * FROM `tasks` WHERE emp_id = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php') ?>
    <link rel="stylesheet" href="vendor/css/project.css">
</head>
<body>
    <?php include('vendor/inc/nav.php') ?>
    <div class="contain">
        <h2 class="h2">My Tasks</h2>

        <table>
            <tr>
                <th align="center">Task ID</th>
                <th align="center">Task Title</th>
                <th align="center">Task Description</th>
                <th align="center">Due Date</th>
                <th align="center">Status</th>
                <th align="center">Priority</th>
                <th align="center">Project ID</th>
                <th align="center">Options</th>
            </tr>

            <?php while ($task = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['task_id']); ?></td>
                    <td><?php echo htmlspecialchars($task['task_title']); ?></td>
                    <td><?php echo htmlspecialchars($task['task_description']); ?></td>
                    <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($task['status']); ?></td>
                    <td><?php echo htmlspecialchars($task['priority']); ?></td>
                    <td><?php echo htmlspecialchars($task['project_id']); ?></td>
                    <td>
                        <a class="submit" href="task-submit.php?task_id=<?php echo htmlspecialchars($task['task_id']); ?>&emp_id=<?php echo htmlspecialchars($task['emp_id']); ?>">
                            Submit
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <h4>Future Update....</h4>
    </div>
</body>
</html>
