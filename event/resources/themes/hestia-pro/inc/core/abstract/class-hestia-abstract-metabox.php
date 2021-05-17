<?php
/**
 * Abstract class of metaboxes.
 *
 * @package Hestia
 */

/**
 * Class Hesita_Abstract_Metabox
 */
abstract class Hestia_Abstract_Metabox {

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Add metabox fuction.
	 */
	public abstract function add();

	/**
	 * Save metabox function.
	 */
	public abstract function save( $post_id );

	/**
	 * Display metabox function.
	 */
	public abstract function html();

	/**
	 * Create a control inside the metabox.
	 *
	 * @param string $type Control type.
	 * @param string $id Control id.
	 * @param array  $settings Control settings.
	 *
	 * @return WP_Error | Hestia_Meta_Radio_Buttons
	 */
	protected function create_control( $type, $id, $settings = array() ) {
		if ( empty( $type ) ) {
			return new WP_Error( 'missing-type', 'No control type.' );
		}

		if ( empty( $id ) ) {
			return new WP_Error( 'missing-id', 'No control id.' );
		}

		$feature_words = explode( '-', $type );
		$feature_words = array_map( 'ucfirst', $feature_words );
		$feature_name  = implode( '_', $feature_words );
		$class         = 'Hestia_Meta_' . $feature_name;

		if ( class_exists( $class ) ) {
			return new $class( $id, $settings );
		}

		return new WP_Error( 'missing-class', 'Class does not exist' );
	}
}
