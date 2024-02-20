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
            $tit = get_the_title($s->ID);
            echo $tit.' --> '.$dept.' ('.$s->ID.')<br>';

            $meta_query = array();

            $args = array(
                'orderby'			=> 'date',
                'order'				=> 'DESC',
                'numberposts'	=> -1,
                'post_type'		=> 'supplies',
                'meta_query'    => array($meta_query),
                'posts_per_page' => -1
            );

            $the_query2 = new WP_Query( $args );
            $posts2 = $the_query2->posts;

            foreach($posts2 as $s2):
                $tit2 =  get_the_title($s2->ID);

                if($tit == $tit2 && $s->ID != $s2->ID):
                    

                    $meta_query3 = array(
                        'key'     => 'supply_name',
                        'value'   =>  $s->ID 
                    );
                    
                    $addquery3 = $theme->createQuery('releasesupplies', $meta_query3, -1, 'date', 'DESC');
                    $addquery4 = $theme->createQuery('actualsupplies', $meta_query3, -1, 'date', 'DESC');

                    echo '[actual: '.count($addquery4->posts).'] ';
                    echo '[release: '.count($addquery3->posts).']<br>';

                    echo '---> '.$tit2.' ('.$s2->ID.') <br>'; 
                endif;
            endforeach;

            /*

            $post_data = array(
                'ID' => $s->ID,
                'post_author' => $deptuser[$dept],
            );
            
            // Update the post with the new author
            $post_updated = wp_update_post( $post_data );
            
            if ( $post_updated !== 0 ) {
                // Post updated successfully
                echo 'Post author updated successfully.';
            } else {
                // Failed to update post
                echo 'Failed to update post author.';
            }
            */
        endif;
    endforeach;
    echo "------".$d."--------END--------------<br>";
endforeach;

?>