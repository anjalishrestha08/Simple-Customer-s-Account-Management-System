<?php
session_start();
include("connection.php");

if(isset($_POST['changepin'])) {
    $email = $_SESSION['email'];
    $accno = $_POST['accno'];	
    $opin = $_POST['oldpin'];
    $npin = $_POST['newpin'];
    $cpin = $_POST['confirmpin'];

    // Retrieve current password from the database
    $query = "SELECT PinNo FROM user WHERE AccountNo = '$accno'"; 
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $current_pin = $row['PinNo'];  

    // Server-side validation
    if($opin == $current_pin) {
        if ( empty($accno)||empty($npin) || empty($cpin) || $npin !== $cpin) {
            echo "<script>alert('Invalid input or pin do not match!');</script>";
            echo "<script>window.location.href = 'pfcpi.php';</script>";

            exit;
        }
        // Validate the PIN number
		if(!preg_match("/^[0-9]{4}$/", $npin)) 
		{
			echo "<script>alert('PIN number must be a 4-digit number.');</script>";
			echo "<script>window.location.href = 'rnaa.html';</script>";
            exit;
		} 
        $check_pin = "SELECT * FROM user WHERE PinNo='$cpin'"; 
        $check_resultpin = mysqli_query($con, $check_pin);
        if(mysqli_num_rows($check_resultpin) > 0) {
            echo "<script>alert('PIN already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'pfcpi.php';</script>";
            exit;
        }
		// Update the PIN in the database
    $updateQuery = "UPDATE user SET PinNo = '$cpin' WHERE AccountNo = '$accno'";
    if (mysqli_query($con, $updateQuery)) {
            echo "<script>alert('PIN changed successfully! Please use this new PIN for transaction.');</script>";
			echo "<script>window.location.href = 'pf.php';</script>";

        } else {
            echo "<script>alert('Error updating PIN No. Try Again!');</script>";
            echo "<script>window.location.href = 'pfcpi.php';</script>";

        }
    } else {
        echo "<script>alert('Incorrect Current PIN.');</script>";
        echo "<script>window.location.href = 'pfcpi.php';</script>";

    }
	mysqli_close($con);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" type="text/css" href="pfcpi.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Customer's Account Management System</h2></b>
		<div id="image">
			<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
		</div>
		<h3>Change Your PIN:</h3>
		<div id="change_pin">
			<form action="#" method="POST">
			<label>AccountNo:</label>
            <input type="number" id="accno" name="accno" required><br/>
			<label>Current Pin:</label>
            <input type="password" id="oldpin" name="oldpin" required><br>
            <label>New PIN:</label>
            <input type="password" id="newpin" name="newpin" required><br>
            <label>Confirm PIN:</label>			
			<input type="password" id="confirmpin" name="confirmpin" required><br>
            <input type="submit" name="changepin" value="Change PIN">
            <button id="goback" onclick="redirectToP()" type="button">Cancel</button>
			</form>
		</div>
		<script>
                 function redirectToP()
                {
                    window.location.href = 'pf.php';
                }
            </script>
	</div>
</body>
</html>	
