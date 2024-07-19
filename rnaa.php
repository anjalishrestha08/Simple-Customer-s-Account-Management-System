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
            echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
        }
        
        // Validate email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email address. Please enter a valid email address.');</script>";
            echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
        }
        
        // Validate password
       if (!preg_match('/^(?=.*[0-9])(?=.*[@#^*]).{7,12}$/', $password)) {
    echo "<script>alert('Password must be 7-12 characters long & special character like @#^*' and must include at least one number  );</script>";
    echo "<script>window.location.href = 'rnaa.html';</script>";
    exit;
}

		// Validate the PIN number
		if(empty($pin)||!preg_match("/^[0-9]{4}$/", $pin)) 
		{
			echo "<script>alert('PIN number must be a 4-digit number.');</script>";
			echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
		}
        // Generate a unique 16-digit account number
        $accountNo = '1625000' . mt_rand(100000000, 999999999);
		
    if ($initialdeposit < 1000 || $initialdeposit > 500000) {
        echo "<script>alert('Initial deposit must be in the range of 1000 to 500000.');</script>";
        echo "<script>window.location.href = 'rnaa.html';</script>";
        exit;
    }
	
		// Check if pin number already exists
        $check_query = "SELECT * FROM user WHERE PinNO='$pin'";
        $check_result = mysqli_query($con, $check_query);
        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Pin Number already exists. Please enter a different PinNo.');</script>";
            echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
        }
        /*Check if contact number already exists
        $check_query = "SELECT * FROM user WHERE ContactNo='$contact'";
        $check_result = mysqli_query($con, $check_query);
        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Contact number already exists. Please enter a different contact number.');</script>";
            echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
        }
		*/
        // Check if password already exists
        $check_pwd = "SELECT * FROM user WHERE Password='$password'";
        $check_resultpwd = mysqli_query($con, $check_pwd);
        if(mysqli_num_rows($check_resultpwd) > 0) {
            echo "<script>alert('Password already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
        }
		/*
         Check if email already exists
        $check_email = "SELECT * FROM user WHERE Email='$email'";
        $check_resultemail = mysqli_query($con, $check_email);
        if(mysqli_num_rows($check_resultemail) > 0) {
            echo "<script>alert('Email already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'rnaa.html';</script>";
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
        echo "<script>alert('New Account Registration Successful!');</script>";
        echo "<script>window.location.href = 'bms.html';</script>";
    }
?>
