<?php
function calculateInterest($initialDeposit, $registrationDate, $fixedPeriod) {
    $currentDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime("+$fixedPeriod years", strtotime($registrationDate)));

    if ($currentDate > $endDate) {
        $currentDate = $endDate;
    }

    $start = new DateTime($registrationDate);
    $end = new DateTime($currentDate);
    $interval = $start->diff($end);
    $months = ($interval->y * 12) + $interval->m;

    $monthlyInterest = ($initialDeposit * 0.10) / 12;
    $totalInterest = $monthlyInterest * $months;

    return $totalInterest;
}
?>
