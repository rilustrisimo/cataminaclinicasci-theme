<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$meta_query = array(
    'key'     => 'department',
    'value'   =>  'PHARMACY' 
);

$addquery = $theme->createQuery('supplies', $meta_query, -1, 'date', 'DESC');
$allsup = $addquery->posts;

foreach($allsup as $p):
    $supid = $p->ID;

    echo $supid.': '.get_the_title($supid).'<br>';

    $meta_query2 = array(
        'key'     => 'supply_name',
        'value'   =>  $supid 
    );
    
    $addquery2 = $theme->createQuery('actualsupplies', $meta_query2, -1, 'date', 'DESC');

    foreach($addquery2->posts as $p2):
        echo '-->'.get_the_title($p2->ID).'('.get_the_date('Y-m-d', $p2->ID).')<br>';
    endforeach;
endforeach;




?>