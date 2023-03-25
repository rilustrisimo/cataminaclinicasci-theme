<?php
/**
 * * Main Class. Classes and functions for Labyog.
 *
 * @author    eyorsogood.com, Rouie Ilustrisimo
 * @package   Eyorsogood
 * @version   1.0.0
 */

/**
 * No direct access to this file.
 *
 * @since 1.0.0
 */
defined( 'ABSPATH' ) || die();

/**
 * Class Labyog
 */
class Theme {
    protected $user;
    protected $post_types = array(
        /**
         * added classes here
         */
        /*
        array(
            'post_type'		=> 'destination',
            'singular_name' => 'Destination',
            'plural_name'	=> 'Destinations',
            'menu_icon' 	=> 'dashicons-universal-access',
            'supports'		=> array( 'title', 'thumbnail')
        )
        */

        array(
            'post_type'		=> 'supplies',
            'singular_name' => 'Supply',
            'plural_name'	=> 'Supplies',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63e5c719d10ce',
            'header'        => array(
                'supply_name' => 'Equipment / Supply Name',
                'department' => 'Department',
                'section' => 'Section',
                'type' => 'Type',
                'purchased_date' => 'Purchased Date',
                'serial' => 'Serial',
                'states__status' => 'State / Status',
                'lot_number' => 'Lot #',
                'expiry_date' => 'Exp. Date',
                'date_added' => 'Date Added',
                'price_per_unit' => 'Price Per Unit'
            )
        ),
        array(
            'post_type'		=> 'actualsupplies',
            'singular_name' => 'Actual Supply',
            'plural_name'	=> 'Actual Supplies',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('supply_name', 'field_63e9e505537a8'),
            'header'        => array(
                'supply_name' => 'Equipment / Supply Name',
                'date_added' => 'Date Added',
                'quantity' => 'Quantity'
            )
        ),
        array(
            'post_type'		=> 'releasesupplies',
            'singular_name' => 'Release Supply',
            'plural_name'	=> 'Release Supplies',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('supply_name', 'field_63eb46224c1ef'),
            'header'        => array(
                'supply_name' => 'Equipment / Supply Name',
                'release_date' => 'Date Released',
                'quantity' => 'Quantity'
            )
        ),
        array(
            'post_type'		=> 'cashcheques',
            'singular_name' => 'Cash & Cheque',
            'plural_name'	=> 'Cash & Cheques',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63eca0ce98710',
            'header'        => array(
                'name_of_bank' => 'Name of Bank',
                'account_number' => 'Account Number',
                'type_of_account' => 'Type',
                'amount' => 'Amount'
            )
        ),
        array(
            'post_type'		=> 'bankbalances',
            'singular_name' => 'Bank Balance',
            'plural_name'	=> 'Bank Balances',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63eca1deed514',
            'header'        => array(
                'description__payee' => 'Description / Payee',
                'adjustment_date' => 'Adjustment Date',
                'check_number' => 'Check Number',
                'amount' => 'Amount'
            )
        ),
        array(
            'post_type'		=> 'liabilities',
            'singular_name' => 'Liability',
            'plural_name'	=> 'Liabilities',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('payee_name', 'field_63eca3058acf3'),
            'header'        => array(
                'payee_name' => 'Payee Name',
                'description__particulars' => 'Description / Particulars',
                'amount' => 'Amount',
                'category' => 'Category',
                'date_added' => 'Date Added',
                'paid' => 'Paid'
            )
        ),
        array(
            'post_type'		=> 'payees',
            'singular_name' => 'Payee',
            'plural_name'	=> 'Payeees',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63eca2c5a6c07',
            'header'        => array(
                'payee_name' => 'Payee Name',
                'contact_number' => 'Contact Number'
            )
        ),
        array(
            'post_type'		=> 'incomeexpenses',
            'singular_name' => 'Income / Expense',
            'plural_name'	=> 'Income / Expenses',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edc459c6b2c',
            'header'        => array(
                'description' => 'Description',
                'amount' => 'Amount',
                'date_added' => 'Date Added',
                'type' => 'Type',
                'income_category' => 'Income Category',
                'expense_category' => 'Expense Category',
                'voucher_number' => 'Voucher Number',
                'remarks' => 'Remarks'
            )
        ),
        array(
            'post_type'		=> 'depreciationitems',
            'singular_name' => 'Depreciation Item',
            'plural_name'	=> 'Depreciation Items',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edc802f181e',
            'header'        => array(
                'item_name' => 'Item Name / Description',
                'value' => 'Value (When Acquired)',
                'date_acquired' => 'Date Acquired',
                'type' => 'Type'
            )
        ),
        array(
            'post_type'		=> 'products',
            'singular_name' => 'Product',
            'plural_name'	=> 'Products',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edca417fadf',
            'header'        => array(
                'product_name' => 'Product Name',
                'product_description' => 'Product Description',
                'date_added' => 'Date Added',
                'price_per_unit' => 'Price Per Unit (SRP)',
                'cost_of_goods_sold' => 'Cost of Goods Sold'
            )
        ),
        array(
            'post_type'		=> 'stocks',
            'singular_name' => 'Stock',
            'plural_name'	=> 'Stocks',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('product_name', 'field_63edcae668ef3'),
            'header'        => array(
                'product_name' => 'Product Name',
                'quantity' => 'Quantity',
                'date_added' => 'Date Added'
            )
        ),
        array(
            'post_type'		=> 'purchases',
            'singular_name' => 'Purchase',
            'plural_name'	=> 'Purchases',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('product_name', 'field_63edcd5789775'),
            'header'        => array(
                'product_name' => 'Product Name',
                'quantity' => 'Quantity',
                'purchase_date' => 'Purchase Date',
                'purchase_total' => 'Purchase Total'
            )
        ),
        array(
            'post_type'		=> 'investors',
            'singular_name' => 'Investor',
            'plural_name'	=> 'Investors',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edcdbd01f01',
            'header'        => array(
                'investor_name' => 'Investor Name',
                'contact_number' => 'Contact Number'
            )
        ),
        array(
            'post_type'		=> 'capitals',
            'singular_name' => 'Capital',
            'plural_name'	=> 'Capitals',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('investor_name', 'field_63edcde87b2f0'),
            'header'        => array(
                'investor_name' => 'Investor Name',
                'invested_amount' => 'Invested Amount'
            )
        ),
        array(
            'post_type'		=> 'retainedearnings',
            'singular_name' => 'Retained Earning',
            'plural_name'	=> 'Retained Earnings',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edd130b2fa4',
            'header'        => array(
                'retained_earnings' => 'Retained Earnings / Undivided Profits',
                'date_added' => 'Date Added'
            )
        ),
        array(
            'post_type'		=> 'declareddividends',
            'singular_name' => 'Declared Dividend',
            'plural_name'	=> 'Declared Dividends',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_63edd191cc48f',
            'header'        => array(
                'declared_dividends' => 'Declared Dividends',
                'date_added' => 'Date Added'
            )
        )
    );
    

