<?php
// Start session
session_start();

// Include database connection file
include("connection.php");

// Initialize variables
$balance_history = array();
$start_date = "";
$end_date = "";
$show_interest = false;

// Check if user is logged in
if (isset($_SESSION['email'])) {
    $current_user_email = $_SESSION['email'];

    // Get current user's information from session
    $current_user_query = "SELECT UId, AccountType, InitialDeposit, Balance, InterestAmount, RegistrationDate FROM user WHERE Email='$current_user_email'";
    $current_user_result = mysqli_query($con, $current_user_query);
    $current_user_row = mysqli_fetch_assoc($current_user_result);
    $current_user_id = $current_user_row['UId'];
    $account_type = $current_user_row['AccountType'];
    $initial_balance = $current_user_row['InitialDeposit'];
    $current_balance = $current_user_row['Balance'];
    $current_interest = $current_user_row['InterestAmount'];
    $registration_date = $current_user_row['RegistrationDate'];

    // Determine if interest should be shown based on account type
    if ($account_type == 'Fixed' || $account_type == 'Savings') {
        $show_interest = true;
    }

    // Get username of the account holder
    $get_username = "SELECT Username FROM user WHERE Email = '$current_user_email'";
    $result_username = mysqli_query($con, $get_username);
    $row_username = mysqli_fetch_assoc($result_username);
    $username = $row_username['Username'];

    // Get registration date of the account holder
    $get_rgd = "SELECT RegistrationDate FROM user WHERE Email = '$current_user_email'";
    $result_rgd = mysqli_query($con, $get_rgd);
    $row_rgd = mysqli_fetch_assoc($result_rgd);
    $rgd = $row_rgd['RegistrationDate'];

    // Check if form is submitted
    if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
        // Retrieve form data
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Check if the end date is the current date
        if ($end_date == date("Y-m-d")) {
            // Increment end date by 1 day
            $end_date = date("Y-m-d", strtotime($end_date . " +1 day"));
        }

        // Add initial balance to history
        $balance_history[] = array(
            'date' => 'Initial Balance',
            'deposit' => '',
            'withdrawal' => '',
            'interest' => '',
            'balance' => $initial_balance
        );

       // Fetch all transactions (deposits and withdrawals) within date range
        $transactions_query = "
            SELECT 'deposit' AS type, Deposit_Amount AS amount, Deposit_Date AS date
            FROM deposit
            WHERE UId = $current_user_id AND Deposit_Date BETWEEN '$start_date' AND '$end_date'
            UNION ALL
            SELECT 'withdrawal' AS type, -Withdrawal_Amount AS amount, Withdrawal_Date AS date
            FROM withdrawal
            WHERE UId = $current_user_id AND Withdrawal_Date BETWEEN '$start_date' AND '$end_date'
            ORDER BY date
        ";

        $transactions_result = mysqli_query($con, $transactions_query);

        // Calculate balance at each transaction date
        $balance = $initial_balance;
        while ($row = mysqli_fetch_assoc($transactions_result)) {
            if ($row['type'] == 'deposit') {
                $balance += $row['amount'];
                $balance_history[] = array(
                    'date' => $row['date'],
                    'deposit' => $row['amount'],
                    'withdrawal' => '',
                    'interest' => '',
                    'balance' => $balance
                );
            } elseif ($row['type'] == 'withdrawal') {
                $balance += $row['amount']; // Since amount is negative, it decreases the balance
                $balance_history[] = array(
                    'date' => $row['date'],
                    'deposit' => '',
                    'withdrawal' => -$row['amount'], // Display positive withdrawal amount
                    'interest' => '',
                    'balance' => $balance
                );
            }
        }

        // Fetch interest transactions within date range for fixed and savings accounts
        if ($show_interest) {
            $interest_query = "SELECT InterestAmount, LastInterestCalculation FROM user WHERE UId = $current_user_id AND LastInterestCalculation BETWEEN '$start_date' AND '$end_date' ORDER BY LastInterestCalculation";
            $interest_result = mysqli_query($con, $interest_query);
            
            // Fetch and store all interest transactions in chronological order
            while ($row = mysqli_fetch_assoc($interest_result)) {
                $balance_history[] = array(
                    'date' => $row['LastInterestCalculation'],
                    'deposit' => '',
                    'withdrawal' => '',
                    'interest' => $row['InterestAmount'],
                    'balance' => ''
                );
            }
        }

        // Add current balance to history
        $balance_query = "SELECT Balance FROM user WHERE UId = $current_user_id";
        $balance_result = mysqli_query($con, $balance_query);
        while ($row = mysqli_fetch_assoc($balance_result)) {
            $balance_history[] = array(
                'date' => 'Current Balance',
                'deposit' => '',
                'withdrawal' => '',
                'interest' => '',
                'balance' => $row['Balance']
            );
        }
    }

    // Sort balance history by date (excluding 'Initial Balance' and 'Current Balance')
    usort($balance_history, function($a, $b) {
        if ($a['date'] == 'Initial Balance' || $a['date'] == 'Current Balance' || $b['date'] == 'Initial Balance' || $b['date'] == 'Current Balance') {
            return 0; // Keep 'Initial Balance' and 'Current Balance' at the beginning and end
        }
        return strtotime($a['date']) - strtotime($b['date']);
    });
        }
mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance</title>
    <link rel="stylesheet" type="text/css" href="b.css">
</head>
<body>
    <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <br/><h2>Customer's Account Management System</h2></b>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <div id='balance'>
            <form method="POST">
                <h4>Logged In as: <?php echo " " . $username . ". ";?> Registration Date:<?php echo " " . $rgd . ". ";?></h4>
                <label>Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                <label>End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                <input type="submit" name="submit" value="Submit">
                <button id="cancelButton" onclick="cancel()" type="button">Cancel</button>
            </form>
        </div>
        <h3>Balance Journey</h3>
        <div id="history">
            <table>
                <tr>
                    <th>Date</th>
                    <th>Deposit Amount</th>
                    <th>Withdrawal Amount</th>
                    <?php if ($show_interest): ?>
                        <th>Interest Amount</th>
                    <?php endif; ?>
                    <th>Balance</th>
                </tr>
                <?php foreach ($balance_history as $entry): ?>
                    <tr>
                        <td><?php echo $entry['date']; ?></td>
                        <td><?php echo $entry['deposit']; ?></td>
                        <td><?php echo $entry['withdrawal']; ?></td>
                        <?php if ($show_interest): ?>
                            <td><?php echo $entry['interest']; ?></td>
                        <?php endif; ?>
                        <td><?php echo $entry['balance']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <script>
            function cancel() {
                window.location.href = 'udb.php';
            }
        </script>
    </div>
</body>
</html>
