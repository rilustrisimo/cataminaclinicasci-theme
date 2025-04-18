<?php
/**
 * Add Supply Count Corrector link to admin menu
 */

// Add to admin menu
add_action('admin_menu', 'add_supply_corrector_menu');

function add_supply_corrector_menu() {
    add_management_page(
        'Supply Count Corrector',
        'Supply Count Corrector',
        'manage_options',
        'supply-corrector',
        'supply_corrector_redirect'
    );
}

function supply_corrector_redirect() {
    // Generate a secure nonce for the supply corrector access
    $nonce = wp_create_nonce('supply_corrector_access');
    
    // Redirect to the supply corrector script with nonce for security
    $url = home_url('/wp-content/themes/eyor-theme/php/supply-corrector.php?_wpnonce=' . $nonce);
    ?>
    <div class="wrap">
        <h1>Supply Count Corrector</h1>
        <p>The Supply Count Corrector tool allows you to compare and update inventory counts using CSV data.</p>
        <p>This tool helps you:</p>
        <ul style="list-style-type: disc; padding-left: 20px;">
            <li>Import supply counts from a CSV file</li>
            <li>Match them with existing supplies in the database</li>
            <li>Identify and resolve count discrepancies</li>
            <li>Update inventory quantities based on actual counts</li>
        </ul>
        <p><a href="<?php echo esc_url($url); ?>" target="_blank" class="button button-primary">Launch Supply Count Corrector</a></p>
        <p><strong>Note:</strong> The tool will open in a new tab.</p>
        
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.button-primary').addEventListener('click', function() {
                    window.open("<?php echo esc_url($url); ?>", "_blank");
                });
            });
        </script>
    </div>
    <?php
}
?>