<?php
session_start();
include("connection.php");

// Initialize $username
$username = "";

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
        
        if (isset($_POST['raccount']) && isset($_POST['amount']) && isset($_POST['pin']) && isset($_POST['remarks'])) {
            $raccount = $_POST['raccount'];    
            $amount = $_POST['amount'];
            $pin = $_POST['pin'];
            $remarks = $_POST['remarks'];

            // Validate amount
            if ($amount < 1000 || $amount > 100000) {
                echo "<script>alert('Amount for withdrawal must be between 1000 and 100,000.');</script>";
                echo "<script>window.location.href = 'tm.php';</script>";
                exit;
            }

            // Check if user has sufficient balance
            if ($balance < $amount) {
                echo "<script>alert('Insufficient balance.');</script>";
                echo "<script>window.location.href = 'tm.php';</script>";
                exit;
            }
            
            // Validate Receiver
            $check_receiver = "SELECT AccountNo, AccountType, FixedPeriod, RegistrationDate, Username FROM user WHERE AccountNo = '$raccount'";
            $result_receiver = mysqli_query($con, $check_receiver);
            $row_receiver = mysqli_fetch_assoc($result_receiver);

            if (!$row_receiver) {
                echo "<script>alert('Receiver\'s account does not exist.');</script>";
                echo "<script>window.location.href = 'tm.php';</script>";
                exit;
            }

            $received_by = $row_receiver['Username'];
            $receiverAccountType = $row_receiver['AccountType'];
            $receiverFixedPeriod = $row_receiver['FixedPeriod'];
            $receiverRegistrationDate = new DateTime($row_receiver['RegistrationDate']);

            // Validate Pin
            if ($pin !== $storedPin) {
                echo "<script>alert('Incorrect PIN.');</script>";
                echo "<script>window.location.href = 'tm.php';</script>";
                exit;
            }
            
            // Check for fixed account type and fixed period for sender
            if ($accountType == 'Fixed') {
                $currentDate = new DateTime();
                $endFixedPeriod = clone $registrationDate;
                $endFixedPeriod->modify("+$fixedPeriod years");

                if ($currentDate < $endFixedPeriod) {
                    echo "<script>alert('Transfer is disabled during the fixed period for your account.');</script>";
                    echo "<script>window.location.href = 'tm.php';</script>";
                    exit;
                }
            }

            // Check for fixed account type and fixed period for receiver
            if ($receiverAccountType == 'Fixed') {
                $currentDate = new DateTime();
                $endFixedPeriod = clone $receiverRegistrationDate;
                $endFixedPeriod->modify("+$receiverFixedPeriod years");

                if ($currentDate < $endFixedPeriod) {
                    echo "<script>alert('Transfer is disabled during the fixed period for the receiver\'s account.');</script>";
                    echo "<script>window.location.href = 'tm.php';</script>";
                    exit;
                }
            }

            // Deduct money from sender's account and insert into withdrawal table
            $withdraw_query = "INSERT INTO withdrawal (UId, AccountNo, Username, Withdrawal_Amount, Received_By, Remarks) 
                               VALUES ((SELECT UId FROM user WHERE AccountNo = '$account'), '$account', '$username',
                               '$amount', '$received_by', '$remarks')";
            mysqli_query($con, $withdraw_query);

            // Add money to receiver's account and insert into deposit table
            $deposit_query = "INSERT INTO deposit (UId, AccountNo, Username, Deposit_Amount, Deposited_By, Remarks) 
                              VALUES ((SELECT UId FROM user WHERE AccountNo = '$raccount'), '$raccount', '$received_by',
                              '$amount', '$username', '$remarks')";
            mysqli_query($con, $deposit_query);

            // Deduct money from sender's account
            $deduct_query = "UPDATE user SET Balance = Balance - $amount WHERE AccountNo = '$account'";
            mysqli_query($con, $deduct_query);

            // Add money to receiver's account
            $add_query = "UPDATE user SET Balance = Balance + $amount WHERE AccountNo = '$raccount'";
            mysqli_query($con, $add_query);

            // Close the database connection
            mysqli_close($con);

            echo "<script>alert('Money transferred successfully.');</script>";
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
    echo "<script>alert('Please Log In');</script>";
    echo "<script>window.location.href = 'bms.html';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TransferMoney</title>
    <link rel="stylesheet" type="text/css" href="tm.css">
</head>
<body>
   <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <br/><h2>Customer's Account Management System</h2></b>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <div id="tm">
            <form action="#" method="POST">
            <br/>
            <h4>Logged In as: <?php echo " ".$username.". ";?></h4>
                <label>Account No:</label>
                <input type="number" id="saccount" name="saccount" value="<?php echo ($account); ?>" readonly><br>
                <label>Receiver's Account No:</label>
                <input type="text" id="raccount" name="raccount" placeholder="Enter Receiver's account number." required><br>
                <label>Amount:</label>
                <input type="text" id="amount" name="amount"  placeholder="In limit of 1000-100000" required><br>
                <label>Pin No:</label>
                <input type="password" id="pin" name="pin" maxlength="4" pattern="\d{4}" placeholder="Enter your transaction PIN." required><br>
                <label>Remarks:</label>
                <input type="text" id="remarks" name="remarks" required><br><br>
                <input type="submit" name="transfer" value="Transfer Money"><br/><br/>
            </form>
        </div>  
        <div id="cancel">
            <button id="cancelButton" onclick="cancel()" type="button">Cancel</button>
        </div>
        <script>
        function cancel() 
        {
            window.location.href = 'udb.php'; // Change this to the appropriate page
        }
        </script>
    </div>
</body>
</html>
