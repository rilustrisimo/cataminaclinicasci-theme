<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$meta_query = array(
    'key'     => 'department',
    'value'   =>  'PHARMACY' 
);

$addquery = $theme->createQuery('supplies', $meta_query, -1, 'date', 'DESC');

var_dump($addquery->posts);

?>