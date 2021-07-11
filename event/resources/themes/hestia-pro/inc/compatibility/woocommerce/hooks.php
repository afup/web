<?php
/**
 * Hooks for WooCommerce which only needs to be used when WooCommerce is active.
 *
 * @package Hestia
 * @since Hestia 1.0.2
 */

/**
 * Layout for the main content of shop page
 *
 * @see  hestia_woocommerce_before_main_content()
 * @see  hestia_woocommerce_after_main_content()
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );  /* Remove the sidebar */
add_action( 'woocommerce_before_main_content', 'hestia_woocommerce_before_main_content', 10 );
add_action( 'woocommerce_after_main_content', 'hestia_woocommerce_after_main_content', 9 );

/* Remove title on shop main */
add_filter( 'woocommerce_show_page_title', 'hestia_woocommerce_hide_page_title' );

/* Move breadcrumbs on the single page */
if ( is_single( 'product' ) ) {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_breadcrumb', 10, 0 );


add_filter( 'woocommerce_add_to_cart_fragments', 'hestia_woocommerce_header_add_to_cart_fragment' ); /* Ensure cart contents update when products are added to the cart via AJAX ) */

/**
 * Reposition Cross Sells after Cart Totals
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );

/**
 * Checkout page
 *
 * @see hestia_coupon_after_order_table_js()
 * @see hestia_coupon_after_order_table()
 */
add_action( 'woocommerce_before_checkout_form', 'hestia_coupon_after_order_table_js' );
add_action( 'woocommerce_checkout_order_review', 'hestia_coupon_after_order_table' );

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'hestia_cart_link_fragment' );

/**
 * Add before and after cart totals code for card.
 */
add_action( 'woocommerce_before_cart_totals', 'hestia_woocommerce_before_cart_totals', 1 );
add_action( 'woocommerce_after_cart_totals', 'hestia_woocommerce_after_cart_totals', 1 );
