<?php
session_start();
include("connection.php");
// Check if the admin is logged in
if(isset($_SESSION['adminId'])) {
    $adminId = $_SESSION['adminId'];
} else {
    // If the admin is not logged in, redirect to login page
	echo "<script>alert('Please Login');</script>";
    echo "<script>window.location.href = 'adlf.html';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminDashboard</title>
    <link rel="stylesheet" type="text/css" href="adb.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Admin Dashboard</h2></b>
		<div id="image">
			<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
		</div>
		<h3>Welcome, Admin <?php echo $adminId; ?></h3>
        <div id="menu">
            <ul>
                <li><div id="menu-ViewUsers" onclick="loadContent('aview.php')"> View Users</div></li>
				<li><div id="menu-AddUser" onclick="loadContent('aadd.php')"> Add User</div></li>
				<li><div id="menu-UpdateUser" onclick="loadContent('aupd.php')"> Update User</div></li>
                <li><div id="menu-DeleteUser" onclick="loadContent('adel.php')"> Delete User</div></li>
				<li><div id="menu-ViewProblems" onclick="loadContent('arp.php')"> View Reported Problems</div></li>
            </ul>
        </div>
		<div id="logout">
            <button id="logoutButton" onclick="logout()" type="button">LOGOUT</button>
        </div>
		<script>
        function loadContent(page)
		{
             window.location.href = page;
		}
		function logout()
		{
            window.location.href = 'alogout.php'; 
        }
    </script>
    </div>
</body>
</html>
