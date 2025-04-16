<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$supid = 2424;

$from = 'April 1, 2025';
$to = 'April 30, 2025';


$return = $theme->batch_process_supplies_recon();

echo '<pre>';
print_r($return);
echo '</pre>';
?>