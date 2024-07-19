<?php
session_start();
include("connection.php");

// Check if the user is logged in
if(isset($_SESSION['email'])) {
	$email = $_SESSION['email']; 
	
	// Fetch the account number of the logged-in user from the database
    $get_account_query = "SELECT AccountNo, AccountType, FixedPeriod, RegistrationDate FROM user WHERE Email = '$email'";
    $result_account = mysqli_query($con, $get_account_query);
    $row_account = mysqli_fetch_assoc($result_account);
    $account = $row_account['AccountNo'];
	$accountType = $row_account['AccountType'];
    $fixedPeriod = $row_account['FixedPeriod'];
    $registrationDate = new DateTime($row_account['RegistrationDate']);
	
	// Get username of the account holder
    $get_username = "SELECT Username FROM user WHERE Email = '$email'";
    $result_username = mysqli_query($con, $get_username);
    $row_username = mysqli_fetch_assoc($result_username);
    $username = $row_username['Username'];
	if(isset($_POST['amount']) && isset($_POST['pin']) && isset($_POST['remarks'])) {
    $amount = $_POST['amount'];
    $pin = $_POST['pin'];
    $remarks = $_POST['remarks'];

    /* Check if the provided account number matches the logged-in user's account
    $check_account = "SELECT * FROM user WHERE AccountNo = '$account' AND Email = '$email'";
    $result_account = mysqli_query($con, $check_account);
    if(mysqli_num_rows($result_account) == 0) {
        echo "<script>alert('Account doesn't match logged-in Email!');</script>"; 
        echo "<script>window.location.href = 'df.php';</script>";
        exit;
    }
	*/
    // Verify PIN
    $check_pin_query = "SELECT * FROM user WHERE Email = '$email' AND PinNo = '$pin'";
    $result_pin = mysqli_query($con, $check_pin_query);
    if (mysqli_num_rows($result_pin) == 0) {
        echo "<script>alert('Incorrect PIN.');</script>";
        echo "<script>window.location.href = 'df.php';</script>";
        exit;
    }
	
	// Check for fixed account type and fixed period
        if ($accountType == 'Fixed') {
            $currentDate = new DateTime();
            $endFixedPeriod = clone $registrationDate;
            $endFixedPeriod->modify("+$fixedPeriod years");

            if ($currentDate < $endFixedPeriod) {
                echo "<script>alert('Deposits are disabled during the fixed period for your account.');</script>";
                echo "<script>window.location.href = 'df.php';</script>";
                exit;
            }
        }
	
    // Validate amount
    if ($amount < 1000 || $amount > 100000) {
        echo "<script>alert('Initial deposit must be in limit of 1000 to 100000.');</script>";
        echo "<script>window.location.href = 'df.php';</script>";
        exit;
    }
    // Get depositor's username
    $get_depositor = "SELECT Username FROM user WHERE Email = '$email'";
    $result_depositor = mysqli_query($con, $get_depositor);
    $row_depositor = mysqli_fetch_assoc($result_depositor);
    $deposited_by = $row_depositor['Username'];

    

    // Insert deposit transaction
    $insert_query = "INSERT INTO deposit (UId, AccountNo, Username, Deposit_Amount, Deposited_By, Remarks) VALUES ((SELECT UId FROM user WHERE AccountNo = '$account'), '$account', '$username', '$amount', '$deposited_by', '$remarks')";
    mysqli_query($con, $insert_query);

    // Update balance
    $update_balance = "UPDATE user SET Balance = Balance + $amount WHERE AccountNo = '$account'";
    mysqli_query($con, $update_balance);

    // Close the database connection
    mysqli_close($con);

    echo "<script>alert('Deposit Successful.');</script>";
    echo "<script>window.location.href = 'udb.php';</script>";
}
} else {
    // If not logged in, redirect to login page
    echo "<script>alert('Please login.');</script>";
    echo "<script>window.location.href = 'bms.html';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepositForm</title>
    <link rel="stylesheet" type="text/css" href="df.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Customer's Account Management System</h2></b>
		<div id="image">
			<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
		</div>
		<div id="df">
			<form action="df.php" method="POST">
			<br/>
			<h4>Logged In as: <?php echo " ".$username.". ";?></h4>
				<label>Account No:</label>
				<input type="number" id="uaccount" name="uaccount" value="<?php echo ($account); ?>"
				required readonly><br>
				<label>Amount:</label>
				<input type="text" id="amount" name="amount" placeholder="In limit of 1000-100000" required><br>
				<label>PinNo:</label>
				<input type="password" id="pin" name="pin" required><br>
				<label>Remarks:</label>
				<input type="text" id="remarks" name="remarks" required><br><br>
				<input type="submit" value="Deposit Money"><br/><br/>
			</form>
		</div>
		<div id="cancel">
            <button id="cancelButton" onclick="cancel()" type="button">Cancel</button>
        </div>
		<script>
		function cancel() 
		{
            window.location.href = 'udb.php'; 
        }
		</script>
	</div>
</body>
</html>

