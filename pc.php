<?php
session_start();
include("connection.php");
if(isset($_POST['submit'])) {
    $semail = $_SESSION['email']; 
    $email = $_POST['email'];
    $accno = $_POST['accno'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    // Server-side validation
    if (empty($accno) || empty($newPassword) || empty($confirmPassword) || $newPassword !== $confirmPassword) {
		echo "<script>alert('Invalid input or passwords do not match!');</script>";
		echo "<script>window.location.href = 'pc.html';</script>";
        exit;
    }
	if (!preg_match('/^(?=.*[0-9])(?=.*[@#^*]).{7,12}$/', $newPassword)) {
    echo "<script>alert('Password must be 7-12 characters long and must include at least one number and special character like @#^*');</script>";
    echo "<script>window.location.href = 'pc.html';</script>";
    exit;
}

    // Check if the account number exists in the database
    $query = "SELECT * FROM user WHERE AccountNo = '$accno' AND Email='$email'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) == 0) {
		echo "<script>alert('Account Not Found! Please Check Your Account Number and Email Again.');</script>";
		echo "<script>window.location.href = 'pc.html';</script>";
        exit;
    }
	
    
    $check_pwd = "SELECT * FROM user WHERE Password='$confirmPassword'";
        $check_resultpwd = mysqli_query($con, $check_pwd);
        if(mysqli_num_rows($check_resultpwd) > 0) {
            echo "<script>alert('Password already exists. Please enter a different one.');</script>";
            echo "<script>window.location.href = 'pc.html';</script>";
            exit;
        }
		// Update the password in the database
    $updateQuery = "UPDATE user SET Password = '$confirmPassword' WHERE AccountNo = '$accno'";
    if (mysqli_query($con, $updateQuery)) {
		echo "<script>alert('Password changed successfully!Please Login with new password.');</script>";
		echo "<script>window.location.href = 'bms.html';</script>";
    } else {
		echo "<script>alert('Error updating password. Try Again!');</script>";
		echo "<script>window.location.href = 'pc.html';</script>";
    }

    // Close database connection
    mysqli_close($con);
}
?>
