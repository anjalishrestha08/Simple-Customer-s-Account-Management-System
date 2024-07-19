<?php
session_start();
include("connection.php"); 

if(isset($_POST['Login']))  {
    $adminId = $_POST['aid'];
    $adminPassword = $_POST['password'];

    $query = "SELECT * FROM admin WHERE AdminID='$adminId' AND AdminPassword='$adminPassword'";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 1) {
        $_SESSION['adminId'] = $adminId;
        echo "<script>window.location.href = 'adb.php';</script>";
    } else {
        echo "<script>alert('Incorrect credentials. Please try again.');</script>";
		echo "<script>window.location.href = 'adlf.html';</script>";
    }
}
?>
