<?php
/**
 * The Sidebar for WooCommerce containing the main widget areas.
 *
 * @package Hestia
 * @since Hestia 1.0
 * @modified 1.1.30
 */

if ( is_active_sidebar( 'sidebar-woocommerce' ) ) { ?>
	<div class="col-md-3 shop-sidebar-wrapper">
		<?php do_action( 'hestia_before_shop_sidebar' ); ?>
		<aside id="secondary" class="shop-sidebar" role="complementary">
			<?php do_action( 'hestia_before_shop_sidebar_content' ); ?>
			<?php dynamic_sidebar( 'sidebar-woocommerce' ); ?>
			<?php do_action( 'hestia_after_shop_sidebar_content' ); ?>
		</aside><!-- .sidebar .widget-area -->
		<?php do_action( 'hestia_after_shop_sidebar' ); ?>
	</div>
	<?php
} elseif ( is_customize_preview() ) {
	hestia_sidebar_placeholder( 'col-md-3 shop-sidebar-wrapper col-md-offset-1', 'sidebar-woocommerce' );
} ?>
