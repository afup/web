<?php
/**
 * Class that manages WooCommerce Shop page.
 *
 * @package Inc/Modules/Woo_Enhancements/Views
 */

/**
 * Class Hestia_Woo_Shop_Page
 */
class Hestia_Woo_Shop_Page {

	/**
	 * Check if the shop view should load.
	 *
	 * @return bool
	 */
	private function should_load() {
		$is_product_loop               = is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy();
		$is_elementor_shop_template    = Hestia_Woocommerce_Manager::is_elementor_template( 'archive', 'product_archive' );
		$is_elementor_product_template = Hestia_Woocommerce_Manager::is_elementor_template( 'single', 'product' );
		if ( $is_product_loop && $is_elementor_shop_template ) {
			return false;
		}

		if ( $is_elementor_shop_template ) {
			return false;
		}

		if ( $is_elementor_product_template ) {
			return false;
		}

		return true;
	}

	/**
	 * Register WooCommerce hooks.
	 */
	function run() {
		if ( ! $this->should_load() ) {
			return false;
		}

		/**
		 * Remove breadcrumbs, result count, catalog ordering and taxonomy archive description to reposition them.
		 */
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

		/**
		 * Reposition breadcrumbs and result count
		 */
		add_action( 'hestia_woocommerce_custom_reposition_left_shop_elements', array( $this, 'hestia_woocommerce_reposition_left_shop_elements' ) );

		/**
		 * Reposition catalog ordering
		 */
		add_action( 'hestia_woocommerce_custom_reposition_right_shop_elements', array( $this, 'hestia_woocommerce_reposition_right_shop_elements' ) );

		/**
		 * Manage sale tag for products
		 */
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 20 );

		/**
		 * Manage products listing layout
		 */
		add_action( 'woocommerce_before_shop_loop_item_title', 'hestia_woocommerce_template_loop_product_thumbnail', 10 );
		add_action( 'woocommerce_before_shop_loop_item', 'hestia_woocommerce_before_shop_loop_item', 10 );
		add_action( 'woocommerce_after_shop_loop_item', 'hestia_woocommerce_after_shop_loop_item', 20 );
		add_action( 'woocommerce_shop_loop_item_title', 'hestia_woocommerce_template_loop_product_title', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

		$page_display = get_option( 'woocommerce_shop_page_display', '' );
		if ( $page_display === 'both' ) {
			remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
			add_action( 'woocommerce_before_shop_loop', array( $this, 'display_product_categories' ) );
		}

		return true;
	}

	/**
	 * Display categories on shop.
	 */
	public function display_product_categories() {
		$columns = get_option( 'woocommerce_catalog_columns', 4 );
		echo '<ul class="products columns-' . esc_attr( $columns ) . '">';
		echo woocommerce_maybe_show_product_subcategories();
		echo '</ul>';
	}

	/**
	 * Reposition breadcrumb and results count - adding
	 */
	public function hestia_woocommerce_reposition_left_shop_elements() {
		woocommerce_breadcrumb();
		woocommerce_result_count();
	}

	/**
	 * Reposition ordering - adding
	 */
	public function hestia_woocommerce_reposition_right_shop_elements() {
		woocommerce_catalog_ordering();
	}

}
