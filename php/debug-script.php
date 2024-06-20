<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$batchData = array(
    1188 => 'Cell Pack'
);
$to = 'March 4, 2024';
$from = 'April 4, 2024';
$reconarray = array();
$relsupplies = array();
$datesupplies = array();

foreach($batchData as $suppid => $supp):
    /** first part */
    $name[$suppid] = get_field('supply_name', $suppid);
    $supplyid = $suppid;
    $dept = get_field('department', $suppid);
    $deptslug = str_replace(" ", "_", strtolower($dept));
    $type = get_field('type', $suppid);
    $typeslug = strtolower($type);

    if($type == "Adjustment"):
        continue;
    endif;

    $overallupplies[$deptslug][$typeslug][$supplyid] = array(
        'supply_name' => get_field('supply_name', $supplyid),
        'department' => $dept,
        'type' => $type,
        'quantity' => $theme->getQtyOfSupplyAfterDate($supplyid, $from)
    );
    /** end first part */

    /** get all actual purchased supplies within the month */

    $meta_query = array(
        'relation' => 'AND',
        array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        ),
        array(
            'key'     => 'supply_name',
            'value'   =>  $suppid
        )
    );

    $addquery = $theme->createQuery('actualsupplies', $meta_query, -1, 'date', 'ASC');
    $qty = array();
    $lotn = array();
    $expd = array();

    foreach($addquery->posts as $p):
        $name[$p->ID] = get_field('supply_name', $p->ID);
        $supplyid = $name[$p->ID]->ID;
        $qty[$supplyid] = (isset($qty[$supplyid]))?(float)$qty[$supplyid] + (float)get_field('quantity', $p->ID):get_field('quantity', $p->ID);
        
        if(get_field('lot_number', $p->ID)){
            $lotn[$supplyid][] = get_field('lot_number', $p->ID);
        }

        if(get_field('expiry_date', $p->ID)){
            $expd[$supplyid][] = get_field('expiry_date', $p->ID);   
        }

        $datesupplies[$supplyid] = array(
            'supply_name' => get_field('supply_name', $supplyid),
            'quantity' => $qty[$supplyid],
            'serial' => (!empty(get_field('serial', $p->ID)))?get_field('serial', $p->ID):false,
            'states__status' => (!empty(get_field('states__status', $p->ID)))?get_field('states__status', $p->ID):false,
            'lot_number' => (isset($lotn[$supplyid]))?implode(',', $lotn[$supplyid]):'',
            'expiry_date' => (isset($expd[$supplyid]))?implode(',', $expd[$supplyid]):''
        );
    endforeach;

    /** end second loop */

    /** get all released supplies within the month */

    $meta_query = array(
        'relation' => 'AND',
        array(
            'key'     => 'release_date',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'  
        ),
        array(
            'key'     => 'supply_name',
            'value'   =>  $suppid
        )
    );

    $addquery = $theme->createQuery('releasesupplies', $meta_query, -1, 'date', 'ASC');
    $qty = array();

    foreach($addquery->posts as $p):
        $name[$p->ID] = get_field('supply_name', $p->ID);
        $supplyid = $name[$p->ID]->ID;
        $qty[$supplyid] = (isset($qty[$supplyid]))?(float)$qty[$supplyid] + (float)get_field('quantity', $p->ID):get_field('quantity', $p->ID);

        $relsupplies[$supplyid] = array(
            'supply_name' => get_field('supply_name', $supplyid),
            'quantity' => $qty[$supplyid]
        );
    endforeach;

    /** end third loop */
endforeach;

$reconarray['overallupplies'] = $overallupplies;
$reconarray['datesupplies'] = $datesupplies;
$reconarray['relsupplies'] = $relsupplies;

var_dump($reconarray);

?>