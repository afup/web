<?php
/**
 * Custom add to cart for the homepage shop section.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( function_exists( 'method_exists' ) && method_exists( $product, 'get_id' ) ) {
	$prod_id = $product->get_id();
} else {
	$prod_id = $product->id;
}

hestia_load_fa();
echo apply_filters(
	'woocommerce_loop_add_to_cart_link',
	sprintf(
		'<a rel="nofollow" href="%1$s" data-quantity="%2$s" data-product_id="%3$s" data-product_sku="%4$s" class="%5$s btn btn-just-icon btn-simple btn-default" title="%6$s"><i rel="tooltip" data-original-title="%6$s" class="fas fa-cart-plus"></i></a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $quantity ) ? $quantity : 1 ),
		esc_attr( $prod_id ),
		esc_attr( $product->get_sku() ),
		esc_attr( isset( $class ) ? $class : 'button' ),
		esc_attr( $product->add_to_cart_text() )
	),
	$product
);
