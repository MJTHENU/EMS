<?php
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Define the number of results per page
$results_per_page = 10;

// Get the current page number from the URL, defaulting to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $results_per_page;

// Initialize the search query
$search_query = '';

// Check if a search query is set
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);

    // Sanitize the input to prevent SQL injection
    $search_query = htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8');

    // Validate: Ensure the input is not too short or too long
    if (strlen($search_query) < 2) {
        $search_query = '';
        $error_message = 'Search term must be at least 2 characters long.';
    } elseif (strlen($search_query) > 100) {
        $search_query = '';
        $error_message = 'Search term must be less than 100 characters.';
    }
}

// Fetch the total number of records with search
$total_sql = "
    SELECT COUNT(*) AS total
    FROM tasks
    JOIN employee ON tasks.emp_id = employee.emp_id
    WHERE employee.first_name LIKE '%" . $conn->real_escape_string($search_query) . "%'
";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

// Fetch the paginated results with search
$sql = "
    SELECT tasks.*, employee.first_name
    FROM tasks
    JOIN employee ON tasks.emp_id = employee.emp_id
    WHERE employee.first_name LIKE '%" . $conn->real_escape_string($search_query) . "%'
    LIMIT $results_per_page OFFSET $offset
";
$result = $conn->query($sql);

// Calculate the total number of pages
$total_pages = ceil($total_records / $results_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include('vendor/inc/head.php') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/css/task.css?v=1.0"> <!-- Link to your CSS file -->
</head>
<body>
<?php include('vendor/inc/nav.php') ?>
<h2 class="h2">Task Reports</h2>
<div class="contain">
    <div class="input-container">
        <input list="employee-options" id="employee-input" placeholder="Search Emp ID / Name">
        <i class="fa fa-search search-icon"></i>
        <datalist id="employee-options">
            <?php foreach ($employees as $emp_id => $emp_name): ?>
                <option value="<?php echo $emp_id . ' - ' . $emp_name; ?>"></option>
            <?php endforeach; ?>
        </datalist>
    </div>

    <div class="button-container">
        <a href="add_task.php">
            <i class="fa-solid fa-plus"></i> Add Task
        </a>
    </div>
    <?php if (isset($error_message)) : ?>
        <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Task ID</th>
                <th>Employee ID</th>
                <th>Project ID</th>
                <th>Task Title</th>
                <th>Task Description</th>
                <th>Type</th>
                <th>Team Name</th>
                <th>Team Count</th>
                <th>Team Leader</th>
                <th>Assigned To</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $s_no = $offset + 1; // Adjust serial number to match current page
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $s_no++ . "</td>";
                    echo "<td>" . $row["task_id"] . "</td>";
                    echo "<td>" . $row["emp_id"] . "</td>";
                    echo "<td>" . $row["project_id"] . "</td>";
                    echo "<td>" . $row["task_title"] . "</td>";
                    echo "<td>" . $row["task_description"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["team_name"] . "</td>";
                    echo "<td>" . $row["team_count"] . "</td>";
                    echo "<td>" . $row["team_leader"] . "</td>";
                    echo "<td>" . $row["assigned_to"] . "</td>";
                    echo "<td>" . $row["due_date"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["priority"] . "</td>";
                    echo "<td>
                            <a class='edit' href=\"task_edit.php?task_id=" . $row['task_id'] . "\">Edit</a> <br> <br>
                            <a class='delete' href=\"task_delete.php?task_id=" . $row['task_id'] . "\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='15'>No tasks found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
