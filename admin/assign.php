<?php 
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Query to fetch project details for the table
$sql = "SELECT 
            project.project_id, 
            project.p_name, 
            project.desc, 
            project.client, 
            project.code_lan, 
            project.start_date, 
            project.end_date, 
            project.status, 
            project.priority, 
            project.created_at, 
            project.updated_at
        FROM project";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/css/assign.css?v=1.0">
    <?php include('vendor/inc/head.php'); ?>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
<?php include('vendor/inc/nav.php'); ?>
<h2 class="h2">Project Details</h2>
<div>
    <div class="input-container">
        <input id="project-input" placeholder="Search Project ID / Name">
        <i class="fa fa-search search-icon"></i>
    </div>

    <!-- <div class="date-filter">
        <label for="start-date">Start Date:</label>
        <input type="date" id="start-date">
        <label for="end-date">End Date:</label>
        <input type="date" id="end-date">
    </div> -->

    <button class="add-project">
        <a href="add-project.php">
            <i class="fa-solid fa-plus"></i> Add Project
        </a>
    </button>
</div>

<div class="contain">
    <table id="project-table">
        <thead>
            <tr>
                <th align="center">S No</th>
                <th align="center">Project ID</th>
                <th align="center">Name</th>
                <th align="center">Description</th>
                <th align="center">Client</th>
                <th align="center">Code Language</th>
                <th align="center">Start Date</th>
                <th align="center">End Date</th>
                <th align="center">Status</th>
                <th align="center">Priority</th>
                <th align="center">Created At</th>
                <th align="center">Updated At</th>
                <th align="center">Options</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter = 1; // Initialize the counter to 1
                while ($project = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='project-id'>{$counter}</td>"; // Use the counter value for S No
                    echo "<td class='project-id'>".$project['project_id']."</td>";
                    echo "<td class='project-name'>".$project['p_name']."</td>";
                    echo "<td>".$project['desc']."</td>";
                    echo "<td>".$project['client']."</td>";
                    echo "<td>".$project['code_lan']."</td>";
                    echo "<td class='start-date'>".$project['start_date']."</td>";
                    echo "<td class='end-date'>".$project['end_date']."</td>";
                    echo "<td>".$project['status']."</td>";
                    echo "<td>".$project['priority']."</td>";
                    echo "<td>".$project['created_at']."</td>";
                    echo "<td>".$project['updated_at']."</td>";
                    echo "<td><a class='edit' href=\"edit-project.php?project_id=".$project['project_id']."\">Edit</a> <br> <br>
                          <a class='delete' href=\"delete-project.php?project_id=".$project['project_id']."\" onClick=\"return confirm('Are you sure you want to delete?')\">Delete</a></td>";
                    echo "</tr>";
                    $counter++; // Increment the counter after each row
                }
            ?>
        </tbody>
    </table>
</div>
<script>
    document.getElementById('project-input').addEventListener('input', function() {
    // Get input value and convert it to lowercase for case-insensitive comparison
    var textInput = document.getElementById('project-input').value.toLowerCase();

    // Get the table and its rows
    var table = document.getElementById('project-table');
    var rows = table.getElementsByTagName('tr');

    // Loop through all table rows (skip the first row, which is the header)
    for (var i = 1; i < rows.length; i++) {
        // Get the Project ID and Project Name columns from the current row
        var projectId = rows[i].getElementsByClassName('project-id')[1]?.textContent.toLowerCase(); 
        var projectName = rows[i].getElementsByClassName('project-name')[0]?.textContent.toLowerCase();

        // Check if either Project ID or Project Name contains the search input
        if (projectId && projectName && (projectId.includes(textInput) || projectName.includes(textInput))) {
            rows[i].style.display = ''; // Show the row
        } else {
            rows[i].style.display = 'none'; // Hide the row
        }
    }
});
</script>

</body>
</html>
