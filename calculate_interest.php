<?php
// Include database connection file
include("connection.php");

// Get the current date
$current_date = date('Y-m-d');
$current_day = date('d');

// Fetch all users
$user_query = "SELECT UId, AccountType, Balance, FixedPeriod, LastInterestCalculation, InterestAmount, RegistrationDate FROM user";
$user_result = mysqli_query($con, $user_query);

if ($user_result) {
    while ($user = mysqli_fetch_assoc($user_result)) {
        $uid = $user['UId'];
        $account_type = $user['AccountType'];
        $balance = $user['Balance'];
        $fixed_period = $user['FixedPeriod'];
        $last_interest_calculation = $user['LastInterestCalculation'];
        $interest_amount = $user['InterestAmount'];
        $registration_date = $user['RegistrationDate'];

        // Calculate months elapsed since last interest calculation
        $months_elapsed = calculateMonthsDifference($last_interest_calculation, $current_date);

        // Ensure interest calculation starts after one month of account creation
        if ($months_elapsed >= 0) {
            // If last interest calculation is null, set it to registration date
            if (is_null($last_interest_calculation)) {
                $last_interest_calculation = $registration_date;
            }

            // Extract day of month for comparison
            $registration_day = date('d', strtotime($registration_date));

            // Ensure the calculation happens on the next day of registration date's day each month
            if ($registration_day == $current_day+1) {
                if ($account_type == 'Fixed') {
                    // Calculate yearly interest for fixed account
                    $fixed_period_end_date = date('Y-m-d', strtotime($registration_date . ' + ' . $fixed_period . ' years'));
                    if ($current_date < $fixed_period_end_date) {
                        $interest = $balance * 0.10 / 12 * floor($months_elapsed); // 10% yearly interest converted to monthly
                        $interest_amount += $interest;
                        // Update the last interest calculation date to the current date
                        $last_interest_calculation = $current_date;
                    }
                } elseif ($account_type == 'Savings') {
                    // Calculate yearly interest for savings account
                    $interest = $balance * 0.04 / 12 * floor($months_elapsed); // 4% yearly interest converted to monthly
                    $interest_amount += $interest;
                    // Update the last interest calculation date to the current date
                    $last_interest_calculation = $current_date;
                }

                // Update the user account with the new interest
                $update_user_query = "UPDATE user SET InterestAmount = '$interest_amount', LastInterestCalculation = '$last_interest_calculation' WHERE UId = '$uid'";
                mysqli_query($con, $update_user_query);
            }
        }
    }
} else {
    echo "Error fetching user data: " . mysqli_error($con);
}

// Function to calculate months difference
function calculateMonthsDifference($start_date, $end_date) {
    $start_timestamp = strtotime($start_date);
    $end_timestamp = strtotime($end_date);

    $start_year = date('Y', $start_timestamp);
    $end_year = date('Y', $end_timestamp);
    $start_month = date('m', $start_timestamp);
    $end_month = date('m', $end_timestamp);

    $months_difference = (($end_year - $start_year) * 12) + ($end_month - $start_month);
    return $months_difference;
}

// Close the database connection
//mysqli_close($con);
?>
