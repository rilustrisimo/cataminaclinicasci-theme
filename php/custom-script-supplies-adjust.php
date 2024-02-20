<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$meta_query = array();

$args = array(
    'orderby'			=> 'date',
    'order'				=> 'DESC',
    'numberposts'	=> -1,
    'post_type'		=> 'supplies',
    'meta_query'    => array($meta_query),
    'posts_per_page' => -1
);

$deptuser = array(
    'NURSING' => 7,
    'LABORATORY' => 6,
    'PHARMACY' => 4,
    'HOUSEKEEPING' => 8,
    'MAINTENANCE' => 8,
    'RADIOLOGY' => 5,
    'BUSINESS OFFICE' => 9,
    'INFORMATION / TRIAGE' => 10,
    'PHYSICAL THERAPY' => 14,
    'KONSULTA PROGRAM' => 11,
    'CLINIC B' => 12,
    'CLINIC C' => 12,
    'CLINIC D' => 12
);

foreach($deptuser as $d => $i):
    $args['author'] = $i;

    $the_query = new WP_Query( $args );
    $posts = $the_query->posts;

    echo "------".$d."--------START--------------<br>";
    foreach($posts as $s):
        $dept = get_field('department', $s->ID);
        
        if($dept != $d):
            echo get_the_title($s->ID).' --> '.$dept.'<br>';
        endif;
    endforeach;
    echo "------".$d."--------END--------------<br>";
endforeach;

?>