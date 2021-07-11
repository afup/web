<?php
/**
 * Class that manages WooCommerce Product page.
 *
 * @package Inc/Modules/Woo_Enhancements/Views
 */

/**
 * Class Hestia_Woo_Shop_Page
 */
class Hestia_Woo_Product_Page {

	/**
	 * Check if the shop view should load.
	 *
	 * @return bool
	 */
	protected function should_load() {
		if ( ! is_product() ) {
			return false;
		}
		if ( Hestia_Woocommerce_Manager::is_elementor_template( 'single', 'product' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @return bool
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return false;
		}

		/**
		 * Manage sale tag
		 */
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'hestia_wrap_product_image' ), 18 );
		add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 21 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'hestia_close_wrap' ), 22 );

		/**
		 * Manage related products
		 */
		add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

		return true;
	}

	/**
	 * Wrap product image in a div.
	 */
	public function hestia_wrap_product_image() {
		echo '<div class="hestia-product-image-wrap">';
	}

	/**
	 * Close product image wrap.
	 */
	public function hestia_close_wrap() {
		echo '</div>';
	}
}
