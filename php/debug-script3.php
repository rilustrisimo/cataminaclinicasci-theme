<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$json = json_decode('{"2240":"MEFENAMIC ACID 500MG TABLET (RITEMED)"}');

$res = $theme->getQtyOfSupplyAfterDate(2240, 'January 14, 2025', true);

var_dump($res);
?>