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

// Convert expiry date to DateTime object
$expiryDate = DateTime::createFromFormat('d/m/Y', $expiry);

// Get today's date
$today = new DateTime();

// Add 6 months to today's date to calculate the threshold
$sixMonthsFromNow = (clone $today)->add(new DateInterval('P6M'));

// Initialize the variable to store the warning class
$expirySixMonths = '';

// Check if the expiry date exists and is within the next 6 months (excluding past dates)
if (!empty($expiry) && $expiry != "" && $expiryDate >= $today && $expiryDate <= $sixMonthsFromNow) {
    // Perform your action here (e.g., add a warning class)
    $expirySixMonths = 'bold-warning';
}


var_dump($expirySixMonths);
?>