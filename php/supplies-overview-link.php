<?php
/**
 * Add Supplies Overview link to admin menu
 */

// Add to admin menu
add_action('admin_menu', 'add_supplies_overview_menu');

function add_supplies_overview_menu() {
    add_management_page(
        'Supplies Overview',
        'Supplies Overview',
        'manage_options',
        'supplies-overview',
        'supplies_overview_redirect'
    );
}

function supplies_overview_redirect() {
    // Redirect to the supplies overview script
    $url = home_url('/wp-content/themes/eyor-theme/php/supplies-overview.php');
    ?>
    <script type="text/javascript">
        window.open("<?php echo $url; ?>", "_blank");
    </script>
    <p>If you are not redirected automatically, follow this <a target="_blank" href="<?php echo $url; ?>">link to Supplies Overview</a>.</p>
    <?php
}
?>
