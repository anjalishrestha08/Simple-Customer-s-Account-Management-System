<?php
// Start session
session_start();

// Include database connection file
include("connection.php");


// Initialize variables
$start_date = "";
$end_date = "";
$show_interest = false;

if (isset($_SESSION['email'])) {
    $current_user_email = $_SESSION['email'];

    // Get current user's information from session
    $current_user_query = "SELECT UId, AccountType, InitialDeposit, LastInterestCalculation FROM user WHERE Email='$current_user_email'";
    $current_user_result = mysqli_query($con, $current_user_query);
    $current_user_row = mysqli_fetch_assoc($current_user_result);
    $current_user_id = $current_user_row['UId'];
    $account_type = $current_user_row['AccountType'];
    $initial_balance = $current_user_row['InitialDeposit'];
    $last_interest_calculation = $current_user_row['LastInterestCalculation'];

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

        // Fetch user deposit transactions within the date range
        $deposit_query = "SELECT 
                            Username,
                            Deposit_Amount,
                            Deposit_Date,
                            Remarks AS Deposit_Remarks,
                            NULL AS Withdrawal_Amount,
                            NULL AS Withdrawal_Date,
                            NULL AS Withdrawal_Remarks
                        FROM 
                            deposit 
                        WHERE 
                            UId = '$current_user_id'
                            AND Deposit_Date >= '$start_date' AND Deposit_Date <= '$end_date'
                        ORDER BY 
                            Deposit_Date ASC";  // Sort by date in ascending order

        // Fetch user withdrawal transactions within the date range
        $withdrawal_query = "SELECT 
                                Username,
                                NULL AS Deposit_Amount,
                                NULL AS Deposit_Date,
                                NULL AS Deposit_Remarks,
                                Withdrawal_Amount,
                                Withdrawal_Date,
                                Remarks AS Withdrawal_Remarks
                            FROM 
                                withdrawal 
                            WHERE 
                                UId = '$current_user_id'
                                AND Withdrawal_Date >= '$start_date' AND Withdrawal_Date <= '$end_date'
                            ORDER BY 
                                Withdrawal_Date ASC";  // Sort by date in ascending order

        // Execute the deposit query
        $deposit_result = mysqli_query($con, $deposit_query);

        // Execute the withdrawal query
        $withdrawal_result = mysqli_query($con, $withdrawal_query);

        // Fetch user interest transactions within the date range if applicable
        if ($show_interest) {
            // Include the interest calculation script
            include("calculate_interest.php");
            // Fetch interest transactions from the database
            $interest_query = "SELECT 
                                    Username,
                                    NULL AS Deposit_Amount,
                                    NULL AS Deposit_Date,
                                    NULL AS Deposit_Remarks,
                                    NULL AS Withdrawal_Amount,
                                    NULL AS Withdrawal_Date,
                                    NULL AS Withdrawal_Remarks,
                                    InterestAmount AS Interest_Amount,
                                    LastInterestCalculation AS Interest_Date
                                FROM 
                                    user
                                WHERE 
                                    UId = '$current_user_id'
                                ORDER BY 
                                    LastInterestCalculation ASC";  // Sort by date in ascending order

            // Execute the interest query
            $interest_result = mysqli_query($con, $interest_query);
        }
        }
    }
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statements</title>
    <link rel="stylesheet" type="text/css" href="us.css">
</head>
<body>
    <div class="container">
        <h1><b style="color: darkgreen;">BMS</h1>
        <br/><h2>Customer's Account Management System</h2></b>
        <div id="image">
            <center><img src="..\SummerProject\BMSlogosm.png" alt="Logo"/></center>
        </div>
        <div id="us">
            <form method="POST">
                <h4>Logged In as: <?php echo " ".$username.". ";?> Registration Date:<?php echo " ".$rgd.". ";?></h4>
                <label>Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                <label>End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                <input type="submit" name="submit" value="Submit">
                <button id="cancelButton" onclick="cancel()" type="button">Cancel</button>
            </form>
        </div>
        <br>

        <!-- Display Deposit Transactions -->
        <?php if (isset($deposit_result) && $deposit_result) : ?>
            <div id="depotable">
                <h3>Deposit Transactions</h3>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Deposit Amount</th>
                        <th>Deposit Date</th>
                        <th>Deposit Remarks</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($deposit_result)) : ?>
                        <tr>
                            <td><?php echo $row['Username']; ?></td>
                            <td><?php echo $row['Deposit_Amount']; ?></td>
                            <td><?php echo $row['Deposit_Date']; ?></td>
                            <td><?php echo $row['Deposit_Remarks']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>

        <!-- Display Withdrawal Transactions -->
        <?php if (isset($withdrawal_result) && $withdrawal_result) : ?>
            <div id="withdtable">
                <h3>Withdrawal Transactions</h3>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Withdrawal Amount</th>
                        <th>Withdrawal Date</th>
                        <th>Withdrawal Remarks</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($withdrawal_result)) : ?>
                        <tr>
                            <td><?php echo $row['Username']; ?></td>
                            <td><?php echo $row['Withdrawal_Amount']; ?></td>
                            <td><?php echo $row['Withdrawal_Date']; ?></td>
                            <td><?php echo $row['Withdrawal_Remarks']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>

        <!-- Display Interest Transactions -->
        <?php if ($show_interest && isset($interest_result) && $interest_result) : ?>
            <div id="interesttable">
                <h3>Interest Transactions</h3>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Interest Amount</th>
                        <th>Interest Date</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($interest_result)) : ?>
                        <tr>
                            <td><?php echo $row['Username']; ?></td>
                            <td><?php echo $row['Interest_Amount']; ?></td>
                            <td><?php echo $row['Interest_Date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>
        
        <br>
        <script>
            function cancel() {
                window.location.href = 'udb.php';
            }
        </script>
    </div>
</body>
</html>
