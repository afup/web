<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-21
 *
 * @package class-hestia-metabox-view.php
 */

/**
 * Class Hestia_Metabox_View
 *
 * Handles the changes for the metabox.
 */
class Hestia_Metabox_View extends Hestia_Abstract_Main {

	/**
	 * Toggle map: [context->meta_key]
	 *
	 * @var array
	 */
	private $toggles_map = array(
		'header' => 'hestia_disable_navigation',
		'footer' => 'hestia_disable_footer',
	);

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_filter( 'hestia_filter_components_toggle', array( $this, 'toggle_components' ), 10, 2 );
	}

	/**
	 * Handle components toggle.
	 *
	 * @param bool   $value   value.
	 * @param string $context context.
	 *
	 * @return bool
	 */
	public function toggle_components( $value, $context ) {
		if ( ! isset( $this->toggles_map[ $context ] ) ) {
			return $value;
		}

		$meta_value = get_post_meta( $this->get_post_id(), $this->toggles_map[ $context ], true );

		if ( empty( $meta_value ) ) {
			return $value;
		}

		if ( $meta_value === 'on' ) {
			return true;
		}

		return $value;
	}

	/**
	 * Get the post ID.
	 *
	 * @return int|null
	 */
	private function get_post_id() {

		if ( 'page' === get_option( 'show_on_front' ) && is_home() ) {
			return get_option( 'page_for_posts' );
		}

		if ( class_exists( 'WooCommerce', false ) && is_shop() ) {
			return get_option( 'woocommerce_shop_page_id' );
		}

		global $post;

		if ( ! isset( $post ) ) {
			return null;
		}

		return $post->ID;
	}
}
