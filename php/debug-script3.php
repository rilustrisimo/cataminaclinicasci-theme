<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$json = json_decode('{
    "22098": "MEBENDAZOLE 100MG/50ML R8",
    "22132": "DISTILLED WATER 10 LITERS",
    "22592": "GLUCOSAMINE PROSEL",
    "22714": "lidocaine single use",
    "22759": "PLAVIHEX 75MG",
    "22791": "ALNIX DROPS"
}');

//$res = $theme->getLastExpDate(2240, 0, 'January 14, 2025');

$expiry = 'January 30, 2025';

// Convert expiry date to a timestamp
$expiryTimestamp = strtotime($expiry);

// Get today's timestamp (start of today)
$today = new DateTime();
$todayTimestamp = $today->setTime(0, 0)->getTimestamp();

// Calculate the timestamp for six months from now (start of that day)
$sixMonthsFromNow = (clone $today)->add(new DateInterval('P6M'))->setTime(0, 0)->getTimestamp();

// Initialize the variable to store the warning class
$expirySixMonths = '';

// Check if the expiry date exists and is within the next 6 months (excluding past dates)
if ($expiryTimestamp && $expiryTimestamp >= $todayTimestamp && $expiryTimestamp <= $sixMonthsFromNow) {
    // Perform your action here (e.g., add a warning class)
    $expirySixMonths = 'bold-warning';
}

var_dump($expirySixMonths);

?>