    function __autoload() {
        $classes = array();

        foreach($classes as $value){
            require_once PARENT_DIR . '/php/class-'. $value .'.php';
        }
    }

	/**
	 * Constructor runs when this class instantiates.
	 *
	 * @param array $config Data via config file.
	 */
	public function __construct( array $config = array() ) {
        $this->__autoload();
        $this->initActions();
        $this->initFilters();
        $this->user = wp_get_current_user();
    }

    protected function initActions() {
        /**
         * 
         * function should be public when adding to an action hook.
         */

        add_action( 'init', array($this, 'createPostTypes')); 
        add_action( 'template_redirect', array($this, 'redirect_to_homepage'));
        add_action('acf/save_post', array($this, 'my_save_post'));

        add_action( 'wp_ajax_edit_item', array($this, 'edit_item') );
        add_action( 'wp_ajax_nopriv_edit_item', array($this, 'edit_item') ); 


        add_action( 'wp_ajax_load_items_per_search', array($this, 'load_items_per_search') );
        add_action( 'wp_ajax_nopriv_load_items_per_search', array($this, 'load_items_per_search') ); 

        add_action( 'wp_ajax_load_reconciliation_report', array($this, 'load_reconciliation_report') );
        add_action( 'wp_ajax_nopriv_load_reconciliation_report', array($this, 'load_reconciliation_report') ); 


        add_action( 'wp_ajax_load_incomeexpense_report', array($this, 'load_incomeexpense_report') );
        add_action( 'wp_ajax_nopriv_load_incomeexpense_report', array($this, 'load_incomeexpense_report') ); 

        add_action( 'wp_ajax_load_goods_report', array($this, 'load_goods_report') );
        add_action( 'wp_ajax_nopriv_load_goods_report', array($this, 'load_goods_report') ); 

        add_action( 'wp_ajax_load_soc_report', array($this, 'load_soc_report') );
        add_action( 'wp_ajax_nopriv_load_soc_report', array($this, 'load_soc_report') ); 

        
    }

    protected function initFilters() {
        /**
         * Place filters here
         */

    }

    public function createQuery($posttype, $meta_query = array(), $numberposts = -1, $orderby = 'date', $order = 'DESC') {
        $args = array(
            'orderby'			=> $orderby,
            'order'				=> $order,
            'numberposts'	=> $numberposts,
            'post_type'		=> $posttype,
            'meta_query'    => array($meta_query),
            'posts_per_page' => $numberposts
        );

        $u = wp_get_current_user();
        $roles = ( array ) $u->roles;

        if(!current_user_can( 'manage_options' ) && !($roles[0] == "um_accounting")):
            $args['author'] = $u->ID;
        endif;

        $the_query = new WP_Query( $args );

        return $the_query;
    }

    public function redirect_to_homepage() {
        if ( ! is_user_logged_in() and ! is_front_page()) {
            wp_redirect( home_url() );
            exit;
        }
    }

