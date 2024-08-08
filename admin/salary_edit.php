<?php
session_start();
include('vendor/inc/connection.php');

if (!isset($_SESSION['a_id'])) {
    header("Location: a-login.php");
    exit();
}

// Initialize variables
$emp_id = $salary = $bonus = $total = '';

// Fetch salary data
if (isset($_GET['emp_id'])) {
    $emp_id = mysqli_real_escape_string($conn, $_GET['emp_id']);
    $query = "SELECT * FROM salary WHERE emp_id='$emp_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $salary = $row['salary'];
        $bonus = $row['bonus'];
        $total = $row['total'];
    } else {
        echo "<script>alert('Salary record not found'); window.location.href='salary_list.php';</script>";
        exit();
    }
}

// Handle form submission
if (isset($_POST['update'])) {
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $bonus = mysqli_real_escape_string($conn, $_POST['bonus']);
    $total = mysqli_real_escape_string($conn, $_POST['total']);

    $update_sql = "UPDATE salary SET 
                    salary='$salary', 
                    bonus='$bonus', 
                    total='$total' 
                    WHERE emp_id='$emp_id'";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>
                alert('Successfully Updated');
                window.location.href='salary_list.php';
              </script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/emp-edit.css">
    <style>
        .input--style {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .input-margin-top {
            margin-top: 20px;
        }

        .p-t-20 {
            text-align: center;
        }

        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
        }
    </style>
</head>
<body>    
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="h2">Edit Salary Details</h2>
                    <hr>
                    <form action="salary_edit.php?emp_id=<?php echo $emp_id; ?>" method="POST">
                        <div class="input-group">
                            <label for="salary">Base Salary:</label>
                            <input class="input--style input-margin-top" type="text" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>" required>
                        </div>

                        <div class="input-group">
                            <label for="bonus">Bonus:</label>
                            <input class="input--style input-margin-top" type="text" id="bonus" name="bonus" value="<?php echo htmlspecialchars($bonus); ?>" required>
                        </div>

                        <div class="input-group">
                            <label for="total">Total Salary:</label>
                            <input class="input--style input-margin-top" type="text" id="total" name="total" value="<?php echo htmlspecialchars($total); ?>" required>
                        </div>

                        <div class="p-t-20">
                            <button class="btn" type="submit" name="update">Update Salary</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
