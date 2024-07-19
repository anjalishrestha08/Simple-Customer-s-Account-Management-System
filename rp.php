<?php
session_start();
include("connection.php");

// Check if the user is logged in
if(isset($_SESSION['email'])){
	
}else{
    // Redirect to login page if user is not logged in
     echo "<script>window.location.href = 'bms.html';</script>";
    exit;
}


if(isset($_POST['report'])) {
    $email = $_SESSION['email'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // Retrieve the UId of the logged-in user
    $query = "SELECT UId FROM user WHERE Email = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $userId = $row['UId'];

    // Insert the problem report with the retrieved UId
    $insert_query = "INSERT INTO reportproblem (UId, Subject, Description) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iss", $userId, $subject, $description);
    mysqli_stmt_execute($stmt);

    // Close the statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    // Redirect back to the user portal
     echo "<script>window.location.href = 'udb.php';</script>";
    exit;
} else {

}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReportProblem</title>
    <link rel="stylesheet" type="text/css" href="rp.css">
</head>
<body>
   <div class="container">
        <h1><b style="color: darkgreen;">BMS</b></h1>
        <br/><h2>Customer's Account Management System</h2>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <h3>Report a Problem?</h3>
		<div id="rp">
        <form action="#" method="POST">
            <label for="subject">Subject:</label><br>
            <input type="text" id="subject" name="subject" required><br>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea><br>
            <input type="submit" value="Report" name="report">
            <button id="resetButton" type="reset">Reset</button>
			<button id="cancelButton" onclick="cancel()" type="button">Cancel</button>

        </form>
		</div>
        <script>
            function cancel() {
                window.location.href = 'udb.php';
            }
        </script>
    </div>
</body>
</html>