    public function load_reconciliation_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getReconciliationReport($from, $to));

    }

    public function load_incomeexpense_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getIncomeExpensesReport($from, $to));

    }

    public function load_goods_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getGoodsReport($from, $to));

    }

    public function load_soc_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getSOCReport($from, $to));

    }

    public function getQtyOfSupplyAfterDate($supid, $date) {

        /** add supplies */
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'date_added',
                'value'   =>  date('Y-m-d', strtotime($date)),
                'type'      =>  'date',
                'compare' =>  '<'   
            ),
            array(
                'key'     => 'supply_name',
                'value'   =>  $supid
            )
        );

        $query = $this->createQuery('actualsupplies', $meta_query);
        $addqty = 0;

        foreach($query->posts as $supp):
            $addqty += (float)get_field('quantity', $supp->ID);
        endforeach;

        /** release supplies */

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'release_date',
                'value'   =>  date('Y-m-d', strtotime($date)),
                'type'      =>  'date',
                'compare' =>  '<'   
            ),
            array(
                'key'     => 'supply_name',
                'value'   =>  $supid
            )
        );

        $query = $this->createQuery('releasesupplies', $meta_query);
        $relqty = 0;

        foreach($query->posts as $supp):
            $relqty += (float)get_field('quantity', $supp->ID);
        endforeach;

        return ($addqty - $relqty);
    }

    public function getReconciliationReport($from, $to) {
        //$from = '13-02-2023';
        //$to = '13-02-2023';

        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Reconciliation Report</h3>";

        /** get all actual supplies total quantity */

        $addquery = $this->createQuery('supplies');

        foreach($addquery->posts as $p):
            $name[$p->ID] = get_field('supply_name', $p->ID);
            $supplyid = $p->ID;
            $dept = get_field('department', $p->ID);
            $deptslug = str_replace(" ", "_", strtolower($dept));
            $type = get_field('type', $p->ID);
            $typeslug = strtolower($type);

            $overallupplies[$deptslug][$typeslug][$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'department' => $dept,
                'type' => $type,
                'quantity' => $this->getQtyOfSupplyAfterDate($supplyid, $from)
            );

        endforeach;

        /** end first loop */

        ksort($overallupplies); // sort department

        /** get all actual purchased supplies within the month */

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $addquery = $this->createQuery('actualsupplies', $meta_query);
        $datesupplies = array();
        $qty = array();

        foreach($addquery->posts as $p):
            $name[$p->ID] = get_field('supply_name', $p->ID);
            $supplyid = $name[$p->ID]->ID;
            $qty[$supplyid] = (isset($qty[$supplyid]))?(float)$qty[$supplyid] + (float)get_field('quantity', $p->ID):get_field('quantity', $p->ID);
            

            $datesupplies[$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'quantity' => $qty[$supplyid]
            );
        endforeach;

        /** end second loop */

        /** get all released supplies within the month */

        $meta_query = array(
            'key'     => 'release_date',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $addquery = $this->createQuery('releasesupplies', $meta_query);
        $relsupplies = array();
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

        /** loop for the output */
        foreach($overallupplies as $department => $types):
            ksort($types);
            $res .= "<h1>".str_replace("_", " ", strtoupper($department))."</h1>";
            
            foreach($types as $type => $suppdetails):
                $typetext = (strtoupper($type)=="SUPPLY")?"SUPPLIES":"EQUIPMENT";
                $res .= '<div class="report__result-header">'.$typetext.'</div>';
                $res .= "<table>";

                if(strtoupper($type) == "EQUIPMENT"):
                    $res .= "<thead>";
                    $res .= "<tr>";
                    $res .= "<th>EQUIPMENT</th>";
                    $res .= "<th>SERIAL</th>";
                    $res .= "<th>STATES</th>";
                    $res .= "<th>BEG INV</th>";
                    $res .= "<th>PURCHASES</th>";
                    $res .= "<th>TOTAL</th>";
                    $res .= "<th>CONSUMPTION</th>";
                    $res .= "<th>END INV</th>";
                    $res .= "<th>PRICE</th>";
                    $res .= "<th>ACTUAL COUNT</th>";
                    $res .= "<th>VARIANCE</th>";
                    $res .= "<th>TOTAL</th>";
                    $res .= "</tr>";
                    $res .= "</thead>";
                else:
                    $res .= "<thead>";
                    $res .= "<tr>";
                    $res .= "<th>SUPPLY NAME</th>";
                    $res .= "<th>LOT #</th>";
                    $res .= "<th>EXP. DATE</th>";
                    $res .= "<th>BEG INV</th>";
                    $res .= "<th>PURCHASES</th>";
                    $res .= "<th>TOTAL</th>";
                    $res .= "<th>CONSUMPTION</th>";
                    $res .= "<th>END INV</th>";
                    $res .= "<th>PRICE</th>";
                    $res .= "<th>ACTUAL COUNT</th>";
                    $res .= "<th>VARIANCE</th>";
                    $res .= "<th>TOTAL</th>";
                    $res .= "</tr>";
                    $res .= "</thead>";
                endif;

                foreach($suppdetails as $suppid => $suppdeets):
                    if(strtoupper($type) == "EQUIPMENT"):
                        $purchase = (isset($datesupplies[$suppid]['quantity']))?(float)$datesupplies[$suppid]['quantity']:0;
                        $release = (isset($relsupplies[$suppid]['quantity']))?(float)$relsupplies[$suppid]['quantity']:0;
                        $price = (float)get_field('price_per_unit', $suppid);

                        /** body */
                        $res .= "<tbody>";
                        $res .= "<tr>";
                        $res .= "<td>".$suppdeets['supply_name']."</td>";
                        $res .= "<td>".get_field('serial', $suppid)."</td>";
                        $res .= "<td>".get_field('states__status', $suppid)."</td>";
                        $res .= "<td>".(float)$suppdeets['quantity']."</td>";
                        $res .= "<td>".$purchase."</td>";
                        $res .= "<td>".((float)$suppdeets['quantity'] + $purchase)."</td>";
                        $res .= "<td>".$release."</td>";
                        $res .= "<td class='orig-count' data-val='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'>".(((float)$suppdeets['quantity'] + $purchase) - $release)."</td>";
                        $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                        $res .= "<td class='row-actual-count'><input type='number' class='actual-field' min='0' value='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'></td>";
                        $res .= "<td class='row-variance'>0</td>";
                        $res .= "<td class='row-total'>&#8369 ".$this->convertNumber(((((float)$suppdeets['quantity'] + $purchase) - $release) * $price))."</td>";
                        $res .= "</tr>";
                        $res .= "</tbody>";
                        /** body end */
                    else:
                        $purchase = (isset($datesupplies[$suppid]['quantity']))?(float)$datesupplies[$suppid]['quantity']:0;
                        $release = (isset($relsupplies[$suppid]['quantity']))?(float)$relsupplies[$suppid]['quantity']:0;
                        $price = (float)get_field('price_per_unit', $suppid);
                        
                        /** body */
                        $res .= "<tbody>";
                        $res .= "<tr>";
                        $res .= "<td>".$suppdeets['supply_name']."</td>";
                        $res .= "<td>".get_field('lot_number', $suppid)."</td>";
                        $res .= "<td>".get_field('expiry_date', $suppid)."</td>";
                        $res .= "<td>".(float)$suppdeets['quantity']."</td>";
                        $res .= "<td>".$purchase."</td>";
                        $res .= "<td>".((float)$suppdeets['quantity'] + $purchase)."</td>";
                        $res .= "<td>".$release."</td>";
                        $res .= "<td class='orig-count' data-val='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'>".(((float)$suppdeets['quantity'] + $purchase) - $release)."</td>";
                        $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                        $res .= "<td class='row-actual-count'><input type='number' class='actual-field' min='0' value='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'></td>";
                        $res .= "<td class='row-variance'>0</td>";
                        $res .= "<td class='row-total'>&#8369 ".$this->convertNumber(((((float)$suppdeets['quantity'] + $purchase) - $release) * $price))."</td>";
                        $res .= "</tr>";
                        $res .= "</tbody>";
                        /** body end */
                    endif;
                    
                endforeach;

                $res .= "</table>";
            endforeach;
        endforeach;

        /** end output loop */


        return $res;
    }

    public function getPricesOfProductsBeforeDate($date) {

        /** add supplies */
        $meta_query = array(
            array(
                'key'     => 'date_added',
                'value'   =>  date('Y-m-d', strtotime($date)),
                'type'      =>  'date',
                'compare' =>  '<'   
            )
        );

        $query = $this->createQuery('stocks', $meta_query);
        $stocktotprice = 0;
        $stocktotqty = 0;

        foreach($query->posts as $p):
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);

            $stocktotprice += ($qty * $cogs);
            $stocktotqty += $qty;
        endforeach;

        /** release supplies */

        $meta_query = array(
            array(
                'key'     => 'purchase_date',
                'value'   =>  date('Y-m-d', strtotime($date)),
                'type'      =>  'date',
                'compare' =>  '<'   
            )
        );

        $query = $this->createQuery('purchases', $meta_query);
        $purtotprice = 0;
        $purtotqty = 0;

        foreach($query->posts as $p):
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);

            $purtotprice += ($qty * $cogs);
            $purtotqty += $qty;
        endforeach;
        

        return array(($stocktotqty - $purtotqty), ($stocktotprice - $purtotprice));
    }

    public function getGoodsReport($from, $to) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Computation of Income and Expense - Goods</h3>";

        $meta_query = array(
            'key'     => 'purchase_date',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $purcha = $this->createQuery('purchases', $meta_query);
        $totsale = 0;
        $totcogs = 0;
        $pdeets = array();

        foreach($purcha->posts as $p):
            
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $price = (float)get_field('price_per_unit', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);
            

            $pdeets[$pid][$p->ID][] = array(
                'product_name' => $product_name,
                'quantity' => $qty,
                'total_price' => ($qty * $price),
                'price' => $price
            );

            $totsale += ($qty * $price);
            $totcogs += ($qty * $cogs);

        endforeach;

        $res .= "<h1>INCOME</h1>";
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Product Name</th>";
        $res .= "<th>Quantity</th>";
        $res .= "<th>Total Price</th>";
        $res .= "</tr>";
        $res .= "</thead>";
        $res .= "<tbody>";
        foreach($pdeets as $pid => $purchases):
            $itemtot = 0;
            $itemqty = 0;

            foreach($purchases as $purid => $items):
                foreach($items as $k => $item):
                    $name = $item['product_name'];
                    $itemtot += $item['total_price'];
                    $itemqty += $item['quantity'];
                endforeach;
            endforeach;

            $res .= "<tr>";
            $res .= "<td>".$name."</td>";
            $res .= "<td>".$this->convertNumber($itemqty)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($itemtot)."</td>";
            $res .= "</tr>";
        endforeach;
        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>Sale of Goods:</span> &#8369 '.$this->convertNumber($totsale).'</div>';
        $res .= '<div class="report__result-total"><span>Cost of Goods Sold:</span> &#8369 '.$this->convertNumber($totcogs).'</div>';
        $res .= '<div class="report__result-total"><span>Total Gross Income:</span> &#8369 '.$this->convertNumber($totsale - $totcogs).'</div>';
        
        $grossinc = ($totsale - $totcogs);

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $stocks = $this->createQuery('stocks', $meta_query);
        $totsale = 0;
        $totcogs = 0;
        $sdeets = array();

        foreach($stocks->posts as $p):
            
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $price = (float)get_field('price_per_unit', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);
            

            $sdeets[$pid][$p->ID][] = array(
                'product_name' => $product_name,
                'quantity' => $qty,
                'total_price' => ($qty * $cogs),
                'price' => $cogs
            );

            $totsale += ($qty * $price);
            $totcogs += ($qty * $cogs);

        endforeach;

        $res .= "<h1>EXPENSES</h1>";
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Product Name</th>";
        $res .= "<th>Quantity</th>";
        $res .= "<th>Total Price</th>";
        $res .= "</tr>";
        $res .= "</thead>";
        $res .= "<tbody>";
        foreach($sdeets as $pid => $stocks):
            $itemtot = 0;
            $itemqty = 0;

            foreach($stocks as $sid => $items):
                foreach($items as $k => $item):
                    $name = $item['product_name'];
                    $itemtot += $item['total_price'];
                    $itemqty += $item['quantity'];
                endforeach;
            endforeach;

            $res .= "<tr>";
            $res .= "<td>".$name."</td>";
            $res .= "<td>".$this->convertNumber($itemqty)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($itemtot)."</td>";
            $res .= "</tr>";
        endforeach;
        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>Purchase Stock:</span> &#8369 '.$this->convertNumber($totcogs).'</div>';
        $res .= '<div class="report__result-total"><span>Net Income:</span> &#8369 '.$this->convertNumber($grossinc - $totcogs).'</div>';

        return $res;
    }


    public function getSOCReport($from, $to) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Statement of Condition</h3>";

        $query = $this->createQuery('cashcheques');
        $cashonhand = 0;
        $banks = array();
        $totalcandb = 0;

        foreach($query->posts as $k => $p):
            if(get_field('type_of_account', $p->ID) == "Cash on Hand"):
                $cashonhand += (float)get_field('amount', $p->ID);
            else:
                $banks[] = array(
                    'name_of_bank' => get_field('name_of_bank', $p->ID),
                    'account_number' => get_field('account_number', $p->ID),
                    'type_of_account' => get_field('type_of_account', $p->ID),
                    'amount' => get_field('amount', $p->ID)
                );
            endif;

            $totalcandb += (float)get_field('amount', $p->ID);
        endforeach;


        $res .= "<h1>ASSETS</h1>";
        $res .= '<div class="report__result-header">Current Assets</div>';

        $res .= "<table>";
        $res .= "<tbody>";
        $res .= "<tr>";
        $res .= "<td>Cash on hand and in banks</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($totalcandb)."</td>";
        $res .= "</tr>";
        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>Total Cash on Hand / In Banks:</span> &#8369 '.$this->convertNumber($totalcandb).'</div>';


        $res .= '<div class="report__result-header">Supplies - Inventory</div>';

        $res .= "<table>";
        $res .= "<tbody>";

        $meta_query = array(
            'key'     => 'type',
            'value'   => 'Supply'
        );

        $supplies = $this->createQuery('supplies', $meta_query);
        $totsup = 0;

        foreach($supplies->posts as $p):
            
            $supname = get_field('supply_name', $p->ID);
            $price = (float)get_field('price_per_unit', $p->ID);
            $curqty = $this->getQtyOfSupplyAfterDate($p->ID, $to);

            if($curqty <= 0) continue;
            
            $res .= "<tr>";
            $res .= "<td>".$supname."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($price * $curqty)."</td>";
            $res .= "</tr>";
            
            $totsup += ($price * $curqty);

        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>Total Supplies - Inventory:</span> &#8369 '.$this->convertNumber($totsup).'</div>';
        $res .= '<div class="report__result-total"><span>Total Current Assets:</span> &#8369 '.$this->convertNumber($totsup + $totalcandb).'</div>';


        $query = $this->createQuery('depreciationitems');

        foreach($query->posts as $deps):
            $depitems[$deps->ID] = array(
                'description' => get_field('item_name', $deps->ID),
                'value' => (float)get_field('value', $deps->ID),
                'date' => get_field('date_acquired', $deps->ID),
                'type' => get_field('type', $deps->ID)
            );
        endforeach;


        $res .= '<div class="report__result-header">Fixed Assets</div>';

        $deptot = 0;

        $res .= "<table>";
        $res .= "<tbody>";

        $overalllval = 0;

        foreach($depitems as $id => $dep):
            $value = $dep['value'];
            $acdate = strtotime($dep['date']);
            $curdate = strtotime(date('Y-m-d', strtotime($to)));
            $deplimit = (strtolower($dep['type']) == "equipment")?strtotime("+10 years", $acdate):strtotime("+50 years", $acdate);

            $hundred = ($deplimit - $acdate);
            $percent = ($curdate - $acdate);
            $range = (strtotime($to) - strtotime($from));

            $deppercent = ($percent / $hundred);
            $depvalue = (float)$value * $deppercent;
            $rangepercent = ($range / $hundred);
            $depvaluerange = (float)$value * $rangepercent;

            $deptot += (float)$depvaluerange;

            $thisval = (float)$dep['value'] - $depvalue;

            $res .= "<tr>";
            $res .= "<td>".$dep['description']."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($thisval)."</td>";
            $res .= "</tr>";

            $overalllval += $thisval;
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>Total Fixed Assets:</span> &#8369 '.$this->convertNumber($overalllval).'</div>';
        $res .= '<div class="report__result-total"><span>Total Assets:</span> &#8369 '.$this->convertNumber($overalllval + $totsup + $totalcandb).'</div>';
       

        $res .= '<div class="report__result-header">LIABILITIES</div>';
        $res .= '<div class="report__result-header">Current Liabilities</div>';

        $query = $this->createQuery('liabilities');
        $paysupptot = 0;
        $payaccttot = 0;

        foreach($query->posts as $liab):
            if(get_field('paid',$liab->ID) == "NOT PAID"):
                $lslug = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtolower(get_field('category',$liab->ID))));

                if($lslug == "payables_to_suppliers"):
                    $paysupptot += (float)get_field('amount', $liab->ID);
                else:
                    $payaccttot += (float)get_field('amount', $liab->ID);
                endif;
            endif;
        endforeach;

        $res .= "<table>";
        $res .= "<tbody>";
        $res .= "<tr>";
        $res .= "<td>Accounts Payable - Professional/Retainers Fees</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($payaccttot)."</td>";
        $res .= "</tr>";
        $res .= "<tr>";
        $res .= "<td>Accounts Payables - Suppliers</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($paysupptot)."</td>";
        $res .= "</tr>";
        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>Total Liabilities:</span> &#8369 '.$this->convertNumber($payaccttot + $paysupptot).'</div>';

        $res .= '<div class="report__result-header">NETWORTH</div>';
        $res .= '<div class="report__result-header">Capital</div>';


        $query = $this->createQuery('capitals');
        $captot = 0;

        $res .= "<table>";
        $res .= "<tbody>";

        foreach($query->posts as $cap):
            $inv = get_field('investor_name', $cap->ID);
            $invid = $inv->ID;
            
            $res .= "<tr>";
            $res .= "<td>".get_field('investor_name', $invid)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber((float)get_field('invested_amount', $cap->ID))."</td>";
            $res .= "</tr>";
            $res .= "<tr>";

            $captot += (float)get_field('invested_amount', $cap->ID);
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>Total Capital:</span> &#8369 '.$this->convertNumber($captot).'</div>';

        $meta_query = array(
            array(
                'key'     => 'date_added',
                'value'   =>  date('Y-m-d', strtotime($to)),
                'type'      =>  'date',
                'compare' =>  '<='   
            )
        );


        $query = $this->createQuery('retainedearnings', $meta_query);
        $rettot = 0;

        foreach($query->posts as $ret):
            $rettot += (float)get_field('retained_earnings', $ret->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Retained Earnings/Undivided Profits:</span> &#8369 '.$this->convertNumber($rettot).'</div>';

        $query = $this->createQuery('declareddividends', $meta_query);
        $dectot = 0;

        foreach($query->posts as $dec):
            $dectot += (float)get_field('declared_dividends', $dec->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Dividends Declared:</span> (&#8369 '.$this->convertNumber($dectot).')</div>';
        $res .= '<div class="report__result-total">&#8369 '.$this->convertNumber($rettot - $dectot).'</div>';


        /** goods profit */

        $meta_query = array(
            'key'     => 'purchase_date',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $purcha = $this->createQuery('purchases', $meta_query);
        $totsale = 0;
        $totcogs = 0;
        $pdeets = array();

        foreach($purcha->posts as $p):
            
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $price = (float)get_field('price_per_unit', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);
            

            $pdeets[$pid][$p->ID][] = array(
                'product_name' => $product_name,
                'quantity' => $qty,
                'total_price' => ($qty * $price),
                'price' => $price
            );

            $totsale += ($qty * $price);
            $totcogs += ($qty * $cogs);

        endforeach;


        foreach($pdeets as $pid => $purchases):
            $itemtot = 0;
            $itemqty = 0;

            foreach($purchases as $purid => $items):
                foreach($items as $k => $item):
                    $name = $item['product_name'];
                    $itemtot += $item['total_price'];
                    $itemqty += $item['quantity'];
                endforeach;
            endforeach;

        endforeach;
        
        $grossinc = ($totsale - $totcogs);

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $stocks = $this->createQuery('stocks', $meta_query);
        $totsale = 0;
        $totcogs = 0;
        $sdeets = array();

        foreach($stocks->posts as $p):
            
            $product = get_field('product_name', $p->ID);
            $pid = $product->ID;
            $product_name = get_field('product_name', $pid);
            $price = (float)get_field('price_per_unit', $pid);
            $cogs = (float)get_field('cost_of_goods_sold', $pid);
            $qty = (float)get_field('quantity', $p->ID);
            

            $sdeets[$pid][$p->ID][] = array(
                'product_name' => $product_name,
                'quantity' => $qty,
                'total_price' => ($qty * $cogs),
                'price' => $cogs
            );

            $totsale += ($qty * $price);
            $totcogs += ($qty * $cogs);

        endforeach;

       
        foreach($sdeets as $pid => $stocks):
            $itemtot = 0;
            $itemqty = 0;

            foreach($stocks as $sid => $items):
                foreach($items as $k => $item):
                    $name = $item['product_name'];
                    $itemtot += $item['total_price'];
                    $itemqty += $item['quantity'];
                endforeach;
            endforeach;

        endforeach;

        $res .= '<div class="report__result-total"><span>Net Profit/Income ('.date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to)).'):</span> &#8369 '.$this->convertNumber($grossinc - $totcogs).'</div>';
        $res .= '<div class="report__result-total">&#8369 '.$this->convertNumber(($rettot - $dectot) + ($grossinc - $totcogs)).'</div>';
        $res .= '<div class="report__result-total"><span>Total Networth:</span> &#8369 '.$this->convertNumber($captot + ($rettot - $dectot) + ($grossinc - $totcogs)).'</div>';
        $res .= '<div class="report__result-total" style="margin-bottom: 50px;"><span>Total Liabilities and Networth:</span> &#8369 '.$this->convertNumber(($payaccttot + $paysupptot) + $captot + ($rettot - $dectot) + ($grossinc - $totcogs)).'</div>';

        return $res;
    }

    public function getFinancialReport() {
        //$from = '13-02-2023';
        //$to = '13-02-2023';

        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y')."</h2>";
        $res .= "<h3>Financial Report</h3>";
        $res .= "<h1>CASH ON HAND</h1>";
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<th>Cash on Hand</th>";
        $res .= "<th>Amount</th>";
        $res .= "</thead>";
        $res .= "<tbody>";

        $query = $this->createQuery('cashcheques');
        $cashonhand = 0;
        $banks = array();
        $totalcandb = 0;

        foreach($query->posts as $k => $p):
            if(get_field('type_of_account', $p->ID) == "Cash on Hand"):
                $cashonhand += (float)get_field('amount', $p->ID);
            else:
                $banks[] = array(
                    'name_of_bank' => get_field('name_of_bank', $p->ID),
                    'account_number' => get_field('account_number', $p->ID),
                    'type_of_account' => get_field('type_of_account', $p->ID),
                    'amount' => get_field('amount', $p->ID)
                );
            endif;

            $totalcandb += (float)get_field('amount', $p->ID);
        endforeach;

        $res .= "<td>Cash on Hand</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($cashonhand)."</td>";
        $res .= "</tbody>";
        $res .= "</table>";


        $res .= "<h1>CASH IN BANKS</h1>";
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Name of Bank</th>";
        $res .= "<th>Account No.</th>";
        $res .= "<th>Type of Account</th>";
        $res .= "<th>Balance</th>";
        $res .= "</tr>";
        $res .= "</thead>";

        $res .= "<tbody>";
        foreach($banks as $b):
            $res .= "<tr>";
            $res .= "<td>".$b['name_of_bank']."</td>";
            $res .= "<td>".$b['account_number']."</td>";
            $res .= "<td>".$b['type_of_account']."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($b['amount'])."</td>";
            $res .= "</tr>";
        endforeach;
        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>TOTAL CASH ON HAND AND IN BANKS:</span> &#8369 '.$this->convertNumber($totalcandb).'</div>';

        $res .= "<h1 style='margin-top: 35px;'>ADJUSTED BALANCE</h1>";
        $res .= '<div class="report__result-header">Outstanding Checks</div>';
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Date</th>";
        $res .= "<th>Payee</th>";
        $res .= "<th>Check No.</th>";
        $res .= "<th>Amount</th>";
        $res .= "</tr>";
        $res .= "</thead>";

        $res .= "<tbody>";

        $query = $this->createQuery('bankbalances');
        $totadj = 0;
        foreach($query->posts as $bb):
            $res .= "<tr>";
            $res .= "<td>".get_field('adjustment_date', $bb->ID)."</td>";
            $res .= "<td>".get_field('description__payee', $bb->ID)."</td>";
            $res .= "<td>".get_field('check_number', $bb->ID)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber((float)get_field('amount', $bb->ID))."</td>";
            $res .= "</tr>";

            $totadj += (float)get_field('amount', $bb->ID);
        endforeach;
        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>TOTAL OUTSTANDING CHECKS:</span> &#8369 '.$this->convertNumber($totadj).'</div>';
        $res .= '<div class="report__result-total" style="margin-bottom: 50px;"><span>ADJUSTED BANK BALANCE:</span> &#8369 '.$this->convertNumber($totalcandb - $totadj).'</div>';

        


        return $res;
    }

    public function getLiabilitiesReport() {

        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y')."</h2>";
        $res .= "<h3>Computation of Current - Liabilities</h3>";
        $res .= "<h1>CURRENT LIABILITIES</h1>";

        $query = $this->createQuery('liabilities');
        $liaball = array();

        foreach($query->posts as $liab):
            if(get_field('paid',$liab->ID) == "NOT PAID"):
                $lslug = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtolower(get_field('category',$liab->ID))));
                $liaball[$lslug][$liab->ID] = array(
                    'payee' => get_field('payee_name', get_field('payee_name', $liab->ID)),
                    'description' => get_field('description__particulars', $liab->ID),
                    'amount' => (float)get_field('amount', $liab->ID),
                    'category' => get_field('category', $liab->ID)
                );
            endif;
        endforeach;
        ksort($liaball);
        $total = 0;
        foreach($liaball as $cat => $payees):
            $flag = true;
            $subtot = 0;
            foreach($payees as $pid => $p):
                if($flag):
                    $res .= "<h1>".$p['category']."</h1>";
                    $res .= "<table>";
                    $res .= "<thead>";
                    $res .= "<tr>";
                    $res .= "<th>Payee</th>";
                    $res .= "<th>Description / Particulars</th>";
                    $res .= "<th>Amount</th>";
                    $res .= "</tr>";
                    $res .= "</thead>";
                    $res .= "<tbody>";

                    $flag = false;
                endif;

                $res .= "<tr>";
                $res .= "<td>".$p['payee']."</td>";
                $res .= "<td>".$p['description']."</td>";
                $res .= "<td>&#8369 ".$this->convertNumber($p['amount'])."</td>";
                $res .= "</tr>";

                $subtot += (float)$p['amount'];
                $total += (float)$p['amount'];
            endforeach;
            $res .= "</tbody>";
            $res .= "</table>";
            $res .= '<div class="report__result-total" style="text-align: right"><span>Sub-total:</span> &#8369 '.$this->convertNumber($subtot).'</div>';
        endforeach;

        $res .= '<div class="report__result-total" style="text-align: right;margin-bottom:35px;"><span>Total Current Liabilities:</span> &#8369 '.$this->convertNumber($total).'</div>';



        return $res;
    }


    public function getIncomeExpensesReport($from, $to) {
        //$from = '13-02-2023';
        //$to = '13-02-2023';

        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Statement of Income & Expense</h3>";

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $query = $this->createQuery('incomeexpenses', $meta_query);
        $incs = array();
        $exps = array();

        foreach($query->posts as $p):
            $type = strtolower(get_field('type',$p->ID));
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", $type));

            if($slug == "income"):
                $incs[$p->ID] = array(
                    'description' => get_field('description', $p->ID),
                    'amount' => (float)get_field('amount', $p->ID),
                    'type' => ucfirst($type)
                );
            else:
                $exps[$p->ID] = array(
                    'description' => get_field('description', $p->ID),
                    'amount' => (float)get_field('amount', $p->ID),
                    'type' => ucfirst($type)
                );
            endif;

        endforeach;


        $query = $this->createQuery('depreciationitems');

        foreach($query->posts as $deps):
            $depitems[$deps->ID] = array(
                'description' => get_field('item_name', $deps->ID),
                'value' => (float)get_field('value', $deps->ID),
                'date' => get_field('date_acquired', $deps->ID),
                'type' => get_field('type', $deps->ID)
            );
        endforeach;


        $res .= "<h1>INCOME</h1>";

        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Description</th>";
        $res .= "<th>Date Added</th>";
        $res .= "<th>Amount</th>";
        $res .= "</tr>";
        $res .= "</thead>";

        $res .= "<tbody>";
        
        ksort($incs);

        $totinc = 0;

        foreach($incs as $id => $i):
            $res .= "<tr>";
            $res .= "<td>".$i['description']."</td>";
            $res .= "<td>".get_field('date_added', $id)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($i['amount'])."</td>";
            $res .= "</tr>";

            $totinc += (float)$i['amount'];
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>TOTAL GROSS INCOME:</span> &#8369 '.$this->convertNumber($totinc).'</div>';


        $res .= "<h1>EXPENSES</h1>";

        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Description</th>";
        $res .= "<th>Date Added</th>";
        $res .= "<th>Amount</th>";
        $res .= "</tr>";
        $res .= "</thead>";

        $res .= "<tbody>";
        
        ksort($exps);

        $totexps = 0;

        foreach($exps as $id => $i):
            $res .= "<tr>";
            $res .= "<td>".$i['description']."</td>";
            $res .= "<td>".get_field('date_added', $id)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($i['amount'])."</td>";
            $res .= "</tr>";

            $totexps += (float)$i['amount'];
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>TOTAL EXPENSES:</span> &#8369 '.$this->convertNumber($totexps).'</div>';
        $res .= '<div class="report__result-total"><span>NET INCOME (LOSS) BEFORE AMORT/DEPRECIATION:</span> &#8369 '.$this->convertNumber($totinc - $totexps).'</div>';

        $res .= "<h1>DEPRECIATION ITEMS</h1>";

        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<tr>";
        $res .= "<th>Description</th>";
        $res .= "<th>Date Acquired</th>";
        $res .= "<th>Value Acquired</th>";
        $res .= "<th>Overall Depreciation</th>";
        $res .= "<th>Depreciation (".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to)).")</th>";
        $res .= "</tr>";
        $res .= "</thead>";

        $res .= "<tbody>";
        $deptot = 0;

        foreach($depitems as $id => $dep):
            $value = $dep['value'];
            $acdate = strtotime($dep['date']);
            $curdate = strtotime(date('Y-m-d', strtotime($to)));
            $deplimit = (strtolower($dep['type']) == "equipment")?strtotime("+10 years", $acdate):strtotime("+50 years", $acdate);

            $hundred = ($deplimit - $acdate);
            $percent = ($curdate - $acdate);
            $range = (strtotime($to) - strtotime($from));

            $deppercent = ($percent / $hundred);
            $depvalue = (float)$value * $deppercent;
            $rangepercent = ($range / $hundred);
            $depvaluerange = (float)$value * $rangepercent;

            $deptot += (float)$depvaluerange;

            $res .= "<tr>";
            $res .= "<td>".$dep['description']."</td>";
            $res .= "<td>".$dep['date']."</td>";
            $res .= "<td>".$this->convertNumber($dep['value'])."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($depvalue)."</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($depvaluerange)."</td>";
            $res .= "</tr>";
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>DEPRECIATION EXPENSES:</span> &#8369 '.$this->convertNumber($deptot).'</div>';
        $res .= '<div class="report__result-total" style="margin-bottom: 35px;"><span>NET INCOME:</span> &#8369 '.$this->convertNumber(($totinc - $totexps) - $deptot).'</div>';
       

        return $res;
    }

    public function load_items_per_search(){
        $search = ($_POST['search'] == "false")?false:$_POST['search'];
        
        $this->getItemsSearch($search, $_POST['pt']);
    }

    public function getItemsSearch($search = false, $pt, $paged = 1){
        $search = (strlen(trim($search)) == 0)?false:$search;

        ob_start();

        foreach($this->post_types as $ptypes):

            if($pt == $ptypes['post_type']):
                $header = $ptypes['header'];

                $this->createCustomPostListHtml($ptypes['post_type'], 20, $header, $search);
            endif;

        endforeach;


        wp_send_json_success(ob_get_clean());
    }

    public function getUserData() {
        return $this->user;
    }

    public function get_breadcrumb() {
        if (is_category() || is_single()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
            the_category(' &bull; ');
                if (is_single()) {
                    echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
                    the_title();
                }
        } elseif (is_page()) {
            $par_id = wp_get_post_parent_id(get_the_ID());

            if($par_id !== 0):
                echo get_the_title($par_id);
                echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
                echo '<span>'.get_the_title().'</span>';
            else:
                echo get_the_title();
            endif;
        } elseif (is_search()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
            echo '"<em>';
            echo the_search_query();
            echo '</em>"';
        }
    }

    public function createPostQuery($postType, $postPerPage, $pagination = false, $meta_query = array(), $s = false) {
        $rows = array();
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'post_type' => $postType,
            'post_status' => array('publish'),
            'posts_per_page' => $postPerPage,
            'paged' => $paged,
            'orderby'			=> 'date',
            'order'				=> 'DESC',
            'meta_query'        => $meta_query,
            's' => $s
        );

        $u = wp_get_current_user();
        $roles = ( array ) $u->roles;

        if(!current_user_can( 'manage_options' ) && !($roles[0] == "um_accounting")):
            $args['author'] = $u->ID;
        endif;

        $pagi = '';
    
        $the_query = new WP_Query( $args );
        // The Loop
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $fields = get_fields(get_the_ID());
    
                $rows[get_the_ID()] = $fields;
            } // end while
        } // endif
    
        if($pagination){
            $pagi = '<div class="pagination">'.paginate_links( array(
                'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'total'        => $the_query->max_num_pages,
                'current'      => max( 1, get_query_var( 'paged' ) ),
                'format'       => '?paged=%#%',
                'show_all'     => false,
                'type'         => 'plain',
                'end_size'     => 2,
                'mid_size'     => 1,
                'prev_next'    => true,
                'prev_text'    => sprintf( '<i></i> %1$s', __( '<i class="fas fa-angle-double-left"></i>', 'text-domain' ) ),
                'next_text'    => sprintf( '%1$s <i></i>', __( '<i class="fas fa-angle-double-right"></i>', 'text-domain' ) ),
                'add_args'     => false,
                'add_fragment' => '',
            ) ).'</div>';
        }
    
        // Reset Post Data
        wp_reset_postdata();
    
        return array($rows, $pagi);
    }

    public function edit_item(){
        $params = $_POST['p'];

        ob_start();
        
        $this->updateAcfForm($params['id'], $params['form'], 'Update Item');

        wp_send_json_success(ob_get_clean());
    }

    public function convertNumber($num){
        return number_format($num, 2, '.', ',');
    }

    public function createCustomPostListHtml($postType, $postPerPage, $header, $s = false) {
        $post_query = $this->createPostQuery($postType, $postPerPage, true, array(), $s);
        $posts = $post_query[0];
        $pagination = $post_query[1];

        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        foreach($header as $key => $value){
            echo '<th>'.$value.'</th>';
        }
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';
        
        foreach($posts as $postid => $p){
            unset($p['_validate_email']);
            $p['actions'] = '<a href="#" class="edit-item action-btn" item-id="'.$postid.'"><i class="fa-solid fa-pen-to-square"></i></a><a href="'.get_delete_post_link($postid).'" class="delete-item action-btn" item-id="'.$postid.'" title="Are you sure you want to delete '.get_the_title($postid).'?"><i class="fa-solid fa-delete-left"></i></a>';

            echo '<tr>';

            foreach($header as $key => $value){
                $value = (isset($p[$key]))?$p[$key]:'';
                $fobj = get_field_object($key, $postid);

                if(isset($fobj['type']) && ($fobj['type'] == "number")):
                    echo '<td>'.$this->convertNumber((float)$value).'</td>';
                    continue;
                endif;

                if(is_object($value)):
                    echo '<td>'.$value->post_title.'</td>';
                    continue;
                endif;

                if($key == "purchase_total"):
                    $pid = get_field('product_name', $postid);
                    $price = (float)get_field('price_per_unit', $pid) * (float)get_field('quantity', $postid);
                    echo '<td>&#8369 '.$this->convertNumber($price).'</td>';
                    continue;
                endif;
                
                echo '<td>'.$value.'</td>';
            }

            echo '<td>'.$p['actions'].'</td>';
            
            echo '</tr>';
        }
        echo '</tbody>';

        echo '</table>';

        echo $pagination;
    }

    public function initAcfScripts(){
        return acf_form_head();
    }

    public function createAcfForm($fieldGroupId, $postType, $button = 'Submit', $redirect = null){
        return 	acf_form(array(
            'post_id'		=> 'new_post',
            'post_title'	=> false,
            'post_content'	=> false,
            'field_groups'	=> array($fieldGroupId),
            //'submit_value'	=> $button,
            'html_submit_button' => '<a href="#" class="acf-button button button-primary button-large">'.$button.'</a>',
            'new_post'		=> array(
                'post_type'		=> $postType,
                'post_status'	=> 'publish'
            ),
            'form' => true,
            'return' => (is_null($redirect))?get_permalink(get_the_ID()):home_url('/'.$redirect),
            'updated_message' => __("Account Created", 'acf'),
        ));
    }

    public function updateAcfForm($postid, $fieldGroupId, $button = 'Update', $redirect = null) {
        return acf_form(array(
            'post_id'		=> $postid,
            'post_title'	=> false,
            'post_content'	=> false,
            'field_groups'	=> array($fieldGroupId),
            'submit_value'	=> $button,
            //'html_submit_button' => '<a href="#" class="acf-button button button-primary button-large">'.$button.'</a>',
            'form' => true,
            'return' => (is_null($redirect))?get_permalink(get_the_ID()):home_url('/'.$redirect)
        ));
    }

    public function my_save_post( $post_id ) {	

        if(isset($_POST['_acf_post_id'])) {
            /**
             * get post details
             */
            $post_values = get_post($post_id);


            /**
             * bail out if not a custom type and admin
             */
            $types = array();

            foreach($this->post_types as $pt):
                $types[] = $pt['post_type']; 
            endforeach;
            
            if(!(in_array($post_values->post_type, $types))){
                return;
            }

            if($_POST['_acf_post_id'] == "new_post"){
                /**
                 * applicant set values
                 */

                foreach($this->post_types as $pt):
                    if($post_values->post_type == $pt['post_type']){
                        /**
                         * update post
                         */

                        if(is_array($pt['title_acf']) && is_object(get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]))):
                            $fobj = get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]);
                            $title = (is_array($pt['title_acf']))?$fobj->post_title:$_POST['acf'][$pt['title_acf']];
                        else:
                            $title = (is_array($pt['title_acf']))?get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]):$_POST['acf'][$pt['title_acf']];
                        endif;
    
                        $my_post = array(
                            'ID'           => $post_id,
                            'post_title'   => $title
                        );
    
                        wp_update_post( $my_post );
                    }
                endforeach;

                /**
                 *  Clear POST data
                 */
                unset($_POST);

                /**
                 * notifications
                 */
         
            }
            else if($_POST['_acf_post_id'] == $post_id) {

                foreach($this->post_types as $pt):
                    if($post_values->post_type == $pt['post_type']){
                        /**
                         * update post
                         */
                        if(is_array($pt['title_acf']) && is_object(get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]))):
                            $fobj = get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]);
                            $title = (is_array($pt['title_acf']))?$fobj->post_title:$_POST['acf'][$pt['title_acf']];
                        else:
                            $title = (is_array($pt['title_acf']))?get_field($pt['title_acf'][0], $_POST['acf'][$pt['title_acf'][1]]):$_POST['acf'][$pt['title_acf']];
                        endif;

                        
    
                        $my_post = array(
                            'ID'           => $post_id,
                            'post_title'   => $title
                        );
    
                        wp_update_post( $my_post );
                    }
                endforeach;

                /**
                 *  Clear POST data
                 */
                unset($_POST);

                /**
                 * notifications
                 */

            }
        }
    }

    public function createPostTypes() {
        /*
        * Added Theme Post Types
        *
        */
        // Uncomment the $a_post_types declaration to register your custom post type
        
        $a_post_types = $this->post_types;

        if( !empty( $a_post_types ) ) {
            foreach( $a_post_types as $a_post_type ) {
                $a_defaults = array(
                    'supports'		=> $a_post_type['supports'],
                    'has_archive'	=> TRUE
                );
    
                $a_post_type = wp_parse_args( $a_post_type, $a_defaults );
    
                if( !empty( $a_post_type['post_type'] ) ) {
    
                    $a_labels = array(
                        'name'				=> $a_post_type['plural_name'],
                        'singular_name'		=> $a_post_type['singular_name'],
                        'menu_name'			=> $a_post_type['plural_name'],
                        'name_admin_bar'		=> $a_post_type['singular_name'],
                        'add_new_item'			=> 'Add New '.$a_post_type['singular_name'],
                        'new_item'			=> 'New '.$a_post_type['singular_name'],
                        'edit_item'			=> 'Edit '.$a_post_type['singular_name'],
                        'view_item'			=> 'View '.$a_post_type['singular_name'],
                        'all_items'			=> 'All '.$a_post_type['plural_name'],
                        'search_items'			=> 'Search '.$a_post_type['plural_name'],
                        'parent_item_colon'		=> 'Parent '.$a_post_type['plural_name'],
                        'not_found'			=> 'No '.$a_post_type['singular_name'].' found',
                        'not_found_in_trash'	=> 'No '.$a_post_type['singular_name'].' found in Trash'
                    );
    
                    $a_args = array(
                        'labels'				=> $a_labels,
                        'show_in_menu'			=> true,
                        'show_ui'				=> true,
                        'rewrite'				=> array( 'slug' => $a_post_type['post_type'] ),
                        'capability_type'		=> 'post',
                        'has_archive'			=> $a_post_type['has_archive'],
                        'supports'				=> $a_post_type['supports'],
                        'publicly_queryable' 	=> true,
                        'public' 				=> true,
                        'query_var' 			=> true,
                        'menu_icon'				=> $a_post_type['menu_icon']
                    );
    
                    register_post_type( $a_post_type['post_type'], $a_args );
                }
            }
        }
    }

    public function initSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return true;
    }
}
