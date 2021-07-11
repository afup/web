<?php
/**
 * WooCommerce related features
 *
 * @package Inc/Addons/Modules/Woo_Enhancements
 */

/**
 * Class Hestia_Woocommerce_Module
 */
class Hestia_Woocommerce_Module extends Hestia_Abstract_Module {

	/**
	 * Classes to load in module
	 *
	 * @var array
	 */
	protected $classes_to_load = array();

	/**
	 * Hestia_Woocommerce_Module constructor.
	 */
	public function __construct() {
		$this->classes_to_load = array(
			'Hestia_Product_Controls'         => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/customizer',
			'Hestia_Product_View'             => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/views',
			'Hestia_Cart_Controls'            => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/customizer',
			'Hestia_Cart_View'                => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/views',
			'Hestia_Product_Catalog_Controls' => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/customizer',
			'Hestia_Product_Catalog_View'     => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements/views',
		);
	}

	/**
	 * Check if this module should load.
	 *
	 * @return bool|void
	 */
	function should_load() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Run module.
	 */
	function run_module() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		$load_seamless = get_theme_mod( 'hestia_enable_seamless_add_to_cart', false );
		if ( $load_seamless === true ) {
			wp_enqueue_script( 'hestia_seamless_add_to_cart', get_template_directory_uri() . '/inc/addons/modules/woo_enhancements/assets/js/seamless.js', array( 'jquery' ), HESTIA_VERSION, true );
		}
	}

	/**
	 * Get product categories.
	 *
	 * @return array
	 */
	static function get_products_categories() {

		$categories_array = array();
		$categories       = get_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => 1,
				'title_li'   => '',
			)
		);
		if ( empty( $categories ) ) {
			return array();
		}
		foreach ( $categories as $category ) {
			if ( ! empty( $category->term_id ) && ! empty( $category->name ) ) {
				$categories_array[ $category->term_id ] = $category->name;
			}
		}
		return $categories_array;
	}

}
