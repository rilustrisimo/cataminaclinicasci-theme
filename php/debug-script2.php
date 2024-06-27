<?php
require_once( __DIR__ . '/../../../../wp-load.php' );
require_once( __DIR__ . '/class-main.php' );

$theme = new Theme();

$batchData = array(
    1188 => 'Cell Pack'
);
$to = 'March 4, 2024';
$from = 'April 4, 2024';

$suppdept = array();

$filename = 'batch_process_supplies - '.$to.'.csv'; // Specify your CSV file name


foreach($batchData as $suppid => $supp):

    $price = (float)get_field('price_per_unit', $suppid);
    $curqty = $this->getQtyOfSupplyAfterDate($suppid, $to);

    //if($curqty <= 0) continue;

    $dept = get_field('department', $suppid);
    $deptslug = strtolower(str_replace(" ", "_", $dept));
    $stype = strtolower(str_replace(" ", "_", get_field('type', $suppid)));

    

    if(isset($suppdept[$deptslug][$stype])):
        $suppdept[$deptslug][$stype] += ($price * $curqty);
    else:
        $suppdept[$deptslug][$stype] = ($price * $curqty);
    endif;

    /** csv func */

    // Prepare the data to be written to CSV
    $data = array(
        'ID' => $suppid,
        'department' => $deptslug,
        'quantity' => $curqty,
        'price' => $price,
        'total price' => ($price * $curqty),
    );

    // Append the data to the CSV file
    $this->append_to_csv($filename, $data);

    /** csv func end */

endforeach;



?>