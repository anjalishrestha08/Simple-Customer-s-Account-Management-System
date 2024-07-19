<?php
    // Start session
    session_start();

    // Include database connection file
    include("connection.php");
	
    // Get the logged-in user's email from the session
    $email = $_SESSION['email'];

    // Fetch the account details of the logged-in user from the database
    $get_account_query = "SELECT AccountNo, Username, DOB, Gender, ContactNo FROM user WHERE Email = '$email'";
    $result_account = mysqli_query($con, $get_account_query);
    $row_account = mysqli_fetch_assoc($result_account);

    // Initialize variables with existing user data
    $account = $row_account['AccountNo'];
    $username = $row_account['Username'];
    $dob = $row_account['DOB'];
    $gender = $row_account['Gender'];
    $contact = $row_account['ContactNo'];

    // Check if the form is submitted
    if(isset($_POST['edit'])) {
        // Retrieve form data
        $username = !empty($_POST['username']) ? $_POST['username'] : $username;
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : $dob;
        $gender = !empty($_POST['gender']) ? $_POST['gender'] : $gender;
        $contact = !empty($_POST['contact']) ? $_POST['contact'] : $contact;

        // Validate phone number
        if(!empty($contact) && (strlen($contact) !== 10 || !ctype_digit($contact))) {
            echo "<script>alert('Invalid phone number. Please enter a 10-digit numeric value.');</script>";
            echo "<script>window.location.href = 'editpf.php';</script>";
            exit;
        }

        // Prepare SQL query to update user data
        $update_query = "UPDATE user SET Username='$username', DOB='$dob', Gender='$gender', ContactNo='$contact' WHERE AccountNo='$account'";
        if(mysqli_query($con, $update_query)) {
            echo "<script>alert('Your data is updated successfully.');</script>";
            echo "<script>window.location.href = 'pf.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error updating your data.');</script>";
            echo "<script>window.location.href = 'editpf.php';</script>";
            exit;
        }
        // Close the database connection
        mysqli_close($con);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="aupd.css"/>
</head>
<body>
    <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <div id="text">
            Edit Your Account:
        </div>
        <div id="aupd">
            <form action="#" method="POST">
                <br/>
                <label>Account No:</label>
                <input type="number" id="uaccount" name="uaccount" value="<?php echo $account; ?>" readonly></br>
                <label>Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>"><br/>
                <label>DOB:</label>
                <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>"><br/>
                <label>Gender:</label>
                <input type="text" id="gender" name="gender" value="<?php echo $gender; ?>"><br/>
                <label>Contact No:</label>
                <input type="number" id="contact" name="contact" placeholder="Enter your 10-digit phone number." value="<?php echo $contact; ?>"><br/>
                <input type="submit" name="edit" value="Save Changes" id="update"/>
                <input type="reset" name="reset" value="Reset" id="reset"/>
            </form>
            <div>
                <button id="goback" onclick="redirectToP()" type="button">Cancel</button>
            </div>
        </div>
        <script>
            function redirectToP() {
                window.location.href = 'pf.php';
            }
        </script>
    </div>
</body>
</html>
