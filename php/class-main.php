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
                'quantity' => 'Quantity',
                'serial' => 'Serial',
                'states__status' => 'State / Status',
                'lot_number' => 'Lot #',
                'expiry_date' => 'Exp. Date',
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
                'quantity' => 'Quantity',
                'department' => 'Department',
                'confirmed' => 'Confirmed',
            )
        ),
        array(
            'post_type'		=> 'banks',
            'singular_name' => 'Bank',
            'plural_name'	=> 'Banks',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_64212ab4b281d',
            'header'        => array(
                'name_of_bank' => 'Name of Bank',
                'account_number' => 'Account Number',
                'type_of_account' => 'Type'
            )
        ),
        array(
            'post_type'		=> 'cashcheques',
            'singular_name' => 'Cash & Cheque',
            'plural_name'	=> 'Cash & Cheques',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => array('name_of_bank', 'field_64212ccd1b252'),
            'header'        => array(
                'name_of_bank' => 'Name of Bank',
                'type_of_account' => 'Account Type',
                'account_number' => 'Account Number',
                'amount' => 'Amount',
                'date_added' => 'Date Added'
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
                'official_receipt' => 'OR',
                'sales_invoice' => 'SI',
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
            'post_type'		=> 'beforeincometax',
            'singular_name' => 'Before Income Tax',
            'plural_name'	=> 'Before Income Taxes',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_66f26824ef620',
            'header'        => array(
                'pre-tax_income_amount' => 'Pre-Tax Income Amount',
                'description' => 'Description',
                'date_added' => 'Date Added',
                'applicable_period' => 'Applicable Period'
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
            'post_type'		=> 'unrecordedcredits',
            'singular_name' => 'Unrecorded Credit',
            'plural_name'	=> 'Unrecorded Credits',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_66eed7325b875',
            'header'        => array(
                'credit_amount' => 'Credit Amount',
                'description' => 'Description',
                'date_added' => 'Date Added',
                'source' => 'Source'
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
        ),
        array(
            'post_type'		=> 'unrecordeddebits',
            'singular_name' => 'Unrecorded Debit',
            'plural_name'	=> 'Unrecorded Debits',
            'menu_icon' 	=> 'dashicons-portfolio',
            'supports'		=> array( 'title', 'thumbnail'),
            'title_acf'     => 'field_66f267ad50a56',
            'header'        => array(
                'debit_amount' => 'Debit Amount',
                'description' => 'Description',
                'date_added' => 'Date Added',
                'reason' => 'Reason'
            )
        )
    );

    protected $departmentArr = array(
        'ALL' => 0,
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
        'CLINIC A' => 12,
        'CLINIC B' => 12,
        'CLINIC C' => 12,
        'CLINIC D' => 12,
        'PHILHEALTH - KP' => 11,
        'PHILHEALTH - ASC' => 7,
        'PHILHEALTH - CLINIC A' => 12,
        'DSWD' => 10,
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
        add_action( 'wp_ajax_load_financial_report', array($this, 'load_financial_report') );
        add_action( 'wp_ajax_nopriv_load_financial_report', array($this, 'load_financial_report') ); 
        add_action( 'wp_ajax_load_release_data', array($this, 'load_release_data') );
        add_action( 'wp_ajax_nopriv_load_release_data', array($this, 'load_release_data') ); 
        add_action( 'wp_ajax_batch_process_supplies', array($this, 'batch_process_supplies') );
        add_action( 'wp_ajax_nopriv_batch_process_supplies', array($this, 'batch_process_supplies') ); 
        add_action( 'wp_ajax_batch_process_supplies_recon', array($this, 'batch_process_supplies_recon') );
        add_action( 'wp_ajax_nopriv_batch_process_supplies_recon', array($this, 'batch_process_supplies_recon') ); 
        add_action( 'wp_ajax_render_recon_output', array($this, 'render_recon_output') );
        add_action( 'wp_ajax_nopriv_render_recon_output', array($this, 'render_recon_output') ); 
        add_action('wp_ajax_get_department_releases', array($this, 'get_department_releases'));
        add_action('wp_ajax_update_release_status', array($this, 'update_release_status'));
        add_action('wp_ajax_get_pending_release_count', array($this, 'get_pending_release_count'));
    }

    protected function initFilters() {
        add_filter('acf/fields/post_object/query/name=supply_name', array($this, 'my_acf_fields_post_object_query_supply_name'), 10, 3);
        add_action('wp_ajax_get_filtered_release_supplies', array($this, 'getFilteredReleaseSupplies'));
        add_action('wp_ajax_export_filtered_supplies_pdf', array($this, 'export_filtered_supplies_pdf'));
        add_filter('acf/validate_value/key=field_67e76678ffc0a', array($this, 'validate_department_selection'), 10, 4);
    }

    /**
     * Validate department selection to prevent "SELECT DEPARTMENT" submissions
     *
     * @param bool|string $valid Whether the value is valid
     * @param mixed $value The field value
     * @param array $field The field array
     * @param string $input The input name attribute
     * @return bool|string
     */
    public function validate_department_selection($valid, $value, $field, $input) {
        // If $valid is not true, another validation has already failed, so return that error
        if ($valid !== true) {
            return $valid;
        }
        
        // Check if value is "SELECT DEPARTMENT" or empty
        if ($value === "SELECT DEPARTMENT" || empty($value)) {
            return "Please Select Department to Release Supply";
        }
        
        return $valid;
    }

    public function getFilteredReleaseSupplies() {
        check_ajax_referer('filter_release_supplies', 'nonce');

        $from_date = sanitize_text_field($_POST['from_date']);
        $to_date = sanitize_text_field($_POST['to_date']);
        $department = isset($_POST['department']) ? $_POST['department']: '';

        // Query release supplies within date range
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'release_date',
                'value'   => array(date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))),
                'type'    => 'date',
                'compare' => 'BETWEEN'
            ),
            array(
                'key'     => 'confirmed',
                'value'   => '1',
                'compare' => '='
            )
        );

        // If department is specified and not ALL (0), add department filter
        $author_id = null;
        if ($department) {
            // Check current user permissions
            $current_user = wp_get_current_user();
            $is_admin = current_user_can('manage_options');
            $is_accounting = in_array('um_accounting', $current_user->roles);
            
            if ($is_admin || $is_accounting) {
                // For admin or accounting, filter by department ID
                $users_id = ($department == 'ALL') ? '' : $this->departmentArr[$department];
                
                if (!empty($users_id)) {
                    $author_ids = $users_id;
                    
                    $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $author_ids);
                } else {
                    $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC');
                }
            } else {
                // For regular users, only show their own department data
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $current_user->ID);
            }
        } else {
            $current_user = wp_get_current_user();

            // If no department filter or ALL is selected
            if (current_user_can('manage_options') || in_array('um_accounting', wp_get_current_user()->roles)) {
                // Admin or accounting users can see all departments
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC');
            } else {
                // Regular users can only see their own data
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $current_user->ID);
            }
        }
        
        // Group and sum quantities by supply name
        $grouped_supplies = array();
        foreach ($query->posts as $post) {
            $supply_name = get_field('supply_name', $post->ID);
            
            // Skip if supply name is null or empty
            if (!$supply_name || empty($supply_name->post_title)) {
                continue;
            }
            
            $quantity = (float)get_field('quantity', $post->ID);
            $price_per_unit = (float)get_field('price_per_unit', $supply_name->ID);
            
            // Get author/released from information
            $author_id = $post->post_author;
            $author_name = get_the_author_meta('display_name', $author_id);
            $department_name = '';
                
            // Find department name based on user ID
            foreach ($this->departmentArr as $dept => $id) {
                if ($id == $author_id) {
                    $department_name = $dept;
                    break;
                }
            }
            
            // Use department name if found, otherwise use author name
            $released_from = $department_name ?: $author_name;
            
            // Get released to department
            $released_to = get_field('department', $post->ID);
            
            if (!isset($grouped_supplies[$supply_name->ID])) {
                $grouped_supplies[$supply_name->ID] = array(
                    'supply_name' => $supply_name->post_title,
                    'total_quantity' => 0,
                    'price_per_unit' => $price_per_unit,
                    'released_from' => $released_from,
                    'released_to' => $released_to
                );
            } else {
                // Maintain the released from/to values - we'll use the last one in the group
                $grouped_supplies[$supply_name->ID]['released_from'] = $released_from;
                $grouped_supplies[$supply_name->ID]['released_to'] = $released_to;
            }
            $grouped_supplies[$supply_name->ID]['total_quantity'] += $quantity;
        }

        // Convert to array and sort by supply name
        $results = array_values($grouped_supplies);
        usort($results, function($a, $b) {
            return strcmp($a['supply_name'], $b['supply_name']);
        });

        wp_send_json_success($results);
    }

    public function export_filtered_supplies_pdf() {
        check_ajax_referer('export_supplies_pdf', 'nonce');

        $from_date = sanitize_text_field($_POST['from_date']);
        $to_date = sanitize_text_field($_POST['to_date']);
        $department = isset($_POST['department']) ? $_POST['department'] : '';

        // Query release supplies within date range
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'release_date',
                'value'   => array(date('Y-m-d', strtotime($from_date)), date('Y-m-d', strtotime($to_date))),
                'type'    => 'date',
                'compare' => 'BETWEEN'
            )
        );

        // If department is specified and not ALL (0), add department filter
        $author_id = null;
        if ($department) {
            // Check current user permissions
            $current_user = wp_get_current_user();
            $is_admin = current_user_can('manage_options');
            $is_accounting = in_array('um_accounting', $current_user->roles);
            
            if ($is_admin || $is_accounting) {
                // For admin or accounting, filter by department ID
                $users_id = ($department == 'ALL') ? '' : $this->departmentArr[$department];
                
                if (!empty($users_id)) {
                    $author_ids = $users_id;
                    
                    $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $author_ids);
                } else {
                    $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC');
                }
            } else {
                // For regular users, only show their own department data
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $current_user->ID);
            }
        } else {
            $current_user = wp_get_current_user();

            // If no department filter or ALL is selected
            if (current_user_can('manage_options') || in_array('um_accounting', wp_get_current_user()->roles)) {
                // Admin or accounting users can see all departments
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC');
            } else {
                // Regular users can only see their own data
                $query = $this->createQueryRoles('releasesupplies', $meta_query, -1, 'date', 'ASC', $current_user->ID);
            }
        }
        
        // Group and sum quantities by supply name
        $grouped_supplies = array();
        foreach ($query->posts as $post) {
            $supply_name = get_field('supply_name', $post->ID);
            
            // Skip if supply name is null or empty
            if (!$supply_name || empty($supply_name->post_title)) {
                continue;
            }
            
            $quantity = (float)get_field('quantity', $post->ID);
            $price_per_unit = (float)get_field('price_per_unit', $supply_name->ID);
            
            if (!isset($grouped_supplies[$supply_name->ID])) {
                $grouped_supplies[$supply_name->ID] = array(
                    'supply_name' => $supply_name->post_title,
                    'total_quantity' => 0,
                    'price_per_unit' => $price_per_unit
                );
            }
            $grouped_supplies[$supply_name->ID]['total_quantity'] += $quantity;
        }

        // Convert to array and sort by supply name
        $results = array_values($grouped_supplies);
        usort($results, function($a, $b) {
            return strcmp($a['supply_name'], $b['supply_name']);
        });

        // Generate PDF
        require_once(ABSPATH . 'wp-content/plugins/woocommerce/includes/libraries/tcpdf/tcpdf.php');
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Catamina Clinica');
        $pdf->SetTitle('Release Supplies Report');
        
        // Add department info to header if specified
        $header_subtitle = 'Period: ' . date('M d, Y', strtotime($from_date)) . ' - ' . date('M d, Y', strtotime($to_date));
        if ($department && $department != 'ALL' && $department != '0') {
            $department_name = array_search($department, $this->departmentArr) ?: 'Department: ' . $department;
            $header_subtitle .= ' | ' . $department_name;
        }
        
        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Release Supplies Report', $header_subtitle);
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Add table
        $html = '<table border="1" cellpadding="4">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="width: 50%;"><b>Equipment / Supply Name</b></th>
                    <th style="width: 20%; text-align: right;"><b>Quantity</b></th>
                    <th style="width: 15%; text-align: right;"><b>Price per Unit</b></th>
                    <th style="width: 15%; text-align: right;"><b>Total Price</b></th>
                </tr>
            </thead>
            <tbody>';
        
        $grand_total = 0;
        foreach ($results as $item) {
            $total_price = $item['total_quantity'] * $item['price_per_unit'];
            $grand_total += $total_price;
            
            $html .= '<tr>
                <td>' . $item['supply_name'] . '</td>
                <td style="text-align: right;">' . number_format($item['total_quantity'], 2) . '</td>
                <td style="text-align: right;">PHP ' . number_format($item['price_per_unit'], 2) . '</td>
                <td style="text-align: right;">PHP ' . number_format($total_price, 2) . '</td>
            </tr>';
        }
        
        // Add total row
        $html .= '<tr style="background-color: #f8f9fa;">
            <td colspan="3" style="text-align: right;"><b>Total Amount:</b></td>
            <td style="text-align: right;"><b>PHP ' . number_format($grand_total, 2) . '</b></td>
        </tr>';
        
        $html .= '</tbody></table>';
        
        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        $pdf->Output('release_supplies_report_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }

    public function my_acf_fields_post_object_query_supply_name($args, $field, $post_id) {
        
        $u = wp_get_current_user();
        $args['author'] = $u->ID;

        return $args;
    }

    public function createQuery($posttype, $meta_query = array(), $numberposts = -1, $orderby = 'date', $order = 'DESC', $aid = false) {
        $args = array(
            'orderby'			=> $orderby,
            'order'				=> $order,
            'numberposts'	=> $numberposts,
            'post_type'		=> $posttype,
            'meta_query'    => array($meta_query),
            'posts_per_page' => $numberposts,
            'post_status'    => 'publish'
        );

        $the_query = new WP_Query( $args );

        return $the_query;
    }

    public function createQueryRoles($posttype, $meta_query = array(), $numberposts = -1, $orderby = 'date', $order = 'DESC', $aid = false) {
        $args = array(
            'orderby'			=> $orderby,
            'order'				=> $order,
            'numberposts'	=> $numberposts,
            'post_type'		=> $posttype,
            'meta_query'    => array($meta_query),
            'posts_per_page' => $numberposts,
            'post_status'    => 'publish'
        );

        $u = wp_get_current_user();
        $roles = ( array ) $u->roles;

        if(!current_user_can( 'manage_options' ) && !($roles[0] == "um_accounting")):
            if($aid):
                $args['author'] = $aid;
            else:
                $args['author'] = $u->ID;
            endif;
        else:
            if($aid):
                $args['author'] = $aid;
            endif;
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
        $dept = ($_POST['dept'])?$_POST['dept']:false;
        $aid = ($_POST['author'])?$_POST['author']:false;
        wp_send_json_success($this->getReconciliationReportBatch($from, $to, $dept, $aid));
    }

    public function load_incomeexpense_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));
        $incomecat = ($_POST['incomecat'])?$_POST['incomecat']:'all';
        $expensecat = ($_POST['expensecat'])?$_POST['expensecat']:'all';
        wp_send_json_success($this->getIncomeExpensesReport($from, $to, $incomecat, $expensecat));
    }

    public function load_release_data() {
        $sup = $_POST['sup'];
        $deets = array(
            'purchased_date' => get_field('purchased_date', $sup),
            'section' => get_field('section', $sup)
        );
        wp_send_json_success($deets);
    }

    public function batch_process_supplies() {
        $batchData = (array)$_POST['batchData'];
        $to = $_POST['to'];
        $suppdept = array();

        foreach($batchData as $suppid => $supp):
            $price = (float)get_field('price_per_unit', $suppid);
            $curqty = $this->getQtyOfSupplyAfterDate($suppid, $to, true);
            $dept = get_field('department', $suppid);
            $deptslug = strtolower(str_replace(" ", "_", $dept));
            $stype = strtolower(str_replace(" ", "_", get_field('type', $suppid)));
            $suppdept[$deptslug][$stype][$suppid] = array(($price * $curqty[0]), ($curqty[1] * $price));
        endforeach;

        wp_send_json_success($suppdept);
    }

    public function append_to_csv($filename, $data) {
        $file_exists = file_exists($filename);
        $file = fopen($filename, 'a');

        if (!$file) {
            throw new Exception("Unable to open file: $filename");
        }

        if (!$file_exists) {
            fputcsv($file, array('ID', 'department', 'quantity', 'price', 'total price', 'duplicate'));
        }

        $is_duplicate = false;
        if ($file_exists) {
            $existing_data = array_map('str_getcsv', file($filename));
            foreach ($existing_data as $row) {
                if ($row[0] == $data['ID']) {
                    $is_duplicate = true;
                    break;
                }
            }
        }

        $data['duplicate'] = $is_duplicate ? 'true' : '';
        fputcsv($file, $data);
        fclose($file);
    }

    public function load_goods_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getGoodsReport($from, $to));

    }

    public function load_soc_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));
        $supd = (array)$_POST['suppdata'];

        wp_send_json_success($this->getSOCReport($from, $to, $supd));

    }

    public function load_financial_report() {
        $from = date('Y-m-d', strtotime($_POST['fromdate']));
        $to = date('Y-m-d', strtotime($_POST['todate']));

        wp_send_json_success($this->getFinancialReport($from, $to));

    }

    public function getQtyOfSupplyAfterDate($supid, $date, $expired = false) {
        // Static cache improves performance for repeated calls
        static $cache = [];
        $cache_key = $supid . '_' . $date . '_' . ($expired ? '1' : '0');
        
        // Check if result already exists in cache
        if (isset($cache[$cache_key])) {
            return $cache[$cache_key];
        }
        
        // Format date once
        $formatted_date = date('Y-m-d', strtotime($date));
        
        global $wpdb;
        
        // Use try/catch to handle potential database errors
        try {
            // Get the sum of quantities directly via SQL for actual supplies
            // Use more specific column selection instead of relying on aliases
            $actual_supplies_query = $wpdb->prepare(
                "SELECT COALESCE(SUM(pm_quantity.meta_value), 0) as total_quantity
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm_supply ON p.ID = pm_supply.post_id AND pm_supply.meta_key = %s
                INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = %s
                INNER JOIN {$wpdb->postmeta} pm_quantity ON p.ID = pm_quantity.post_id AND pm_quantity.meta_key = %s
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND pm_supply.meta_value = %d
                AND pm_date.meta_value <= %s",
                'supply_name',
                'date_added',
                'quantity',
                'actualsupplies',
                'publish',
                $supid,
                $formatted_date
            );
            
            // Use proper error checking on query execution
            $actual_quantity = (float)$wpdb->get_var($actual_supplies_query);
            if ($wpdb->last_error) {
                error_log('Database error in getQtyOfSupplyAfterDate (actual supplies): ' . $wpdb->last_error);
                $actual_quantity = 0;
            }
            
            // Get the sum of quantities directly via SQL for release supplies with similar optimizations
            $release_supplies_query = $wpdb->prepare(
                "SELECT COALESCE(SUM(pm_quantity.meta_value), 0) as total_quantity
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm_supply ON p.ID = pm_supply.post_id AND pm_supply.meta_key = %s
                INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = %s
                INNER JOIN {$wpdb->postmeta} pm_quantity ON p.ID = pm_quantity.post_id AND pm_quantity.meta_key = %s
                WHERE p.post_type = %s
                AND p.post_status = %s
                AND pm_supply.meta_value = %d
                AND pm_date.meta_value <= %s",
                'supply_name',
                'release_date',
                'quantity',
                'releasesupplies',
                'publish',
                $supid,
                $formatted_date
            );
            
            $release_quantity = (float)$wpdb->get_var($release_supplies_query);
            if ($wpdb->last_error) {
                error_log('Database error in getQtyOfSupplyAfterDate (release supplies): ' . $wpdb->last_error);
                $release_quantity = 0;
            }
            
            // Calculate remaining quantity
            $remaining_quantity = $actual_quantity - $release_quantity;
            
            // If expired information is needed, get the expired quantity
            if ($expired) {
                $expired_query = $wpdb->prepare(
                    "SELECT COALESCE(SUM(pm_quantity.meta_value), 0) as expired_quantity
                    FROM {$wpdb->posts} p
                    INNER JOIN {$wpdb->postmeta} pm_supply ON p.ID = pm_supply.post_id AND pm_supply.meta_key = %s
                    INNER JOIN {$wpdb->postmeta} pm_date ON p.ID = pm_date.post_id AND pm_date.meta_key = %s
                    INNER JOIN {$wpdb->postmeta} pm_quantity ON p.ID = pm_quantity.post_id AND pm_quantity.meta_key = %s
                    INNER JOIN {$wpdb->postmeta} pm_expiry ON p.ID = pm_expiry.post_id AND pm_expiry.meta_key = %s
                    WHERE p.post_type = %s
                    AND p.post_status = %s
                    AND pm_supply.meta_value = %d
                    AND pm_date.meta_value <= %s
                    AND pm_expiry.meta_value != %s
                    AND pm_expiry.meta_value <= %s",
                    'supply_name',
                    'date_added',
                    'quantity',
                    'expiry_date',
                    'actualsupplies',
                    'publish',
                    $supid,
                    $formatted_date,
                    '',
                    $formatted_date
                );
                
                $expired_quantity = (float)$wpdb->get_var($expired_query);
                if ($wpdb->last_error) {
                    error_log('Database error in getQtyOfSupplyAfterDate (expired): ' . $wpdb->last_error);
                    $expired_quantity = 0;
                }
                
                // Calculate expired quantity that hasn't been released
                $expired_remaining = max(0, $expired_quantity - $release_quantity);
                
                $result = array($remaining_quantity, $expired_remaining);
            } else {
                $result = $remaining_quantity;
            }
            
        } catch (Exception $e) {
            // Log any unexpected errors
            error_log('Error in getQtyOfSupplyAfterDate: ' . $e->getMessage());
            $result = $expired ? array(0, 0) : 0;
        }
        
        // Limit cache size to prevent memory issues (keep last 100 results)
        if (count($cache) > 100) {
            array_shift($cache); // Remove oldest entry
        }
        
        // Cache the result
        $cache[$cache_key] = $result;
        
        // Help garbage collector by nullifying large variables that are no longer needed
        unset($actual_supplies_query, $release_supplies_query, $expired_query);
        
        return $result;
    }

    public function getReconciliationReport($from, $to, $dept = false, $aid = false) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Reconciliation Report</h3>";

        if(!is_bool($dept) && ($dept !== 'false')):
            $meta_query = array(
                'key'     => 'department',
                'value'   =>  $dept,
                'compare' =>  '='   
            );

            $addquery = $this->createQuery('supplies', $meta_query);
        else:
            $addquery = $this->createQuery('supplies');
        endif;

        $filename = 'getReconciliationReport - '.$to.'.csv'; // Specify your CSV file name


        foreach($addquery->posts as $p):
            $name[$p->ID] = get_field('supply_name', $p->ID);
            $supplyid = $p->ID;
            $dept = get_field('department', $p->ID);
            $deptslug = str_replace(" ", "_", strtolower($dept));
            $type = get_field('type', $p->ID);
            $typeslug = strtolower($type);
            $curqty = $this->getQtyOfSupplyAfterDate($supplyid, $to);

            if($type == "Adjustment"):
                continue;
            endif;

            $overallupplies[$deptslug][$typeslug][$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'department' => $dept,
                'type' => $type,
                'quantity' => $curqty
            );

        endforeach;

        ksort($overallupplies); // sort department

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $addquery = $this->createQuery('actualsupplies', $meta_query, -1, 'date', 'DESC', $aid);
        $datesupplies = array();
        $qty = array();

        foreach($addquery->posts as $p):
            $name[$p->ID] = get_field('supply_name', $p->ID);
            $supplyid = $name[$p->ID]->ID;
            $qty[$supplyid] = (isset($qty[$supplyid]))?(float)$qty[$supplyid] + (float)get_field('quantity', $p->ID):get_field('quantity', $p->ID);
            

            $datesupplies[$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'quantity' => $qty[$supplyid],
                'serial' => (!empty(get_field('serial', $p->ID)))?get_field('serial', $p->ID):false,
                'states__status' => (!empty(get_field('states__status', $p->ID)))?get_field('states__status', $p->ID):false,
                'lot_number' => (!empty(get_field('lot_number', $p->ID)))?get_field('lot_number', $p->ID):false,
                'expiry_date' => (!empty(get_field('expiry_date', $p->ID)))?get_field('expiry_date', $p->ID):false
            );
        endforeach;

        $meta_query = array(
            'key'     => 'release_date',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        $addquery = $this->createQuery('releasesupplies', $meta_query, -1, 'date', 'DESC', $aid);
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

        $sectionlist = array();
        $subsectionlist = array();

        foreach($overallupplies as $department => $types):
            ksort($types);
            $res .= "<h1>".str_replace("_", " ", strtoupper($department))."</h1>";
            
            foreach($types as $type => $suppdetails):
                $typetext = strtoupper($type);
                $res .= '<div class="report__result-header">'.$typetext.'</div>';
                $res .= "<table>";

                if(strtoupper($type) == "EQUIPMENT"):
                    $res .= "<thead>";
                    $res .= "<tr>";
                    $res .= "<th>EQUIPMENT</th>";
                    $res .= "<th class='filter-serial'>SERIAL</th>";
                    $res .= "<th class='filter-states'>STATES</th>";
                    $res .= "<th class='filter-beg'>BEG INV</th>";
                    $res .= "<th class='filter-purchase'>PURCHASES</th>";
                    $res .= "<th class='filter-total'>TOTAL</th>";
                    $res .= "<th class='filter-cons'>CONSUMPTION</th>";
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
                    $res .= "<th class='filter-lot'>LOT #</th>";
                    $res .= "<th class='filter-exp'>EXP. DATE</th>";
                    $res .= "<th class='filter-beg'>BEG INV</th>";
                    $res .= "<th class='filter-purchase'>PURCHASES</th>";
                    $res .= "<th class='filter-total'>TOTAL</th>";
                    $res .= "<th class='filter-cons'>CONSUMPTION</th>";
                    $res .= "<th>END INV</th>";
                    $res .= "<th>PRICE</th>";
                    $res .= "<th>ACTUAL COUNT</th>";
                    $res .= "<th>VARIANCE</th>";
                    $res .= "<th>TOTAL</th>";
                    $res .= "</tr>";
                    $res .= "</thead>";
                endif;

                foreach($suppdetails as $suppid => $suppdeets):
                    $section = get_field('section', $suppid);
                    $subsection = ($section == "Ambulatory Surgery Center (ASC)")?get_field('sub_section', $suppid):'';

                    if(strtoupper($type) == "EQUIPMENT"):
                        $purchase = (isset($datesupplies[$suppid]['quantity']))?(float)$datesupplies[$suppid]['quantity']:0;
                        $release = (isset($relsupplies[$suppid]['quantity']))?(float)$relsupplies[$suppid]['quantity']:0;
                        $price = (float)get_field('price_per_unit', $suppid);

                        $totcount = (float)$suppdeets['quantity'] + $purchase + ((float)$suppdeets['quantity'] + $purchase) + $release;

                        if($totcount == 0):
                            continue;
                        endif;

                        $serial = (!empty($datesupplies[$suppid]['serial']))?$datesupplies[$suppid]['serial']:get_field('serial', $suppid);
                        $states = (!empty($datesupplies[$suppid]['states__status']))?$datesupplies[$suppid]['states__status']:get_field('states__status', $suppid);

                        $res .= "<tbody>";
                        $res .= "<tr data-section='".$section."' data-subsection='".$subsection."'>";
                        $res .= "<td>".$suppdeets['supply_name']."</td>";
                        $res .= "<td class='filter-serial'>".$serial."</td>";
                        $res .= "<td class='filter-states'>".$states."</td>";
                        $res .= "<td class='filter-beg'>".(float)$suppdeets['quantity']."</td>";
                        $res .= "<td class='filter-purchase'>".$purchase."</td>";
                        $res .= "<td class='filter-total'>".((float)$suppdeets['quantity'] + $purchase)."</td>";
                        $res .= "<td class='filter-cons'>".$release."</td>";
                        $res .= "<td class='orig-count' data-val='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'>".(((float)$suppdeets['quantity'] + $purchase) - $release)."</td>";
                        $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                        $res .= "<td class='row-actual-count'><input type='number' class='actual-field' value='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'></td>";
                        $res .= "<td class='row-variance'>0</td>";
                        $res .= "<td class='row-total'>&#8369 ".$this->convertNumber(((((float)$suppdeets['quantity'] + $purchase) - $release) * $price))."</td>";
                        $res .= "</tr>";
                        $res .= "</tbody>";
                    else:
                        $purchase = (isset($datesupplies[$suppid]['quantity']))?(float)$datesupplies[$suppid]['quantity']:0;
                        $release = (isset($relsupplies[$suppid]['quantity']))?(float)$relsupplies[$suppid]['quantity']:0;
                        $price = (float)get_field('price_per_unit', $suppid);

                        $totcount = (float)$suppdeets['quantity'] + $purchase + ((float)$suppdeets['quantity'] + $purchase) + $release;

                        if($totcount == 0):
                            continue;
                        endif;

                        $lot = (!empty($datesupplies[$suppid]['lot_number']))?$datesupplies[$suppid]['lot_number']:get_field('lot_number', $suppid);
                        $expiry = (!empty($datesupplies[$suppid]['expiry_date']))?$datesupplies[$suppid]['expiry_date']:get_field('expiry_date', $suppid);
                        
                        $res .= "<tbody>";
                        $res .= "<tr data-section='".$section."' data-subsection='".$subsection."'>";
                        $res .= "<td>".$suppdeets['supply_name']."</td>";
                        $res .= "<td class='filter-lot'>".$lot."</td>";
                        $res .= "<td class='filter-exp'>".$expiry."</td>";
                        $res .= "<td class='filter-beg'>".(float)$suppdeets['quantity']."</td>";
                        $res .= "<td class='filter-purchase'>".$purchase."</td>";
                        $res .= "<td class='filter-total'>".((float)$suppdeets['quantity'] + $purchase)."</td>";
                        $res .= "<td class='filter-cons'>".$release."</td>";
                        $res .= "<td class='orig-count' data-val='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'>".(((float)$suppdeets['quantity'] + $purchase) - $release)."</td>";
                        $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                        $res .= "<td class='row-actual-count'><input type='number' class='actual-field' value='".(((float)$suppdeets['quantity'] + $purchase) - $release)."'></td>";
                        $res .= "<td class='row-variance'>0</td>";
                        $res .= "<td class='row-total'>&#8369 ".$this->convertNumber(((((float)$suppdeets['quantity'] + $purchase) - $release) * $price))."</td>";
                        $res .= "</tr>";
                        $res .= "</tbody>";
                    endif;

                    if($section):
                        $sectionlist[str_replace(" ", "-", strtolower($section))] = $section;
                    endif;

                    if($subsection && ($subsection != '')):
                        $subsectionlist[str_replace(" ", "-", strtolower($subsection))] = $subsection;
                    endif;
                    
                endforeach;

                $res .= "</table>";

            endforeach;
        endforeach;

        $res .= "<select id='section-list'><option data-val='all'>Select Room Section</option><option>";
        $res .= implode("</option><option>", $sectionlist);
        $res .= "</option></select>";

        $res .= "<select id='subsection-list'><option data-val='all'>Select Sub Section</option><option>";
        $res .= implode("</option><option>", $subsectionlist);
        $res .= "</option></select>";

        $res .= "<div class='recon-total'><b>TOTAL:</b> <span></span></div>";

        return $res;
    }

    public function getReconciliationReportBatch($from, $to, $dept = false, $aid = false) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Reconciliation Report</h3>";

        if(!is_bool($dept) && ($dept !== 'false')):
            $meta_query = array(
                'key'     => 'department',
                'value'   =>  $dept,
                'compare' =>  '='  
            );

            $addquery = $this->createQuery('supplies', $meta_query);
        else:
            $addquery = $this->createQuery('supplies');
        endif;

        $arrfinal = array_column($addquery->posts, 'post_title', 'ID');
        $res .= '<div class="supplies-json-recon" style="display:none;">'.json_encode($arrfinal).'</div>';
        return $res;
    }

    public function render_recon_output() {
        try {
            // Set higher memory and execution time limits for large data processing
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes
            
            // Get raw input and validate it's proper JSON
            $reconarray = isset($_POST['suppdata']) ? $_POST['suppdata'] : null;
            if (empty($reconarray)) {
                wp_send_json_error('Missing supply data');
                return;
            }
            
            // Ensure we have the expected data structure
            if (!isset($reconarray['overallupplies']) || !isset($reconarray['datesupplies']) || !isset($reconarray['relsupplies'])) {
                wp_send_json_error('Invalid data structure');
                return;
            }
            
            $overallupplies = $reconarray['overallupplies'];
            $datesupplies = $reconarray['datesupplies'];
            $relsupplies = $reconarray['relsupplies'];
            
            // Validate date inputs
            $from = isset($_POST['fromdate']) ? sanitize_text_field($_POST['fromdate']) : '';
            $to = isset($_POST['todate']) ? sanitize_text_field($_POST['todate']) : '';
            
            if (empty($from) || empty($to)) {
                wp_send_json_error('Missing date range');
                return;
            }
            
            $sectionlist = array();
            $subsectionlist = array();
            
            $res = "";
            $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
            $res .= "<h3>Reconciliation Report</h3>";
            
            // Process departments in batches to avoid memory issues
            foreach($overallupplies as $department => $types) {
                // Sort types for consistent display
                ksort($types);
                $res .= "<h1>".str_replace("_", " ", strtoupper($department))."</h1>";
                
                foreach($types as $type => $suppdetails) {
                    $typetext = strtoupper($type);
                    $res .= '<div class="report__result-header">'.$typetext.'</div>';
                    $res .= "<table>";
                    
                    if(strtoupper($type) == "EQUIPMENT") {
                        $res .= "<thead><tr>
                            <th>EQUIPMENT</th>
                            <th class='filter-serial'>SERIAL</th>
                            <th class='filter-states'>STATES</th>
                            <th class='filter-beg'>BEG INV</th>
                            <th class='filter-purchase'>PURCHASES</th>
                            <th class='filter-total'>TOTAL</th>
                            <th class='filter-cons'>CONSUMPTION</th>
                            <th>END INV</th>
                            <th>PRICE</th>
                            <th>ACTUAL COUNT</th>
                            <th>VARIANCE</th>
                            <th>TOTAL</th>
                        </tr></thead>";
                    } else {
                        $res .= "<thead><tr>
                            <th>SUPPLY NAME</th>
                            <th class='filter-lot'>LOT #</th>
                            <th class='filter-exp'>EXP. DATE</th>
                            <th class='filter-beg'>BEG INV</th>
                            <th class='filter-purchase'>PURCHASES</th>
                            <th class='filter-total'>TOTAL</th>
                            <th class='filter-cons'>CONSUMPTION</th>
                            <th>END INV</th>
                            <th>PRICE</th>
                            <th>ACTUAL COUNT</th>
                            <th>VARIANCE</th>
                            <th>TOTAL</th>
                        </tr></thead>";
                    }
                    
                    $expSuppExpTotal = 0;
                    
                    // Process items in smaller batches
                    $itemCount = 0;
                    $batchSize = 100;
                    $suppliesBatch = array_chunk($suppdetails, $batchSize, true);
                    
                    foreach($suppliesBatch as $batchDetails) {
                        foreach($batchDetails as $suppid => $suppdeets) {
                            // Skip entries with invalid data
                            if (!isset($suppdeets['supply_name'])) {
                                continue;
                            }
                            
                            // Check if section exists
                            $section = get_field('section', $suppid);
                            if (!$section) $section = '';
                            
                            $subsection = ($section == "Ambulatory Surgery Center (ASC)") ? get_field('sub_section', $suppid) : '';
                            if (!$subsection) $subsection = '';
                            
                            if(strtoupper($type) == "EQUIPMENT") {
                                // Calculate equipment values
                                $purchase = (isset($datesupplies[$suppid]['quantity'])) ? (float)$datesupplies[$suppid]['quantity'] : 0;
                                $release = (isset($relsupplies[$suppid]['quantity'])) ? (float)$relsupplies[$suppid]['quantity'] : 0;
                                $price = (float)get_field('price_per_unit', $suppid);
                                
                                // Fetch serial and states data
                                $serial = (!empty($datesupplies[$suppid]['serial'])) ? $datesupplies[$suppid]['serial'] : get_field('serial', $suppid);
                                $states = (!empty($datesupplies[$suppid]['states__status'])) ? $datesupplies[$suppid]['states__status'] : get_field('states__status', $suppid);
                                
                                // Calculate values for display
                                $beginQuantity = isset($suppdeets['quantity']) ? (float)$suppdeets['quantity'] : 0;
                                $endInventory = (($beginQuantity + $purchase) - $release);
                                $totalValue = $endInventory * $price;
                                
                                $res .= "<tbody class='sup-container' data-name='".$suppdeets['supply_name']."'>";
                                $res .= "<tr data-section='".$section."' data-subsection='".$subsection."'>";
                                $res .= "<td>".esc_html($suppdeets['supply_name'])."</td>";
                                $res .= "<td class='filter-serial'>".esc_html($serial)."</td>";
                                $res .= "<td class='filter-states'>".esc_html($states)."</td>";
                                $res .= "<td class='filter-beg'>".$beginQuantity."</td>";
                                $res .= "<td class='filter-purchase'>".$purchase."</td>";
                                $res .= "<td class='filter-total'>".($beginQuantity + $purchase)."</td>";
                                $res .= "<td class='filter-cons'>".$release."</td>";
                                $res .= "<td class='orig-count' data-val='".$endInventory."'>".$endInventory."</td>";
                                $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                                $res .= "<td class='row-actual-count'><input type='number' class='actual-field' value='".$endInventory."'></td>";
                                $res .= "<td class='row-variance'>0</td>";
                                $res .= "<td class='row-total'>&#8369 ".$this->convertNumber($totalValue)."</td>";
                                $res .= "</tr>";
                                $res .= "</tbody>";
                            } else {
                                // Calculate supply values
                                $purchase = (isset($datesupplies[$suppid]['quantity'])) ? (float)$datesupplies[$suppid]['quantity'] : 0;
                                $release = (isset($relsupplies[$suppid]['quantity'])) ? (float)$relsupplies[$suppid]['quantity'] : 0;
                                $price = (float)get_field('price_per_unit', $suppid);
                                
                                $beginQuantity = isset($suppdeets['quantity']) ? (float)$suppdeets['quantity'] : 0;
                                $endInventory = (($beginQuantity + $purchase) - $release);
                                $suptots = $endInventory * $price;
                                
                                $lot = (!empty($datesupplies[$suppid]['lot_number'])) ? $datesupplies[$suppid]['lot_number'] : '';
                                $expiry = (!empty($datesupplies[$suppid]['expiry_date'])) ? $datesupplies[$suppid]['expiry_date'] : $this->getLastExpDate($suppid, $beginQuantity, $to);
                                
                                // Handle expired quantities
                                $expQtyAmount = isset($suppdeets['expired_qty']) ? (float)$suppdeets['expired_qty'] : 0;
                                $expQtyAmountHTML = ($expQtyAmount && ($expQtyAmount > 0)) ? "<span class='red-warning'>(" . $expQtyAmount . ")</span>" : "";
                                $expNameHTMLClass = ($expQtyAmount && ($expQtyAmount > 0)) ? "red-warning" : "";
                                
                                $expSuppExpTotal += ($expQtyAmount * $price);
                                
                                $res .= "<tbody class='sup-container count-supplies' data-name='".esc_attr($suppdeets['supply_name'])."'>";
                                $res .= "<tr data-section='".$section."' data-subsection='".$subsection."'>";
                                $res .= "<td class='".$expNameHTMLClass."'>".esc_html($suppdeets['supply_name'])."</td>";
                                $res .= "<td class='filter-lot'>".esc_html($lot)."</td>";
                                $res .= "<td class='filter-exp'>".esc_html($expiry)."</td>";
                                $res .= "<td class='filter-beg'>".$beginQuantity."</td>";
                                $res .= "<td class='filter-purchase'>".$purchase."</td>";
                                $res .= "<td class='filter-total'>".($beginQuantity + $purchase)."</td>";
                                $res .= "<td class='filter-cons'>".$release."</td>";
                                $res .= "<td class='orig-count' data-val='".$endInventory."'>".$endInventory." ".$expQtyAmountHTML."</td>";
                                $res .= "<td class='row-price' data-val='".$price."'>&#8369 ".$this->convertNumber($price)."</td>";
                                $res .= "<td class='row-actual-count'><input type='number' class='actual-field' value='".$endInventory."'></td>";
                                $res .= "<td class='row-variance'>0</td>";
                                $res .= "<td class='row-total'>&#8369 ".$this->convertNumber($suptots)."</td>";
                                $res .= "</tr>";
                                $res .= "</tbody>";
                            }
                            
                            // Build section and subsection lists for filters
                            if($section) {
                                $sectionlist[str_replace(" ", "-", strtolower($section))] = $section;
                            }
                            
                            if($subsection && ($subsection != '')) {
                                $subsectionlist[str_replace(" ", "-", strtolower($subsection))] = $subsection;
                            }
                            
                            // Release memory periodically
                            if ($itemCount % 100 === 0) {
                                gc_collect_cycles();
                            }
                            $itemCount++;
                        }
                    }
                    
                    $res .= "</table>";
                }
                
                // Release memory after processing each department
                gc_collect_cycles();
            }
            
            // Build filter dropdowns
            $res .= "<select id='section-list'><option data-val='all'>Select Room Section</option>";
            if (!empty($sectionlist)) {
                $res .= "<option>" . implode("</option><option>", $sectionlist) . "</option>";
            }
            $res .= "</select>";
            
            $res .= "<select id='subsection-list'><option data-val='all'>Select Sub Section</option>";
            if (!empty($subsectionlist)) {
                $res .= "<option>" . implode("</option><option>", $subsectionlist) . "</option>";
            }
            $res .= "</select>";
            
            // Add summary information
            $res .= "<div class='sup-total'><b>SUPPLIES TOTAL:</b> <span></span></div>";
            $res .= "<div class='sup-loss'><b>SUPPLIES (LOSS):</b> <span data-val='".$expSuppExpTotal."'>₱ ".$this->convertNumber($expSuppExpTotal)."</span></div>";
            $res .= "<div class='recon-total'><b>OVERALL TOTAL:</b> <span></span></div>";
            
            // Clear all local variables to help with memory management
            unset($overallupplies, $datesupplies, $relsupplies, $sectionlist, $subsectionlist);
            gc_collect_cycles();
            
            wp_send_json_success($res);
        } catch (Exception $e) {
            // Log the error and return a helpful message
            error_log('Error in render_recon_output: ' . $e->getMessage());
            wp_send_json_error('An error occurred while generating the report: ' . $e->getMessage());
        }
    }

    public function getLastExpDate($suppid, $quantity, $date) {
        if((int)$quantity == 0) return '';

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'date_added',
                'value'   => date('Y-m-d', strtotime($date)),
                'type'    => 'date',
                'compare' => '<='
            ),
            array(
                'key'   => 'supply_name',
                'value' => $suppid
            )
        );

        $addquery = $this->createQuery('actualsupplies', $meta_query, -1, 'date', 'ASC');
        $expd = array();

        foreach($addquery->posts as $p):
            $expfield = get_field('expiry_date', $p->ID);
            if(!empty($expfield) && trim($expfield) !== ''):
                $expd[] = $expfield;  
            endif;
        endforeach;

        $cnt = count($expd);
        return ($cnt > 0) ? $expd[$cnt-1] : '';
    }

    public function batch_process_supplies_recon() {
        $batchData = (array)$_POST['batchData'];
        $to = $_POST['to'];
        $from = $_POST['from'];
        $reconarray = array();
        
        // Prepare data structures
        $overallupplies = [];
        $datesupplies = [];
        $relsupplies = [];
        
        // Format dates once to avoid repetitive conversion
        $formatted_from = date('Y-m-d', strtotime($from));
        $formatted_to = date('Y-m-d', strtotime($to));
        
        // Get all supplies and actual supplies in batch for the date range
        $supply_ids = array_keys($batchData);
        
        if (empty($supply_ids)) {
            wp_send_json_success([
                'overallupplies' => [],
                'datesupplies' => [],
                'relsupplies' => []
            ]);
            return;
        }
        
        // Batch process all supplies
        foreach ($supply_ids as $suppid) {
            $name[$suppid] = get_field('supply_name', $suppid);
            $dept = get_field('department', $suppid);
            $type = get_field('type', $suppid);
            
            // Skip adjustment type
            if ($type == "Adjustment") {
                continue;
            }
            
            $deptslug = str_replace(" ", "_", strtolower($dept));
            $typeslug = strtolower($type);
            
            // Get current quantities - this is the bottleneck function that we optimized
            $curqty = $this->getQtyOfSupplyAfterDate($suppid, $from);
            $price = (float)get_field('price_per_unit', $suppid);
            $expQtySup = $this->getQtyOfSupplyAfterDate($suppid, $to, true);
            
            // Store in overall supplies
            if (!isset($overallupplies[$deptslug])) {
                $overallupplies[$deptslug] = [];
            }
            
            if (!isset($overallupplies[$deptslug][$typeslug])) {
                $overallupplies[$deptslug][$typeslug] = [];
            }
            
            $overallupplies[$deptslug][$typeslug][$suppid] = array(
                'supply_name' => get_field('supply_name', $suppid),
                'department' => $dept,
                'type' => $type,
                'quantity' => $curqty,
                'expired_qty' => $expQtySup[1]
            );
        }
        
        // Create optimized meta query for actual supplies
        $meta_query = array(
            'key'     => 'date_added',
            'value'   => array($formatted_from, $formatted_to),
            'type'    => 'date',
            'compare' => 'between'
        );
        
        // Batch fetch all actual supplies in the date range
        $all_actual_supplies = $this->createQuery('actualsupplies', [
            'meta_query' => array(
                'relation' => 'AND',
                $meta_query,
                array(
                    'key'     => 'supply_name',
                    'value'   => $supply_ids,
                    'compare' => 'IN'
                )
            )
        ], -1, 'date', 'ASC');
        
        // Process all actual supplies
        $qty = [];
        $lotn = [];
        
        foreach ($all_actual_supplies->posts as $p) {
            $supply_obj = get_field('supply_name', $p->ID);
            if (!$supply_obj) continue;
            
            $supplyid = $supply_obj->ID;
            
            // Skip if not in our batch data
            if (!in_array($supplyid, $supply_ids)) continue;
            
            // Sum quantities
            $qty[$supplyid] = isset($qty[$supplyid]) ? 
                (float)$qty[$supplyid] + (float)get_field('quantity', $p->ID) : 
                (float)get_field('quantity', $p->ID);
            
            // Collect lot numbers
            $lot_number = get_field('lot_number', $p->ID);
            if ($lot_number) {
                if (!isset($lotn[$supplyid])) {
                    $lotn[$supplyid] = [];
                }
                $lotn[$supplyid][] = $lot_number;
            }
            
            // Prepare date supplies data
            $datesupplies[$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'quantity' => $qty[$supplyid],
                'serial' => (!empty(get_field('serial', $p->ID))) ? get_field('serial', $p->ID) : false,
                'states__status' => (!empty(get_field('states__status', $p->ID))) ? get_field('states__status', $p->ID) : false,
                'lot_number' => (isset($lotn[$supplyid])) ? implode(',', array_unique($lotn[$supplyid])) : '',
                'expiry_date' => (!empty(get_field('expiry_date', $p->ID))) ? get_field('expiry_date', $p->ID) : ''
            );
        }
        
        // Create optimized meta query for release supplies
        $meta_query = array(
            'key'     => 'release_date',
            'value'   => array($formatted_from, $formatted_to),
            'type'    => 'date',
            'compare' => 'between'
        );
        
        // Batch fetch all release supplies in the date range
        $all_release_supplies = $this->createQuery('releasesupplies', [
            'meta_query' => array(
                'relation' => 'AND',
                $meta_query,
                array(
                    'key'     => 'supply_name',
                    'value'   => $supply_ids,
                    'compare' => 'IN'
                )
            )
        ], -1, 'date', 'ASC');
        
        // Process all release supplies
        $qty = [];
        
        foreach ($all_release_supplies->posts as $p) {
            $supply_obj = get_field('supply_name', $p->ID);
            if (!$supply_obj) continue;
            
            $supplyid = $supply_obj->ID;
            
            // Skip if not in our batch data
            if (!in_array($supplyid, $supply_ids)) continue;
            
            // Sum quantities
            $qty[$supplyid] = isset($qty[$supplyid]) ? 
                (float)$qty[$supplyid] + (float)get_field('quantity', $p->ID) : 
                (float)get_field('quantity', $p->ID);
            
            // Prepare release supplies data
            $relsupplies[$supplyid] = array(
                'supply_name' => get_field('supply_name', $supplyid),
                'quantity' => $qty[$supplyid]
            );
        }
        
        // Prepare final result
        $reconarray = array(
            'overallupplies' => $overallupplies,
            'datesupplies' => $datesupplies,
            'relsupplies' => $relsupplies
        );
        
        wp_send_json_success($reconarray);
    }

    public function getPricesOfProductsBeforeDate($date) {

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

    public function getSOCReport($from, $to, $supparr = false) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Statement of Condition</h3>";

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  date('Y-m-d', strtotime($to)),
            'type'      =>  'date',
            'compare' =>  '<='   
        );

        $query = $this->createQuery('cashcheques', $meta_query, -1, 'meta_value', 'ASC');
        $cashonhand = array();
        $banks = array();
        $totalcandb = 0;
        $totalexclu = 0;

        foreach($query->posts as $k => $p):
            $bank = get_field('name_of_bank', $p->ID);

            if(get_field('type_of_account', $bank->ID) == "Cash on Hand"):
                $cashonhand[0] = (float)get_field('amount', $p->ID);
            else:
                $banks[$bank->ID] = array(
                    'name_of_bank' => get_field('name_of_bank', $bank->ID),
                    'account_number' => get_field('account_number', $bank->ID),
                    'type_of_account' => get_field('type_of_account', $bank->ID),
                    'amount' => get_field('amount', $p->ID)
                );
            endif;

        endforeach;


        $res .= "<h1>ASSETS</h1>";
        $res .= '<div class="report__result-header">Current Assets</div>';

        $res .= "<table>";
        $res .= "<tbody>";
        $res .= "<tr>";
        $res .= "<td>Cash on hand</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($cashonhand[0])."</td>";
        $res .= "</tr>";

        $temparr = array();

        foreach($banks as $id => $b):
            $exclu = array('Accounts Receivable', 'Accrued Interest Receivable');
            $bankName = trim($b['name_of_bank']);
            $found = false;

            foreach ($exclu as $word) {
                if (strpos($bankName, $word) !== false) {
                    $found = true;
                    break;
                }
            }

            if(!$found):
                $res .= "<tr>";
                $res .= "<td>".$b['name_of_bank']."</td>";
                $res .= "<td>&#8369 ".$this->convertNumber($b['amount'])."</td>";
                $res .= "</tr>";
                $totalcandb += (float)$b['amount'];
            else:
                $temparr[] = array(
                    'title' => $b['name_of_bank'],
                    'amount' => $this->convertNumber($b['amount'])
                );
                $totalexclu += (float)$b['amount'];
            endif;
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>Total Cash on Hand / In Banks:</span> &#8369 '.$this->convertNumber(($totalcandb + $cashonhand[0])).'</div>';

        $res .= "<table>";
        $res .= "<tbody>";

        foreach($temparr as $i => $e):
            $res .= "<tr>";
            $res .= "<td>".$e['title']."</td>";
            $res .= "<td>&#8369 ".$e['amount']."</td>";
            $res .= "</tr>";
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>Total Accounts Receivable:</span> &#8369 '.$this->convertNumber($totalexclu).'</div>';

        if(!$supparr):
            $meta_query = array(
                'key'     => 'type',
                'value'   => array('Equipment', 'Adjustment'),
                'compare' => 'NOT IN'
            );
    
            $supplies = $this->createQuery('supplies', $meta_query);
            $totsup = 0;
            $suppidlist = array();
            $suppdept = array();
            $arrfinal = array_column($supplies->posts, 'post_title', 'ID');
            $res .= '<div class="supplies-json" style="display:none;">'.json_encode($arrfinal).'</div>';
        endif;

        if($supparr):
            $res .= "<table>";
            $res .= "<tbody>";
            $totsup = 0;
            $totsupexp = 0;

            foreach($supparr as $d => $t):
                $res .= "<tr style='background: #8e8e8e;'>";
                $res .= "<td style='color: #fff;font-weight: 700;'>".strtoupper(str_replace("_", " ", $d))."</td>";
                $res .= "<td>AMOUNT</td>";
                $res .= "<td>EXPIRED (LOSS)</td>";
                $res .= "<td>FINAL AMOUNT</td>";
                $res .= "</tr>";

                foreach($t as $typetitle => $v):
                    $vnumfinal = 0;
                    $expvnumfinal = 0;

                    foreach($v as $vnum):
                        $vnumfinal += (float)$vnum[0];
                        $expvnumfinal += (float)$vnum[1];
                    endforeach;

                    $totsup += $vnumfinal;
                    $totsupexp += $expvnumfinal;

                    $res .= "<tr>";
                    $res .= "<td>".strtoupper(str_replace("_", " ", $typetitle))."</td>";
                    $res .= "<td>&#8369 ".$this->convertNumber($vnumfinal)."</td>";
                    $res .= "<td>&#8369 ".$this->convertNumber($expvnumfinal)."</td>";
                    $res .= "<td>&#8369 ".$this->convertNumber($vnumfinal - $expvnumfinal)."</td>";
                    $res .= "</tr>";
                endforeach;
            endforeach;

            $res .= "</tbody>";
            $res .= "</table>";

            $res .= '<div class="report__result-total"><span>Total Supplies - Inventory:</span> &#8369 '.$this->convertNumber($totsup).'</div>';
            $res .= '<div class="report__result-total"><span>Total Supplies (LOSS):</span> (&#8369 '.$this->convertNumber($totsupexp).')</div>';
            $res .= '<div class="report__result-total"><span>Total Current Assets:</span> &#8369 '.$this->convertNumber(($totsup - $totsupexp) + $totalcandb + $cashonhand[0] + $totalexclu).'</div>';
        endif;

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
            $res .= "<td>".$dep['description']." - (net of depreciation)</td>";
            $res .= "<td>&#8369 ".$this->convertNumber($thisval)."</td>";
            $res .= "</tr>";

            $overalllval += $thisval;
        endforeach;

        $res .= "</tbody>";
        $res .= "</table>";

        $res .= '<div class="report__result-total"><span>Total Fixed Assets:</span> &#8369 '.$this->convertNumber($overalllval).'</div>';
        $res .= '<div class="report__result-total"><span>Total Assets:</span> &#8369 '.$this->convertNumber($overalllval + ($totsup - $totsupexp) + $totalcandb + $cashonhand[0] + $totalexclu).'</div>';

        $res .= '<div class="report__result-header">LIABILITIES</div>';
        $res .= '<div class="report__result-header">Current Liabilities</div>';

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  date('Y-m-d', strtotime($to)),
            'type'      =>  'date',
            'compare' =>  '<='   
        );

        $query = $this->createQuery('liabilities', $meta_query);
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

        $query = $this->createQuery('retainedearnings', $meta_query, -1, 'date', 'ASC');
        $rettot = 0;

        foreach($query->posts as $ret):
            $rettot = (float)get_field('retained_earnings', $ret->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Retained Earnings/Undivided Profits:</span> &#8369 '.$this->convertNumber($rettot).'</div>';

        $meta_query = array(
            array(
                'key'     => 'date_added',
                'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
                'type'      =>  'date',
                'compare' =>  'between'  
            )
        );

        $query = $this->createQuery('beforeincometax', $meta_query, -1, 'date', 'ASC');
        $beforetax = 0;

        foreach($query->posts as $tax):
            $beforetax += (float)get_field('pre-tax_income_amount', $tax->ID);
        endforeach;

        $incexp = $this->getIncomeExpensesTotalNet($from, $to) - $beforetax;

        $res .= '<div class="report__result-total"><span>Before Income Tax:</span> (&#8369 '.$this->convertNumber($beforetax).')</div>';

        $res .= '<div class="report__result-total"><span>Net Profit/Income ('.date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to)).'):</span> &#8369 '.$this->convertNumber($incexp).'</div>';

        $res .= '<div class="report__result-total">&#8369 '.$this->convertNumber(($rettot + $incexp)).'</div>';

        $query = $this->createQuery('declareddividends', $meta_query, -1, 'date', 'ASC');
        $dectot = 0;

        foreach($query->posts as $dec):
            $dectot = (float)get_field('declared_dividends', $dec->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Dividends Declared:</span> (&#8369 '.$this->convertNumber($dectot).')</div>';


        $res .= '<div class="report__result-total">&#8369 '.$this->convertNumber(($rettot - $dectot) + ($incexp)).'</div>';

        $query = $this->createQuery('unrecordedcredits', $meta_query, -1, 'date', 'ASC');
        $uncredits = 0;

        foreach($query->posts as $cre):
            $uncredits += (float)get_field('credit_amount', $cre->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Unrecorded Credits:</span> &#8369 '.$this->convertNumber($uncredits).'</div>';

        $query = $this->createQuery('unrecordeddebits', $meta_query, -1, 'date', 'ASC');
        $undebits = 0;

        foreach($query->posts as $deb):
            $undebits += (float)get_field('debit_amount', $deb->ID);
        endforeach;

        $res .= '<div class="report__result-total"><span>Unrecorded Debits:</span> (&#8369 '.$this->convertNumber($undebits).')</div>';
        
        $networth = (($captot + ($rettot - $dectot) + ($incexp)) + $uncredits) - $undebits;

        $res .= '<div class="report__result-total"><span>Total Networth:</span> &#8369 '.$this->convertNumber($networth).'</div>';
        $res .= '<div class="report__result-total" style="margin-bottom: 50px;"><span>Total Liabilities and Networth:</span> &#8369 '.$this->convertNumber(($payaccttot + $paysupptot) + $networth).'</div>';

        return $res;
    }

    public function getFinancialReport($from, $to) {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Financial Report</h3>";
        $res .= "<h1>CASH ON HAND</h1>";
        $res .= "<table>";
        $res .= "<thead>";
        $res .= "<th>Cash on Hand</th>";
        $res .= "<th>Amount</th>";
        $res .= "</thead>";
        $res .= "<tbody>";

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'
        );

        $query = $this->createQuery('cashcheques', $meta_query, -1, 'meta_value', 'ASC');
        $cashonhand = array();
        $banks = array();
        $totalcandb = 0;

        foreach($query->posts as $k => $p):
            $bank = get_field('name_of_bank', $p->ID);

            if(get_field('type_of_account', $bank->ID) == "Cash on Hand"):
                $cashonhand[0] = (float)get_field('amount', $p->ID);
            else:
                $banks[$bank->ID] = array(
                    'name_of_bank' => get_field('name_of_bank', $bank->ID),
                    'account_number' => get_field('account_number', $bank->ID),
                    'type_of_account' => get_field('type_of_account', $bank->ID),
                    'amount' => get_field('amount', $p->ID)
                );
            endif;
        endforeach;

        $res .= "<td>Cash on Hand</td>";
        $res .= "<td>&#8369 ".$this->convertNumber($cashonhand[0])."</td>";
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

            $totalcandb += (float)$b['amount'];
        endforeach;
        $res .= "</tbody>";
        $res .= "</table>";
        $res .= '<div class="report__result-total"><span>TOTAL CASH ON HAND AND IN BANKS:</span> &#8369 '.$this->convertNumber(($totalcandb + $cashonhand[0])).'</div>';

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


    public function getIncomeExpensesReport($from, $to, $incomecat = 'all', $expensecat = 'all') {
        $res = "";
        $res .= "<h2>AS OF ".date('M d, Y', strtotime($from))." - ".date('M d, Y', strtotime($to))."</h2>";
        $res .= "<h3>Statement of Income & Expense</h3>";

        $meta_query = array(
            'key'     => 'date_added',
            'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
            'type'      =>  'date',
            'compare' =>  'between'   
        );

        if($incomecat != 'all' || $expensecat != 'all'):
            $temp_meta = $meta_query;
            $meta_query = array();
            $cats = array();
            $meta_query[] = $temp_meta;

            if($incomecat != 'all'):
                $cats[] = array(
                    'key'     => 'income_category',
                    'value'   =>  $incomecat,
                    'compare' =>  '='   
                );
            else:
                $cats[] = array(
                    'key'     => 'income_category',
                    'value'   =>  array('Charges','ECG','Goods','Laboratory Fees','OR Fees','Others/Miscellaneous','Pharma Meds/Supplies','Professional Fees','PHIC (ACPN)','Physical Therapist','Ultrasound','X-ray Fees'),
                    'compare' =>  'IN'   
                );
            endif;

            if($expensecat != 'all'):
                $cats[] = array(
                    'key'     => 'expense_category',
                    'value'   =>  $expensecat,
                    'compare' =>  '='   
                );
            else:
                $cats[] = array(
                    'key'     => 'expense_category',
                    'value'   =>  array('Advertisement','Donations & Contribution','ECG Reading Fee','Freight, Handling & Delivery','Fuel and Oil','Fringe Benefits','Goods Used','Housekeeping Supplies','Laboratory Supplies Used','Light, Water &Maintenance','MAX Dr. Ched','Office Supplies Used','OR/WC Supplies Used','Others','Personnel Benefits','Pharmacy Medicines/Supplies USed','Professional Fees','PF PHIC','PF HMO','PF DSWD','OTHER PF','Retainers Fee','Representation & Entertainment','Salaries','Seminars & Trainings','Taxes, Licenses and Fees','Transporation & Travel','Utilities','Wages','Xray Supplies Used'),
                    'compare' =>  'IN'   
                );
            endif;

            $cats['relation'] = 'OR';
            $meta_query[] = $cats;
            $meta_query['relation'] = 'AND';
        endif;

        $query = $this->createQuery('incomeexpenses', $meta_query);
        $incs = array();
        $exps = array();

        foreach($query->posts as $p):
            $type = strtolower(get_field('type',$p->ID));
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", $type));

            if($slug == "income"):
                $slug2 = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtoupper(get_field('income_category', $p->ID))));
                $slug2 = (empty(get_field('income_category', $p->ID)))?'OTHERS_MISCELLANEOUS':$slug2;
                $cat = (empty(get_field('income_category', $p->ID)))?'Others/Miscellaneous':get_field('income_category', $p->ID);

                if(!isset($incs[$slug2])):
                    $incs[$slug2] = array(
                        'category' => $cat,
                        'description' => strtoupper(get_field('description', $p->ID)),
                        'amount' => (float)get_field('amount', $p->ID),
                        'type' => ucfirst($type)
                    );
                else:
                    $incs[$slug2]['amount'] += (float)get_field('amount', $p->ID);
                endif;
            else:
                $slug2 = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtoupper(get_field('expense_category', $p->ID))));
                $slug2 = (empty(get_field('expense_category', $p->ID)))?'OTHERS':$slug2;
                $cat = (empty(get_field('expense_category', $p->ID)))?'Others':get_field('expense_category', $p->ID);

                if(!isset($exps[$slug2])):
                    $exps[$slug2] = array(
                        'category' => $cat,
                        'description' => strtoupper(get_field('description', $p->ID)),
                        'amount' => (float)get_field('amount', $p->ID),
                        'type' => ucfirst($type)
                    );
                else:
                    $exps[$slug2]['amount'] += (float)get_field('amount', $p->ID);
                endif;
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
        $res .= "<th>Amount</th>";
        $res .= "</tr>";
        $res .= "</thead>";
        $res .= "<tbody>";
        
        ksort($incs);
        $totinc = 0;

        foreach($incs as $id => $i):
            $res .= "<tr>";
            $res .= "<td>".$i['category']."</td>";
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
        $res .= "<th>Amount</th>";
        $res .= "</tr>";
        $res .= "</thead>";
        $res .= "<tbody>";

        ksort($exps);
        $totexps = 0;

        foreach($exps as $id => $i):
            $res .= "<tr>";
            $res .= "<td>".$i['category']."</td>";
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

            if(strtotime($from) < $acdate):
                $range = (strtotime($to) - $acdate);
            else:
                $range = (strtotime($to) - strtotime($from));
            endif;

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

        $res .= '<div class="report__result-total"><span>DEPRECIATION EXPENSES:</span> &#8369 ('.$this->convertNumber($deptot).')</div>';

        $meta_query = array(
            array(
                'key'     => 'date_added',
                'value'   =>  array(date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))),
                'type'      =>  'date',
                'compare' =>  'between'  
            )
        );

        $query = $this->createQuery('beforeincometax', $meta_query, -1, 'date', 'ASC');
        $beforetax = 0;

        foreach($query->posts as $tax):
            $beforetax += (float)get_field('pre-tax_income_amount', $tax->ID);
        endforeach;

        $res .= '<div class="report__result-total" style="margin-bottom: 35px;"><span>BEFORE INCOME TAX:</span> &#8369 '.$this->convertNumber((($totinc - $totexps) - $deptot)).'</div>';
        $res .= '<div class="report__result-total"><span>Income Taxes:</span> (&#8369 '.$this->convertNumber($beforetax).')</div>';
        $res .= '<div class="report__result-total" style="margin-bottom: 35px;"><span>NET INCOME:</span> &#8369 '.$this->convertNumber((($totinc - $totexps) - $deptot) - $beforetax).'</div>';

        return $res;
    }


    public function getIncomeExpensesTotalNet($from, $to) {
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
                $slug2 = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtoupper(get_field('income_category', $p->ID))));
                $slug2 = (empty(get_field('income_category', $p->ID)))?'OTHERS_MISCELLANEOUS':$slug2;
                $cat = (empty(get_field('income_category', $p->ID)))?'Others/Miscellaneous':get_field('income_category', $p->ID);

                if(!isset($incs[$slug2])):
                    $incs[$slug2] = array(
                        'category' => $cat,
                        'description' => strtoupper(get_field('description', $p->ID)),
                        'amount' => (float)get_field('amount', $p->ID),
                        'type' => ucfirst($type)
                    );
                else:
                    $incs[$slug2]['amount'] += (float)get_field('amount', $p->ID);
                endif;
            else:
                $slug2 = preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", strtoupper(get_field('expense_category', $p->ID))));
                $slug2 = (empty(get_field('expense_category', $p->ID)))?'OTHERS':$slug2;
                $cat = (empty(get_field('expense_category', $p->ID)))?'Others':get_field('expense_category', $p->ID);

                if(!isset($exps[$slug2])):
                    $exps[$slug2] = array(
                        'category' => $cat,
                        'description' => strtoupper(get_field('description', $p->ID)),
                        'amount' => (float)get_field('amount', $p->ID),
                        'type' => ucfirst($type)
                    );
                else:
                    $exps[$slug2]['amount'] += (float)get_field('amount', $p->ID);
                endif;
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
        
        ksort($incs);

        $totinc = 0;

        foreach($incs as $id => $i):
            $totinc += (float)$i['amount'];
        endforeach;
        
        ksort($exps);

        $totexps = 0;

        foreach($exps as $id => $i):
            $totexps += (float)$i['amount'];
        endforeach;

        $deptot = 0;

        foreach($depitems as $id => $dep):
            $value = $dep['value'];
            $acdate = strtotime($dep['date']);
            $curdate = strtotime(date('Y-m-d', strtotime($to)));
            $deplimit = (strtolower($dep['type']) == "equipment")?strtotime("+10 years", $acdate):strtotime("+50 years", $acdate);

            $hundred = ($deplimit - $acdate);
            $percent = ($curdate - $acdate);

            if(strtotime($from) < $acdate):
                $range = (strtotime($to) - $acdate);
            else:
                $range = (strtotime($to) - strtotime($from));
            endif;

            $deppercent = ($percent / $hundred);
            $depvalue = (float)$value * $deppercent;
            $rangepercent = ($range / $hundred);
            $depvaluerange = (float)$value * $rangepercent;

            $deptot += (float)$depvaluerange;
        endforeach;
       
        return (($totinc - $totexps) - $deptot);
    }

    public function load_items_per_search(){
        $search = ($_POST['search'] == "false")?false:$_POST['search'];
        $dept = ($_POST['dept'] == "false")?false:$_POST['dept'];

        
        $this->getItemsSearch($search, $_POST['pt'], 1, $dept);
    }

    public function getItemsSearch($search = false, $pt, $paged = 1, $dept){
        $search = (strlen(trim($search)) == 0)?false:$search;

        ob_start();

        foreach($this->post_types as $ptypes):

            if($pt == $ptypes['post_type']):
                $header = $ptypes['header'];

                $this->createCustomPostListHtml($ptypes['post_type'], -1, $header, $search, $dept);
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

    public function createPostQuery($postType, $postPerPage, $pagination = false, $meta_query = array(), $s = false, $d = false) {
        $rows = array();
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        if($d):
            $meta_query = array(
                $meta_query,
                array(
                    'key'     => 'department',
                    'value'   =>  $d,
                    'compare' =>  '='   
                ),
                'relation' => 'AND'
            );
        endif;


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
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                $fields = get_fields(get_the_ID());
    
                $rows[get_the_ID()] = $fields;
            }
        }
    
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

    public function createCustomPostListHtml($postType, $postPerPage, $header, $s = false, $d = false) {
        $post_query = $this->createPostQuery($postType, $postPerPage, true, array(), $s, $d);
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

                if($key == "confirmed"):
                    $conf = get_field('confirmed', $postid);
                    $result = ($conf)?"CONFIRMED":"PENDING";
                    echo '<td>'.$result.'</td>';
                    continue;
                endif;

                if($key == "account_number"):
                    $bid = get_field('name_of_bank', $postid);

                    if(is_object($bid)):
                        $acctnum = get_field('account_number', $bid->ID);
                        echo '<td>'.$acctnum.'</td>';
                    else:
                        echo '<td>'.$value.'</td>';
                    endif;
                    continue;
                endif;

                if($key == "type_of_account"):
                    $bid = get_field('name_of_bank', $postid);
                    
                    if(is_object($bid)):
                        $typeacc = get_field('type_of_account', $bid->ID);
                        echo '<td>'.$typeacc.'</td>';
                    else:
                        echo '<td>'.$value.'</td>';
                    endif;
                    continue;
                endif;
                
                echo '<td>'.$value.'</td>';
            }

            echo '<td>'.$p['actions'].'</td>';
            
            echo '</tr>';
        }
        echo '</tbody>';

        echo '<table>';

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
            'form' => true,
            'return' => (is_null($redirect))?get_permalink(get_the_ID()):home_url('/'.$redirect)
        ));
    }

    public function my_save_post( $post_id ) {	

        if(isset($_POST['_acf_post_id'])) {
            $post_values = get_post($post_id);

            $types = array();

            foreach($this->post_types as $pt):
                $types[] = $pt['post_type']; 
            endforeach;
            
            if(!(in_array($post_values->post_type, $types))){
                return;
            }

            if($_POST['_acf_post_id'] == "new_post"){
                foreach($this->post_types as $pt):
                    if($post_values->post_type == $pt['post_type']){
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

                unset($_POST);
            }
            else if($_POST['_acf_post_id'] == $post_id) {

                foreach($this->post_types as $pt):
                    if($post_values->post_type == $pt['post_type']){
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

                unset($_POST);
            }
        }
    }

    public function createPostTypes() {
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

    public function get_department_releases() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_department_releases')) {
            wp_send_json_error('Security check failed');
        }

        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'pending';
        $department = isset($_POST['department']) ? sanitize_text_field($_POST['department']) : 'ALL';
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        
        $meta_query = array('relation' => 'AND');
        
        if ($status === 'pending') {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key' => 'confirmed',
                    'value' => '1',
                    'compare' => '!='
                ),
                array(
                    'key' => 'confirmed',
                    'compare' => 'NOT EXISTS'
                )
            );
        } else {
            $meta_query[] = array(
                'key' => 'confirmed',
                'value' => '1',
                'compare' => '='
            );
        }
        
        if ($department !== 'ALL' && !current_user_can('manage_options') && !in_array('um_accounting', wp_get_current_user()->roles) && $user_id != 4) {
            $meta_query[] = array(
                'key' => 'department',
                'value' => $department,
                'compare' => '='
            );
        } else if ($department !== 'ALL') {
            $meta_query[] = array(
                'key' => 'department',
                'value' => $department,
                'compare' => '='
            );
        } else if (!current_user_can('manage_options') && !in_array('um_accounting', wp_get_current_user()->roles) && $user_id != 4) {
            $department_id = $user_id;
            
            $department_name = '';
            foreach ($this->departmentArr as $dept => $id) {
                if ($id == $department_id) {
                    $department_name = $dept;
                    break;
                }
            }
            
            if ($department_name) {
                $meta_query[] = array(
                    'key' => 'department',
                    'value' => $department_name,
                    'compare' => '='
                );
            }
        }
        
        $args = array(
            'post_type' => 'releasesupplies',
            'posts_per_page' => -1,
            'meta_query' => $meta_query,
            'orderby' => 'meta_value',
            'meta_key' => 'release_date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        $releases = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $post = get_post($post_id);
                $author_id = $post->post_author;
                $supply_id = get_field('supply_name', $post_id)->ID;
                $supply_name = get_field('supply_name', $post_id)->post_title;
                $quantity = get_field('quantity', $post_id);
                $release_date = get_field('release_date', $post_id);
                
                $price_per_unit = get_field('price_per_unit', $supply_id);
                
                $author_name = get_the_author_meta('display_name', $author_id);
                $department_id = $author_id;
                $department_name = '';
                
                foreach ($this->departmentArr as $dept => $id) {
                    if ($id == $department_id) {
                        $department_name = $dept;
                        break;
                    }
                }
                
                $released_by = $author_name;
                if (!empty($department_name)) {
                    $released_by .= ' (' . $department_name . ')';
                }
                
                $released_to = get_field('department', $post_id);
                
                $releases[] = array(
                    'id' => $post_id,
                    'supply_name' => $supply_name,
                    'quantity' => $quantity,
                    'release_date' => $release_date,
                    'price_per_unit' => $price_per_unit,
                    'released_by' => $released_by,
                    'released_to' => $released_to
                );
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success($releases);
    }

    public function update_release_status() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_release_status')) {
            wp_send_json_error('Security check failed');
        }
        
        $release_id = isset($_POST['release_id']) ? intval($_POST['release_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'pending';
        
        if (!$release_id) {
            wp_send_json_error('Invalid release ID');
        }
        
        $post = get_post($release_id);
        if (!$post || $post->post_type !== 'releasesupplies') {
            wp_send_json_error('Release supply not found');
        }
        
        $result = update_field('confirmed', ($status === 'confirmed' ? true : false), $release_id);
        
        if ($result) {
            wp_send_json_success('Status updated successfully');
        } else {
            wp_send_json_error('Failed to update status');
        }
    }

    public function get_pending_release_count() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'get_pending_release_count')) {
            wp_send_json_error('Security check failed');
        }

        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        
        $has_advanced_access = current_user_can('manage_options') || 
                               in_array('um_accounting', wp_get_current_user()->roles) || 
                               $user_id == 4;
        
        $meta_query = array(
            'relation' => 'OR',
            array(
                'key' => 'confirmed',
                'value' => '1',
                'compare' => '!='
            ),
            array(
                'key' => 'confirmed',
                'compare' => 'NOT EXISTS'
            )
        );
        
        if (!$has_advanced_access) {
            $department_id = $user_id;
            
            $department_name = '';
            foreach ($this->departmentArr as $dept => $id) {
                if ($id == $department_id) {
                    $department_name = $dept;
                    break;
                }
            }
            
            if ($department_name) {
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'confirmed',
                            'value' => '1',
                            'compare' => '!='
                        ),
                        array(
                            'key' => 'confirmed',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'department',
                        'value' => $department_name,
                        'compare' => '='
                    )
                );
            }
        }
        
        $args = array(
            'post_type' => 'releasesupplies',
            'posts_per_page' => -1,
            'meta_query' => $meta_query
        );
        
        $query = new WP_Query($args);
        $count = $query->found_posts;
        
        wp_send_json_success($count);
    }
}
