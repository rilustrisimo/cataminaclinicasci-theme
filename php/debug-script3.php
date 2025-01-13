<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$res = $theme->getQtyOfSupplyAfterDate(2306, 'January 4, 2025', true);

var_dump($res);
?>