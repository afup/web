<?php
/**
 * Addon main manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Addon_Manager
 */
class Hestia_Addon_Manager extends Hestia_Abstract_Main {

	/**
	 * Addons array
	 *
	 * @var array $addons classes that will be loaded instead of the basic functionality.
	 */
	protected $addons;

	/**
	 * Addons array
	 *
	 * @var array $high_priority_addons classes that will be loaded before the main features.
	 */
	protected $high_priority_addons;

	/**
	 * Removables array
	 *
	 * @var array $features_to_remove classes that will not ve loaded because we'll be using the addon versions.
	 */
	protected $features_to_remove;

	/**
	 * Initialize addon manager.
	 */
	public function init() {
		define( 'HESTIA_PRO_FLAG', 'pro_available' );
		define( 'HESTIA_ADDONS_URI', trailingslashit( get_template_directory_uri() ) . 'inc/addons/' );

		$this->set_high_priority_addons();
		$this->set_addons();
		$this->set_removables();

		add_filter( 'hestia_filter_main_features', array( $this, 'remove_features' ), 0 );
		add_filter( 'hestia_filter_main_features', array( $this, 'add_high_priority_addons' ), 10 );
		add_filter( 'hestia_filter_main_features', array( $this, 'add_addons' ), 20 );

		/* Conditional Add-ons */
		if ( class_exists( 'WooCommerce', false ) ) {
			add_filter( 'hestia_filter_main_features', array( $this, 'add_woocommerce_addon' ), 30 );
		}
	}

	/**
	 * Set add-ons.
	 */
	private function set_addons() {
		$this->addons = array(
			'main-addon',
			'header-addon',
			'header-controls-addon',
			'shop-controls-addon',
			'footer-controls-addon',
			'typography-controls-addon',
			'slider-section-addon',
			'slider-controls-addon',
			'blog-section-controls-addon',
			'features-section',
			'features-controls',
			'team-section',
			'team-controls',
			'testimonials-section',
			'testimonials-controls',
			'portfolio-section',
			'portfolio-controls',
			'pricing-section',
			'pricing-controls',
			'ribbon-section',
			'ribbon-controls',
			'clients-bar-section',
			'clients-bar-controls',
			'public-typography-addon',
			'general-inline-style',
			'general-controls-addon',
			'buttons-style-controls-addon',
			'blog-settings-controls-addon',
			'subscribe-blog-section',
			'customizer-notices-addon',
			'colors-addon',
			'buttons-addon',
			'color-controls-addon',
			'hooks-page',
			'elementor-compatibility-addon',
			'front-page-shortcodes',
			'dokan-compatibility',
			'translations-manager',
			'metabox-addon',
			'white-label-controls-addon',
			'woocommerce-module',
			'custom-layouts-module',
			'compatibility-style-addon',
		);
	}

	/**
	 * Set high priority addons. These addons will be added before the features.
	 */
	private function set_high_priority_addons() {
		$this->high_priority_addons = array(
			'section-ordering',
		);
	}

	/**
	 * Set removable features.
	 */
	private function set_removables() {
		$this->features_to_remove = array(
			'header-controls',
			'typography-controls',
			'header',
			'big-title-section',
			'big-title-controls',
			'shop-controls',
			'blog-section-controls',
			'public-typography',
			'general-controls',
			'buttons-style-controls',
			'blog-settings-controls',
			'upsell-manager',
			'customizer-notices',
			'colors',
			'buttons',
			'elementor-compatibility',
			'child-compat-customizer',
			'metabox-main',
			'compatibility-style',
		);
	}

	/**
	 * Remove unused features.
	 *
	 * @param array $features features.
	 *
	 * @return mixed
	 */
	public function remove_features( $features ) {
		$removables = $this->features_to_remove;
		foreach ( $removables as $removable ) {
			$key = array_search( $removable, $features, true );
			if ( $key !== false ) {
				unset( $features[ $key ] );
			}
		}

		return $features;
	}

	/**
	 * Add addons to load.
	 *
	 * @param array $features features.
	 *
	 * @return array
	 */
	public function add_addons( $features ) {
		$addons = $this->addons;
		foreach ( $addons as $addon ) {
			array_push( $features, $addon );
		}

		return $features;
	}

	/**
	 * Add addons to load.
	 *
	 * @param array $features features.
	 *
	 * @return array
	 */
	public function add_high_priority_addons( $features ) {
		$addons = $this->high_priority_addons;
		foreach ( $addons as $addon ) {
			array_unshift( $features, $addon );
		}

		return $features;
	}

	/**
	 * Load the WooCommerce Add-on.
	 *
	 * @param array $addons Add-ons already passed to this filter.
	 *
	 * @return mixed
	 */
	public function add_woocommerce_addon( $addons ) {
		// add the WooCommerce settings in Customizer.
		array_push( $addons, 'woocommerce-settings-controls' );
		// add the infinite scroll version of woocommerce.
		array_push( $addons, 'woocommerce-infinite-scroll' );
		// get the front-end functionality for this add-on
		require_once HESTIA_PHP_INCLUDE . '/addons/plugin-compatibility/woocommerce/functions.php';

		return $addons;
	}
}
