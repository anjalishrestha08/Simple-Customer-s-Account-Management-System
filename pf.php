<?php
session_start();
include("connection.php");

// Check if the user is logged in
if(isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    // Prepare the query to fetch the account number of the logged-in user
    $select = "SELECT AccountNo, Username, AccountType, DOB, Gender,ContactNo, Balance, RegistrationDate FROM user WHERE Email=?";
    $stmt = mysqli_prepare($con, $select);
    
    // Bind parameter and execute the query
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    
    // Fetch the account number
    $row = mysqli_fetch_assoc($result);
    $accountNo = $row['AccountNo'];
	$username=$row['Username'];
	$acctype = $row['AccountType'];
	$dob = $row['DOB'];
	$gender=$row['Gender'];
	$contact=$row['ContactNo'];
	$balance=$row['Balance'];
	$rgd=$row['RegistrationDate'];
    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" type="text/css" href="pf.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Customer's Account Management System</h2></b>
		<div id="image">
			<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
		</div>
		<div id="text">
		    <b>Username:</b><?php echo " ".$username.". "; ?></br><b>Account Number:</b> <?php echo " ". $accountNo; ?>
			</br> <b>Account Type:</b><?php echo " ". $acctype.". "; ?></br><b>D.O.B:</b> <?php echo " ".$dob; ?>
			</br> <b>Gender:</b><?php echo " ".$gender.". "; ?></br> <b>ContactNo:</b><?php echo " ".$contact.". "; ?></br><b>Email ID:</b> <?php echo " ".$email; ?>
			</br> <b>Balance:</b> Rs.<?php echo " ".$balance.". "; ?></br><b>Registration Date: </b><?php echo " ".$rgd; ?>	 
			<div id="menu-edit" onclick="loadContent('editpf.php')"> Edit</div>
		</div>
		<div id="menu">
            <ul>
                <li><div id="menu-Password" onclick="loadContent('pfcpw.php')"> Change Password?</div></li>
				<li><div id="menu-Pin" onclick="loadContent('pfcpi.php')"> Change Pin?</div></li>
				<li><div id="menu-Del" onclick="loadContent('pfad.php')"> Delete Account?</div></li>
            </ul>
        </div>
		<div id="nav">
			<nav id="home-nav">
				<a href="..\SummerProject\udb.php">HOME</a>
				<a href="..\SummerProject\pf.php">PROFILE</a>
			</nav>
		</div>
		<script>
        function loadContent(page)
		{
             window.location.href = page;
		}
		</script>
	</div>
</body>
</html>	