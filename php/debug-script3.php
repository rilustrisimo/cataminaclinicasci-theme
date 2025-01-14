<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$json = json_encode('{"2240":"MEFENAMIC ACID 500MG TABLET (RITEMED)"}');

var_dump($json);

$res = $theme->getExpAmountAndStatus($json, 'December 4, 2024', 'January 4, 2025');

var_dump($res);
?>