<?php
    // Check if the form is submitted
	include("connection.php");
    if(isset($_POST['request'])) {
        // Retrieve form data
        $username = $_POST['username'];
        $acctype = $_POST['acctype'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $password = $_POST['password'];
		$pin=$_POST['pin'];
        $initialdeposit = $_POST['initialdeposit'];
        $dor = date('Y-m-d');   // Current date
        
        // Validate phone number
        if(strlen($contact) !== 10 || !ctype_digit($contact)) {
            echo "<script>alert('Invalid phone number. Please enter a 10-digit numeric value.');</script>";
            echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
        }
        
        // Validate email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email address. Please enter a valid email address.');</script>";
            echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
        }
        
        // Validate password
       if (!preg_match('/^(?=.*[0-9])(?=.*[@#^*]).{7,12}$/', $password)) {
    echo "<script>alert('Password must be 7-12 characters long and must include at least one number and special character like @#^*');</script>";
    echo "<script>window.location.href = 'aadd.php';</script>";
    exit;
}

		// Validate the PIN number
		if(empty($pin)||!preg_match("/^[0-9]{4}$/", $pin)) 
		{
			echo "<script>alert('PIN number must be a 4-digit number.');</script>";
			echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
		}
        // Generate a unique 16-digit account number
        $accountNo = '1625000' . mt_rand(100000000, 999999999);
        
           //deposit for normal and fixed account 
        /*if ($accountType == 'fixed') {
    if ($initialDeposit < 100000 || $initialDeposit > 1000000) {
        echo "<script>alert('For fixed accounts, the initial deposit must be in the range of 100000 to 1000000.');</script>";
        echo "<script>window.location.href = 'rnaa.html';</script>";
        exit;
    }
	} else {*/
	
      if ($initialdeposit < 1000 || $initialdeposit > 500000) {
        echo "<script>alert('Initial deposit must be in the range of 1000 to 500000.');</script>";
        echo "<script>window.location.href = 'rnaa.html';</script>";
        exit;
    }
	//}
        /*Check if contact number already exists
        $check_query = "SELECT * FROM user WHERE ContactNo='$contact'";
        $check_result = mysqli_query($con, $check_query);
        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Contact number already exists. Please enter a different contact number.');</script>";
            echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
        }
		*/
        // Check if password already exists
        $check_pwd = "SELECT * FROM user WHERE Password='$password'";
        $check_resultpwd = mysqli_query($con, $check_pwd);
        if(mysqli_num_rows($check_resultpwd) > 0) {
            echo "<script>alert('Password already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
        }

        /*Check if email already exists
        $check_email = "SELECT * FROM user WHERE Email='$email'";
        $check_resultemail = mysqli_query($con, $check_email);
        if(mysqli_num_rows($check_resultemail) > 0) {
            echo "<script>alert('Email already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'aadd.php';</script>";
            exit;
        }
		*/
		
		// Prepare the fixed period field, if applicable
		$fixedPeriod = null;
		if ($acctype == 'Fixed') {
        $fixedPeriod = $_POST['fixedPeriod'];
			if (empty($fixedPeriod) || !is_numeric($fixedPeriod) || $fixedPeriod < 1 || $fixedPeriod > 5) {
				echo "<script>alert('The fixed period is for 1 to 5 years only. Please input a valid period.');</script>";
				echo "<script>window.location.href = 'rnaa.html';</script>";
				exit;
			}
		}
		
        // Prepare and execute the query to insert data into the user table
        $insert = "INSERT INTO user (AccountNo, Username, AccountType, FixedPeriod, DOB, Gender, ContactNo, Email, Password, PinNo, LastInterestCalculation, InterestAmount, InitialDeposit, Balance, RegistrationDate) VALUES ('$accountNo', '$username', '$acctype','$fixedPeriod', '$dob', '$gender', '$contact', '$email', '$password', '$pin',0, 0, '$initialdeposit','$initialdeposit', '$dor')";
        $result = mysqli_query($con, $insert) or die("Insertion Error");

        // Close the database connection
        mysqli_close($con);

        // Display success message and redirect
        echo "<script>alert('New Account Registration Successful! Please do change your password and pin as soon as you first login!');</script>";
        echo "<script>window.location.href = 'adb.php';</script>";
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>AddUsers</title>
		<link rel="stylesheet" type="text/css" href="aadd.css"/>
	</head>
	<body>
		<div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Admin Mode</h2></b>
			<div id="image">
				<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
			</div>
			<div id="aadd">
				<form action="#" method="POST">
					<br/>
					<label>Username:</label>
					<input type="text" id="username" name="username" required><br/>
					<label>AccountType:</label><br/>
					<input type="radio" id="current" name="acctype" value="Current Deposit" onclick="toggleFixedPeriod(false)">Current<br/>
					<input type="radio" id="fixed" name="acctype" value="Fixed" onclick="toggleFixedPeriod(true)">Fixed <br/>
					<input type="radio" id="savings" name="acctype" value="Savings" onclick="toggleFixedPeriod(false)">Savings	<br/>	
					
					<div id="fixedPeriodD" style="display: none;">
						<label>Fixed Period:</label>
						<input type="number" id="fixedPeriod" name="fixedPeriod" placeholder="Fixed period is for 1-5 years only." min="1" max="5"><br/>
					</div>			
					<label>DOB:</label>
					<input type="date" id="dob" name="dob" required><br/>
					<label>Gender:</label>
					<input type="text" id="gender" name="gender" required><br/>
					<label>ContactNo:</label>
					<input type="number" id="contact" name="contact" placeholder="Enter your 10-digit phone number." required><br/>
					<label>Email:</label>
					<input type="email" id="email" name="email" required><br/>
					<label>Password:</label>
					<input type="password" id="password" name="password" placeholder="atleast7-12 characters,0-9,@#^*special char" required><br/>
					<label>Pin No:</label>
					<input type="password" id="pin" name="pin" maxlength="4" pattern="\d{4}" placeholder="Enter a 4-digit numeric PIN number." required><br/>
					<label>Initial Deposit:</label>
					<input type="number" id="initialdeposit" name="initialdeposit" placeholder="Must be in range of 1000 to 500000." required><br/>
					<input type="hidden" id="dor" name="dor"/>
					<input type="submit" name="request" value="Add Account" id="request"/>
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
				 function toggleFixedPeriod(show) {
                    document.getElementById('fixedPeriodD').style.display = show ? 'block' : 'none';
                    document.getElementById('fixedPeriod').required = show;
                }
			</script>
			</div>
		</div>
	</body>
</html>
			