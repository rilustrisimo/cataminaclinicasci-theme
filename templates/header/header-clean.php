<?php
/**
 * Header clean template part.
 *
 * @author    eyorsogood.com, Rouie Ilustrisimo
 * @version   1.0.0
 */

 $theme = new Theme();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php
	if ( ! qed_check( 'is_wordpress_seo_in_use' ) ) {
		printf( '<meta name="description" content="%s">', get_bloginfo( 'description', 'display' ) );
	}
	?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php acf_form_head(); ?>
	<?php wp_head(); ?>
	<script>
	jQuery(document).ready(function($) {
	    // Only run if user is logged in
	    <?php if (is_user_logged_in()): ?>
	    
	    // Function to get count of pending releases
	    function getPendingReleaseCount() {
	        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	        
	        $.ajax({
	            url: ajaxurl,
	            type: 'POST',
	            data: {
	                action: 'get_pending_release_count',
	                user_id: <?php echo get_current_user_id(); ?>,
	                nonce: '<?php echo wp_create_nonce("get_pending_release_count"); ?>'
	            },
	            success: function(response) {
	                if (response.success && response.data > 0) {
	                    // Find the Release Management menu item
	                    $('li.menu-item a').each(function() {
	                        if ($(this).text().trim() === 'Release Management') {
	                            // Add badge with count
	                            var badge = $('<span class="badge-counter">' + response.data + '</span>');
	                            
	                            // Remove existing badge if any
	                            $(this).find('.badge-counter').remove();
	                            
	                            // Add new badge
	                            $(this).append(badge);
	                        }
	                    });
	                }
	            }
	        });
	    }
	    
	    // Add styles for the counter badge
	    $('<style>\
	        .badge-counter {\
	            display: inline-flex;\
	            justify-content: center;\
	            align-items: center;\
	            position: absolute;\
	            margin-left: 8px;\
	            min-width: 18px;\
	            height: 18px;\
	            font-size: 11px;\
	            font-weight: 600;\
	            line-height: 1;\
	            background-color: #dc3545;\
	            color: white;\
	            border-radius: 10px;\
	            padding: 0 6px;\
	        }\
	    </style>').appendTo('head');
	    
	    // Initial load
	    getPendingReleaseCount();
	    
	    // Refresh every minute
	    setInterval(getPendingReleaseCount, 60000);
	    
	    <?php endif; ?>
	});
	</script>
</head>
<body <?php body_class(); ?>>
<div class="modal-container">
	<div class="modal__inner">
		<div class="modal__close"><i class="fa-solid fa-xmark"></i></div>
		<div class="modal__content"></div>
	</div>
</div>
<a href="<?php echo home_url(); ?>" class="logo-link">
	<div class="overall-menu__logo-outside">
		<div class="top">Catamina Clinic</div>
		<div class="bottom">& Ambulatory Sugery Center, Inc.</div>
	</div>
</a>
<div class="overall-menu expanded">
	<div id="navMenu" class="active">
		<span></span>
		<span></span>
		<span></span>
	</div>
	<a href="<?php echo home_url(); ?>" class="logo-link">
		<div class="overall-menu__logo">
			<div class="top">Catamina Clinic</div>
			<div class="bottom">& Ambulatory Surgery Center, Inc.</div>
		</div>
	</a>
	<div class="header__content-wrap">
		<div class="col-md-12 header__content">
			<?php if(current_user_can( 'manage_options' )): ?>
				<?php if ( has_nav_menu( 'header-menu' ) ) : ?>
					<?php $dis = (is_user_logged_in())?'':' disabled';?>
					<nav class="main-nav-header" role="navigation">
						<?php wp_nav_menu(array(
							'theme_location' => 'header-menu',
							'container' => 'ul',
							'menu_class' => 'main-nav'.$dis,
							'menu_id' => 'navigation',
							'depth' => 3,
						)); ?>
					</nav>
				<?php endif; ?>

			<?php else: ?>
			    <?php
    			    $user = wp_get_current_user();
                    $roles = ( array ) $user->roles;
			    ?>
			    
			    <?php if(isset($roles[0]) && ($roles[0] == "um_accounting")): ?>
			    
			        <?php if ( has_nav_menu( 'header-menu-accounting' ) ) : ?>
    					<?php $dis = (is_user_logged_in())?'':' disabled';?>
    					<nav class="main-nav-header" role="navigation">
    						<?php wp_nav_menu(array(
    							'theme_location' => 'header-menu-accounting',
    							'container' => 'ul',
    							'menu_class' => 'main-nav'.$dis,
    							'menu_id' => 'navigation',
    							'depth' => 3,
    						)); ?>
    					</nav>
    				<?php endif; ?>
			    
			    <?php else: ?>
    				<?php if ( has_nav_menu( 'header-menu-staff' ) ) : ?>
    					<?php $dis = (is_user_logged_in())?'':' disabled';?>
    					<nav class="main-nav-header" role="navigation">
    						<?php wp_nav_menu(array(
    							'theme_location' => 'header-menu-staff',
    							'container' => 'ul',
    							'menu_class' => 'main-nav'.$dis,
    							'menu_id' => 'navigation',
    							'depth' => 3,
    						)); ?>
    					</nav>
    				<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
			<div class="clearfix"></div>
		</div><!-- .header__content -->
	</div><!-- .header__content-wrap -->
	<div class="login-status">
		<div class="login-status__wrap">
			<?php if(is_user_logged_in()): ?>
				<div class="login-status__greeting">Welcome,</div>
				<div class="login-status__name"><?php echo $theme->getUserData()->user_firstname.' '.$theme->getUserData()->user_lastname; ?></div>
				<div class="login-status__btn"><a class="btn button" href="<?php echo wp_logout_url(); ?>">Logout <i class="fa-solid fa-arrow-right-from-bracket"></i></a></div>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="layout-content">