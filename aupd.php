<?php
    // Check if the form is submitted
    include("connection.php");
    if(isset($_POST['update'])) 
    {
        // Retrieve form data
        $accountno = $_POST['accountno'];
        // Check if account number exists
        $check_accno = "SELECT * FROM user WHERE AccountNo='$accountno'";
        $check_resultaccno = mysqli_query($con, $check_accno);
        if(mysqli_num_rows($check_resultaccno) == 0) {
            echo "<script>alert('Account does not exist.');</script>";
            echo "<script>window.location.href = 'aupd.php';</script>";
            exit;
        }
        // Fetch existing user data
        $user_data_query = "SELECT * FROM user WHERE AccountNo = '$accountno'";
        $user_data_result = mysqli_query($con, $user_data_query);
        // Check if the query was successful
        if ($user_data_result) {
            $row = mysqli_fetch_assoc($user_data_result);
            
           // Populate form fields with existing data if form data is empty
			$username = !empty($_POST['username']) ? $_POST['username'] : $row['Username'];
			$acctype = !empty($_POST['acctype']) ? $_POST['acctype'] : $row['AccountType'];
			$dob = !empty($_POST['dob']) ? $_POST['dob'] : $row['DOB'];
			$gender = !empty($_POST['gender']) ? $_POST['gender'] : $row['Gender'];
			$contact = !empty($_POST['contact']) ? $_POST['contact'] : $row['ContactNo'];
			$email = !empty($_POST['email']) ? $_POST['email'] : $row['Email'];
        } else {
            echo "<script>alert('Error fetching user data.');</script>";
            echo "<script>window.location.href = 'aupd.php';</script>";
            exit;
        }
        // Validate phone number
        if(!empty($contact) && (strlen($contact) !== 10 || !ctype_digit($contact))) {
            echo "<script>alert('Invalid phone number. Please enter a 10-digit numeric value.');</script>";
            echo "<script>window.location.href = 'aupd.php';</script>";
            exit;
        }
        
        // Validate email address
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email address. Please enter a valid email address.');</script>";
            echo "<script>window.location.href = 'aupd.php';</script>";
            exit;
        }
		
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
		
        // Prepare SQL query to update user data
        $update_query = "UPDATE user SET Username='$username', AccountType='$acctype',FixedPeriod='$fixedPeriod',DOB='$dob',Gender='$gender',ContactNo='$contact',Email='$email' WHERE AccountNo='$accountno'";
        if(mysqli_query($con, $update_query)) 
        {
            echo "<script>alert('User data updated successfully.');</script>";
            echo "<script>window.location.href = 'adb.php';</script>";
            exit;
        } else 
        {
            echo "<script>alert('Error updating user data.');</script>";
            echo "<script>window.location.href = 'aupd.php';</script>";
            exit;
        }
        // Close the database connection
        mysqli_close($con);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UpdateUsers</title>
        <link rel="stylesheet" type="text/css" href="aupd.css"/>
    </head>
    <body>
        <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <br/><h2>Admin Mode</h2></b>
            <div id="image">
                <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
            </div>
			<div id="text">
				Update Account:
			</div>
            <div id="aupd">
                <form action="#" method="POST">
                    <br/>
                    <label>AccountNo:</label>
                    <input type="number" id="accountno" name="accountno" required><br/>
                    <label>Username:</label>
                    <input type="text" id="username" name="username" ><br/>
                    <label>AccountType:</label><br/>
					<input type="radio" id="current" name="acctype" value="Current Deposit" onclick="toggleFixedPeriod(false)">Current<br/>
					<input type="radio" id="fixed" name="acctype" value="Fixed" onclick="toggleFixedPeriod(true)">Fixed <br/>
					<input type="radio" id="savings" name="acctype" value="Savings" onclick="toggleFixedPeriod(false)">Savings	<br/>	
					
					<div id="fixedPeriodD" style="display: none;">
						<label>Fixed Period:</label>
						<input type="number" id="fixedPeriod" name="fixedPeriod" placeholder="Fixed period is for 1-5 years only." min="1" max="5"><br/>
					</div>               
                    <label>DOB:</label>
                    <input type="date" id="dob" name="dob" ><br/>
                    <label>Gender:</label>
                    <input type="text" id="gender" name="gender" ><br/>
                    <label>ContactNo:</label>
                    <input type="number" id="contact" name="contact" placeholder="Enter your 10-digit phone number." ><br/>
                    <label>Email:</label>
                    <input type="email" id="email" name="email" ><br/>
                    <input type="submit" name="update" value="Update Account" id="update"/>
                    <input type="reset" name="reset" value="Reset" id="reset"/>
                </form>
                <div >
                <button id="goback" onclick="redirectToP()" type="button">Cancel</button>
            </div>
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
    </body>
</html>
