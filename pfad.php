<?php
session_start();
// Include database connection file
include("connection.php");

// Check if form is submitted
if (isset($_POST['delete'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $pin = $_POST['pin'];
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($pin)) {
        echo "<script>alert('Please input valid credentials.');</script>";
        echo "<script>window.location.href = 'pfad.php';</script>";
        exit;
    }

    // Check if the user exists
    $check_user_query = "SELECT * FROM user WHERE Username='$username' AND Email='$email' AND PinNo='$pin' AND Password='$password'";
    $check_user_result = mysqli_query($con, $check_user_query);

    if (mysqli_num_rows($check_user_result) > 0) {
        // Fetch user data
        $user_data = mysqli_fetch_assoc($check_user_result);
        $user_balance = $user_data['Balance'];
        $account_type = $user_data['AccountType'];
        $fixed_period = $user_data['FixedPeriod'];
        $registration_date = $user_data['RegistrationDate'];

        // Check if the account type is 'Fixed' and if the fixed period has ended
        if ($account_type == 'Fixed') {
            $fixed_period_end = date('Y-m-d', strtotime($registration_date . ' + ' . $fixed_period . ' days'));
            $current_date = date('Y-m-d');
            if ($current_date < $fixed_period_end) {
                echo "<script>alert('Account cannot be deleted until the fixed period has ended.');</script>";
                echo "<script>window.location.href = 'pfad.php';</script>";
                exit;
            }
        }
		// Check if balance is less than Rs.100
        if ($user_balance >= 100) {
            echo "<script>alert('Please withdraw your money first.');</script>";
            echo "<script>window.location.href = 'pfad.php';</script>";
            exit;
        }

        // Archive user data
        $archive_query = "INSERT INTO archive (UId, AccountNo, Username, Email, Balance, Date_deleted) VALUES ('{$user_data['UId']}', '{$user_data['AccountNo']}', '$username', '$email', '{$user_data['Balance']}', NOW())";
        mysqli_query($con, $archive_query);

        // Delete related records from the withdrawal table
        $delete_withdrawals_query = "DELETE FROM withdrawal WHERE UId='{$user_data['UId']}'";
        mysqli_query($con, $delete_withdrawals_query);

        // Delete user record from main user table
        $delete_query = "DELETE FROM user WHERE Username='$username' AND Email='$email'";
        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('Account Deleted Successfully.');</script>";
            echo "<script>window.location.href = 'bms.html';</script>";
            exit;
        } else {
            echo "<script>alert('Error deleting account.');</script>";
            echo "<script>window.location.href = 'pfad.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('User not found.');</script>";
        echo "<script>window.location.href = 'pfad.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link rel="stylesheet" type="text/css" href="pfad.css"/>
</head>
<body>
    <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <br/><h2>Customer's Account Management System</h2></b>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <h3>Make sure that you have withdrawn your money and your balance is less than Rs.100 before you delete your account!!</h3>
        <div id="pfad">
            <form action="#" method="POST">
                <br/>
                <label>Username:</label>
                <input type="text" id="username" name="username" required><br/>
                <label>Email:</label>
                <input type="email" id="email" name="email" required><br/>
                <label>Password:</label>
                <input type="password" id="password" name="password" required><br/>
                <label>PinNo:</label>
                <input type="password" id="pin" name="pin" required><br/>
                <input type="submit" name="delete" value="Delete Account" id="request"/>
                <input type="reset" name="reset" value="Reset" id="reset"/>
            </form>
            <div>
                <button id="goback" onclick="redirectToP()" type="button">Cancel</button>
            </div>
            <script>
                function redirectToP() {
                    window.location.href = 'udb.php';
                }
            </script>
        </div>
    </div>
</body>
</html>
