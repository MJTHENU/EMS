<?php
session_start();

if (!isset($_SESSION['emp_id'])) {
    header("Location: emp-login.php");
    exit();
}

require_once('vendor/inc/connection.php');

$id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
$sql = "SELECT * FROM `employee_bank_details` WHERE emp_id='$id'";
$result = mysqli_query($conn, $sql);
$account = mysqli_fetch_assoc($result);

if (!$account) {
    echo "<script>alert('No account details found'); window.location.href='profile.php?emp_id=$id';</script>";
    exit();
}

// Fetch account details
$accountHolder = htmlspecialchars($account['bank_holder_name']);
$bankName = htmlspecialchars($account['bank_name']);
$accountNumber = htmlspecialchars($account['acc_no']);
$ifscCode = htmlspecialchars($account['ifsc_code']);
$branchName = htmlspecialchars($account['branch_name']);
$passbookImgSrc = !empty($account['passbook_img']) ? 'data:image/jpeg;base64,' . base64_encode($account['passbook_img']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('vendor/inc/head.php'); ?>
    <link rel="stylesheet" href="vendor/css/account.css">
    <title>View Account</title>
</head>
<body>
    <?php include('vendor/inc/nav.php'); ?>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-body">
                    <h2 class="h2">Account Details</h2>
                    <div class="account-details-container">
                        <!-- Passbook Image -->
                        <div class="passbook-img-container">
                            <p>Passbook Image</p>
                            <?php if (!empty($passbookImgSrc)): ?>
                                <img class="passbook-img" src="<?php echo $passbookImgSrc; ?>" alt="Passbook Image">
                            <?php else: ?>
                                <p>No Image Available</p>
                            <?php endif; ?>
                        </div>

                        <!-- Account Details -->
                        <div class="account-details">
                            <!-- First Row: Account Holder Name, Bank Name -->
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <p>Account Holder Name</p>
                                        <input class="input--style-1" type="text" value="<?php echo $accountHolder; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <p>Bank Name</p>
                                        <input class="input--style-1" type="text" value="<?php echo $bankName; ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Row: Account Number -->
                            <div class="input-group">
                                <p>Account Number</p>
                                <input class="input--style-1" type="text" value="<?php echo $accountNumber; ?>" readonly>
                            </div>

                            <!-- Third Row: IFSC Code, Branch Name -->
                            <div class="row">
                                <div class="col">
                                    <div class="input-group">
                                        <p>IFSC Code</p>
                                        <input class="input--style-1" type="text" value="<?php echo $ifscCode; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <p>Branch Name</p>
                                        <input class="input--style-1" type="text" value="<?php echo $branchName; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn--radius btn--blue" onclick="window.location.href='my-profile.php?emp_id=<?php echo $id; ?>';">Back to Profile</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
