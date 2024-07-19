<?php
session_start();
include("connection.php");

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Fetch the account details of the logged-in user from the database
    $get_account_query = "SELECT AccountNo, AccountType, FixedPeriod, RegistrationDate, Username, Balance, PinNo FROM user WHERE Email = '$email'";
    $result_account = mysqli_query($con, $get_account_query);
    $row_account = mysqli_fetch_assoc($result_account);

    if ($row_account) {
        $account = $row_account['AccountNo'];
        $accountType = $row_account['AccountType'];
        $fixedPeriod = $row_account['FixedPeriod'];
        $registrationDate = new DateTime($row_account['RegistrationDate']);
        $username = $row_account['Username'];
        $balance = $row_account['Balance'];
        $storedPin = $row_account['PinNo'];

        if (isset($_POST['amount']) && isset($_POST['pin']) && isset($_POST['remarks'])) {
            $amount = $_POST['amount'];
            $pin = $_POST['pin'];
            $remarks = $_POST['remarks'];

            // Verify PIN
            if ($pin !== $storedPin) {
                echo "<script>alert('Incorrect PIN.');</script>";
                echo "<script>window.location.href = 'wd.php';</script>";
                exit;
            }

            // Validate amount
            if ($amount < 1000 || $amount > 100000) {
                echo "<script>alert('Withdraw amount must be in the limit of 1000 to 100000.');</script>";
                echo "<script>window.location.href = 'wd.php';</script>";
                exit;
            }

            // Check for fixed account type and fixed period
            if ($accountType == 'Fixed') {
                $currentDate = new DateTime();
                $endFixedPeriod = clone $registrationDate;
                $endFixedPeriod->modify("+$fixedPeriod years");

                if ($currentDate < $endFixedPeriod) {
                    echo "<script>alert('Withdrawals are disabled during the fixed period.');</script>";
                    echo "<script>window.location.href = 'wd.php';</script>";
                    exit;
                }
            }

            // Check if user has sufficient balance
            if ($balance < $amount) {
                echo "<script>alert('Insufficient balance.');</script>";
                echo "<script>window.location.href = 'wd.php';</script>";
                exit;
            }

            // Insert withdrawal transaction
            $insert_query = "INSERT INTO withdrawal (UId, AccountNo, Username, Withdrawal_Amount, Remarks) VALUES ((SELECT UId FROM user WHERE AccountNo = '$account'), '$account', '$username', '$amount', '$remarks')";
            mysqli_query($con, $insert_query);

            // Update balance
            $update_balance = "UPDATE user SET Balance = Balance - $amount WHERE AccountNo = '$account'";
            mysqli_query($con, $update_balance);

            // Close the database connection
            mysqli_close($con);

            echo "<script>alert('Withdrawal Successful.');</script>";
            echo "<script>window.location.href = 'udb.php';</script>";
        }
    } else {
        // Handle case where no account is found for the email
        echo "<script>alert('Account not found.');</script>";
        echo "<script>window.location.href = 'bms.html';</script>";
        exit;
    }
} else {
    // If not logged in, redirect to login page
    echo "<script>alert('Please login.');</script>";
    echo "<script>window.location.href = 'bms.html';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>
    <link rel="stylesheet" type="text/css" href="wd.css">
</head>
<body>
    <div class="container">
        <h1><b style="color: darkgreen;">BMS</b></h1>
        <br/><h2>Customer's Account Management System</h2>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <div id="wd">
            <form action="#" method="POST">
                <br/>
                <h4>Logged In as: <?php echo " ".$username.". ";?></h4>
                <label>Account No:</label>
                <input type="number" id="uaccount" name="uaccount" value="<?php echo $account; ?>" readonly></br>
                <label>Amount:</label>
                <input type="text" id="amount" name="amount" placeholder="In limit of 1000-100000" required><br>
                <label>PinNo:</label>
                <input type="password" id="pin" name="pin" required><br>
                <label>Remarks:</label>
                <input type="text" id="remarks" name="remarks" required><br><br>
                <input type="submit" value="Withdraw Money"><br/><br/>
            </form>
        </div>
        <div id="cancel">
            <button id="cancelButton" onclick="cancel()" type="button">Cancel</button>
        </div>
        <script>
        function cancel() {
            window.location.href = 'udb.php'; // Change this to the appropriate page
        }
        </script>
    </div>
</body>
</html>
