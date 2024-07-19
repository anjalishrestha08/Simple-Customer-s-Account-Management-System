<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProblemReports</title>
    <link rel="stylesheet" type="text/css" href="arp.css"/>
</head>
<body>
<div class="container">
    <h1><b style="color: darkgreen;">BMS</b></h1>
    <br/><h2>Reported Problems</h2>
    <div id="image">
        <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
    </div>
    <?php
    session_start();
    include("connection.php");
    // Check if the admin is logged in
    if(!isset($_SESSION['adminId'])) {
        // If not logged in, redirect to login page
        echo "<script>window.location.href = 'adlf.html';</script>";
        exit;
    }

    // Query to select all reports
    $sql = "SELECT * FROM reportproblem";
    $result = mysqli_query($con, $sql);

    // Check if there are any reports
    if (mysqli_num_rows($result) > 0) {
        // Display reports in a table
        echo "<div class='user-table'>";
        echo "<table>";
        echo "<tr><th>RId</th><th>UId</th><th>Subject</th><th>Description</th><th>Reported_at</th></tr>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$row['RId']."</td>";
            echo "<td>".$row['UId']."</td>";
            echo "<td>".$row['Subject']."</td>";
			echo "<td>".$row['Description']."</td>";
            echo "<td>".$row['Reported_at']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<h3>No reports found.</h3>";
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
