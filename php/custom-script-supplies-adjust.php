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


$args['author'] = 5;


$the_query = new WP_Query( $args );
$posts = $the_query->posts;


foreach($posts as $s):
    $dept = get_field('department', $s->ID);
    
    if($dept != "RADIOLOGY"):
        echo get_the_title($s->ID).' --> '.$dept.'<br>';
    endif;
endforeach;


?>