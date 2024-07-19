<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin's View</title>
    <link rel="stylesheet" type="text/css" href="aview.css"/>
    
</head>
<body>
<div class="container">
    <h1><b style="color: darkgreen;">BMS</b></h1>
    <br/><h2>Customer Details</h2>
    <div id="image">
        <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
    </div>
    <?php
    session_start();
    include("connection.php");
    // Check if the user is logged in
    if(!isset($_SESSION['adminId'])) {
        // If not logged in, redirect to login page
        echo "<script>window.location.href = 'adlf.html';</script>";
        exit;
    }

    // Query to select all users
    $sql = "SELECT * FROM user";
    $result = mysqli_query($con, $sql);

    // Check if there are any users
    if (mysqli_num_rows($result) > 0) {
        // Display users in a table
        echo "<div class='user-table'>";
        echo "<table>";
        echo "<tr><th>UserID</th><th>Account Number</th><th>Username</th><th>Account Type</th><th>FixedPeriod</th><th>DOB</th><th>Gender</th><th>Contact Number</th><th>Email</th><th>Last Interest Calculation</th><th>Interest Amount</th><th>Initial Deposit</th><th>Balance</th><th>Registration Date</th></tr>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$row['UId']."</td>";
            echo "<td>".$row['AccountNo']."</td>";
            echo "<td>".$row['Username']."</td>";
            echo "<td>".$row['AccountType']."</td>";
			echo "<td>".$row['FixedPeriod']."</td>";
            echo "<td>".$row['DOB']."</td>";
            echo "<td>".$row['Gender']."</td>";
            echo "<td>".$row['ContactNo']."</td>";
            echo "<td>".$row['Email']."</td>";
			echo "<td>".$row['LastInterestCalculation']."</td>";
			echo "<td>".$row['InterestAmount']."</td>";
			echo "<td>".$row['InitialDeposit']."</td>";
            echo "<td>".$row['Balance']."</td>";
            echo "<td>".$row['RegistrationDate']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "No users found.";
    }

    // Close the database connection
    mysqli_close($con);
    ?>
	<div id="back">
				<button onclick="redirectToAd()" type="button">Back To Dashboard</button>
			</div>
			<script>
				function redirectToAd() 
				{
					window.location.href = 'adb.php';
				}
			</script>
</div>
</body>
</html>
