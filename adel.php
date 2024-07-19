<?php
    // Include database connection file
    include("connection.php");
    
    // Check if form is submitted
    if(isset($_POST['delete'])) {
        // Retrieve form data
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Validate input (You can add more validation as per your requirements)
        if(empty($username) || empty($email)) {
            echo "<script>alert('Please provide both username and email.');</script>";
            echo "<script>window.location.href = 'adel.php';</script>";
            exit;
        }

        // Check if the user exists
        $check_user_query = "SELECT * FROM user WHERE Username='$username' AND Email='$email'";
        $check_user_result = mysqli_query($con, $check_user_query);

        // If the user exists, proceed with deletion
        if(mysqli_num_rows($check_user_result) > 0) {
            // Fetch user data
            $user_data = mysqli_fetch_assoc($check_user_result);
			$user_balance = $user_data['Balance'];
			$account_type = $user_data['AccountType'];
			$fixed_period = $user_data['FixedPeriod'];
			$registration_date = $user_data['RegistrationDate'];

		// Check if the account type is 'Fixed' and if the fixed period has ended
        if ($account_type == 'Fixed') {
            $fixed_period_end = date('Y-m-d', strtotime($registration_date . ' + ' . $fixed_period . ' days'));
            $current_date = date('Y-m-d');
            if ($current_date < $fixed_period_end) {
                echo "<script>alert('Account cannot be deleted until the fixed period has ended.');</script>";
                echo "<script>window.location.href = 'adel.php';</script>";
                exit;
            }
        }
		// Check if balance is less than Rs.100
        if ($user_balance >= 100) {
            echo "<script>alert('The User need to withdraw his/her Balance.');</script>";
            echo "<script>window.location.href = 'adel.php';</script>";
            exit;
        }
            // Archive user data
            $archive_query = "INSERT INTO archive (UId, AccountNo,Username, Email, Balance, Date_deleted) VALUES ('{$user_data['UId']}', '{$user_data['AccountNo']}','$username', '$email', '{$user_data['Balance']}', NOW())";
            mysqli_query($con, $archive_query);

            // Delete related records from the withdrawal table
            $delete_withdrawals_query = "DELETE FROM withdrawal WHERE UId='{$user_data['UId']}'";
            mysqli_query($con, $delete_withdrawals_query);

            // Delete user record from main user table
            $delete_query = "DELETE FROM user WHERE Username='$username' AND Email='$email'";
            if(mysqli_query($con, $delete_query)) {
                echo "<script>alert('User record deleted successfully.');</script>";
            } else {
                echo "<script>alert('Error deleting user record.');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DeleteUsers</title>
		<link rel="stylesheet" type="text/css" href="adel.css"/>
	</head>
	<body>
		<div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Admin Mode</h2></b>
			<div id="image">
				<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
			</div>
			<div id="text">
				Delete a User Account:
			</div>
			<div id="adel">
				<form action="#" method="POST">
					<br/>
					<label>Username:</label>
					<input type="text" id="username" name="username" required><br/>
					<label>Email:</label>
					<input type="email" id="email" name="email" required><br/>
					<input type="submit" name="delete" value="Delete Account" id="request"/>
					<input type="reset" name="reset" value="Reset" id="reset"/>
				</form>
				<div >
				<button id="goback" onclick="redirectToP()" type="button">Cancel</button>
			</div>
			<script>
				 function redirectToP()
				{
					window.location.href = 'adb.php';
				}
			</script>
			</div>
		</div>
	</body>
</html>
			