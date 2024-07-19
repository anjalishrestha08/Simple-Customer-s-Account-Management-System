<?php
session_start();
include("connection.php");

// Check if the user is logged in
if(isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    
    // Prepare the query to fetch the account number of the logged-in user
    $select = "SELECT AccountNo, Username FROM user WHERE Email=?";
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
    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserHomepage</title>
    <link rel="stylesheet" type="text/css" href="udb.css">
</head>
<body>
   <div class="container">
		<h1><b style="color: darkgreen;">BMS</h1>
		<br/><h2>Customer's Account Management System</h2></b>
			<div id="image">
				<center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
			</div>
        <h3>Welcome to Your Bank,<?php echo $username.". "; ?></br>Account number: <?php echo $accountNo; ?></h3>
        <div id="menu">
            <ul>
                <li><div id="menu-Statements" onclick="loadContent('us.php')"> Statements</div></li>
				<li><div id="menu-Balance" onclick="loadContent('b.php')"> Balance</div></li>
				<li><div id="menu-DepositFunds" onclick="loadContent('df.php')"> Deposit Funds</div></li>
				<li><div id="menu-WithdrawMoney" onclick="loadContent('wd.php')"> Withdraw Money</div></li>
                <li><div id="menu-TransferMoney" onclick="loadContent('tm.php')"> Transfer Money</div></li>
                <li><div id="menu-ReportProblem" onclick="loadContent('rp.php')"> Report a Problem</div></li>
            </ul>
        </div>
		<div id="nav">
			<nav id="home-nav">
				<a href="..\SummerProject\udb.php">HOME</a>
				<a href="..\SummerProject\pf.php">PROFILE</a>
			</nav>
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
            window.location.href = 'ulogout.php';
        }
    </script>
    </div>
</body>
</html>
