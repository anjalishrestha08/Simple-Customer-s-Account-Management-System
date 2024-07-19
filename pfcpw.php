<?php
session_start();
include("connection.php");

if(isset($_POST['changepwd'])) {
    $email = $_SESSION['email'];
    $accno = $_POST['accno'];	
    $password = $_POST['oldpassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Retrieve current password from the database
    $query = "SELECT Password FROM user WHERE AccountNo = '$accno'"; 
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);
    $current_password = $row['Password'];  

    // Server-side validation
    if($password == $current_password) {
        if ( empty($accno)||empty($newPassword) || empty($confirmPassword) || $newPassword !== $confirmPassword) {
            echo "<script>alert('Invalid input or passwords do not match!');</script>";
            echo "<script>window.location.href = 'pfcpw.php';</script>";

            exit;
        }
        if (!preg_match('/^(?=.*[0-9])(?=.*[@#^*]).{7,12}$/', $newPassword)) {
            echo "<script>alert('Password must be 7-12 characters long and must include at least one number and special character like @#^*');</script>";
            echo "<script>window.location.href = 'pfcpw.php';</script>";

            exit;
        }    
        $check_pwd = "SELECT * FROM user WHERE Password='$confirmPassword'"; // This query seems unnecessary, as it checks for an exact match of the new password
        $check_resultpwd = mysqli_query($con, $check_pwd);
        if(mysqli_num_rows($check_resultpwd) > 0) {
            echo "<script>alert('Password already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'pfcpw.php';</script>";
            exit;
        }
		// Update the password in the database
    $updateQuery = "UPDATE user SET Password = '$confirmPassword' WHERE AccountNo = '$accno'";
    if (mysqli_query($con, $updateQuery)) {
            echo "<script>alert('Password changed successfully! Please login with the new password.');</script>";
            echo "<script>window.location.href = 'bms.html';</script>";
			echo "<script>window.location.href = 'pfcpw.php';</script>";

        } else {
            echo "<script>alert('Error updating password. Try Again!');</script>";
            echo "<script>window.location.href = 'pfcpw.php';</script>";

        }
    } else {
        echo "<script>alert('Incorrect current password.');</script>";
        echo "<script>window.location.href = 'pfcpw.php';</script>";

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
    <link rel="stylesheet" type="text/css" href="pfcpw.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Customer's Account Management System</h2></b>
		<div id="image">
			<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
		</div>
		<h3>Change Your Password:</h3>
		<div id="change_pwd">
			<form action="#" method="POST">
			<br/>
			<label>AccountNo:</label>
            <input type="number" id="accno" name="accno" required><br/>
			<label>Current Password:</label>
            <input type="password" id="oldpassword" name="oldpassword" required><br>
			<label>New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required><br>
			<label>Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required><br>
            <input type="submit" name="changepwd" value="Change Password">
			<button id="goback" onclick="redirectToP()" type="button">Cancel</button>
			<br/>
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
