<?php
/**
 * Customizer sections order main file
 *
 * @package Hestia
 */

/**
 * Class Hestia_Section_Ordering
 */
class Hestia_Section_Ordering extends Hestia_Register_Customizer_Controls {
	/**
	 * Initialize the section ordering module.
	 */
	public function init() {
		parent::init();
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_enqueue' ) );
		add_action( 'customize_preview_init', array( $this, 'refresh_positions' ), 100 );
		add_filter( 'hestia_section_priority', array( $this, 'get_section_priority' ), 10, 2 );
	}

	/**
	 * Add customizer controls
	 */
	public function add_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'sections_order',
				array(
					'sanitize_callback' => array( $this, 'sanitize_order' ),
				),
				array(
					'section'  => 'hestia_general',
					'type'     => 'hidden',
					'priority' => 80,
				)
			)
		);
	}

	/**
	 * Enqueue specific customizer scripts.
	 */
	public function customizer_enqueue() {
		wp_enqueue_script(
			'hestia_customizer-sections-order-script',
			HESTIA_ADDONS_URI . 'assets/js/customizer-sections-order.js',
			array(
				'jquery',
				'jquery-ui-sortable',
				'customize-controls',
			),
			HESTIA_VERSION,
			true
		);
		$control_settings = array(
			'sections_container' => '#accordion-panel-hestia_frontpage_sections > ul, #sub-accordion-panel-hestia_frontpage_sections',
			'blocked_items'      => '#accordion-section-hestia_slider, #accordion-section-hestia_info_jetpack, #accordion-section-hestia_info_woocommerce, #accordion-section-sidebar-widgets-sidebar-big-title',
			'saved_data_input'   => '#customize-control-sections_order input',
		);
		wp_localize_script( 'hestia_customizer-sections-order-script', 'control_settings', $control_settings );
		wp_enqueue_style( 'hestia_customizer-sections-order-style', HESTIA_ADDONS_URI . '/assets/css/customizer-sections-order-style.css', array( 'dashicons' ), HESTIA_VERSION );
	}

	/**
	 * Function for returning section priority
	 *
	 * @param integer $value Default priority.
	 * @param string  $key Section id.
	 *
	 * @return int
	 */
	public function get_section_priority( $value, $key = '' ) {
		$orders = get_theme_mod( 'sections_order' );
		if ( empty( $orders ) ) {
			return $value;
		}
		$json = json_decode( $orders );

		if ( isset( $json->$key ) ) {
			return $json->$key;
		} elseif ( $key === 'sidebar-widgets-subscribe-widgets' && isset( $json->hestia_subscribe ) ) {
			return $json->hestia_subscribe;
		}

		return $value;
	}

	/**
	 * Function to refresh customize preview when changing sections order
	 */
	public function refresh_positions() {
		$section_order         = get_theme_mod( 'sections_order' );
		$section_order_decoded = json_decode( $section_order, true );
		if ( ! empty( $section_order_decoded ) ) {
			remove_all_actions( 'hestia_sections' );
			foreach ( $section_order_decoded as $k => $priority ) {
				if ( $k !== 'hestia_subscribe' ) {
					if ( $k === 'sidebar-widgets-subscribe-widgets' ) {
						$this->hook_section_by_slug( 'hestia_subscribe', $priority );
					} else {
						$this->hook_section_by_slug( $k, $priority );
					}
				}
			}
		}
	}

	/**
	 * Function to sanitize sections order control
	 *
	 * @param string $input Sections order in json format.
	 *
	 * @return string
	 */
	public function sanitize_order( $input ) {

		$json = json_decode( $input, true );
		foreach ( $json as $section => $priority ) {
			if ( ! is_string( $section ) || ! is_int( $priority ) ) {
				return false;
			}
		}
		$filter_empty = array_filter( $json, array( $this, 'not_empty' ) );

		return json_encode( $filter_empty );
	}

	/**
	 * Function to filter json empty elements.
	 *
	 * @param int $val Element of json decoded.
	 *
	 * @return bool
	 */
	private function not_empty( $val ) {
		return ! empty( $val );
	}

	/**
	 * Get section class name to instantiate.
	 *
	 * @param string $slug section slug.
	 *
	 * @return string
	 */
	private function get_section_class_name( $slug ) {
		if ( empty( $slug ) ) {
			__return_empty_string();
		}

		$slug_words = explode( '_', $slug );
		$slug_words = array_map( 'ucfirst', $slug_words );
		$slug       = implode( '_', $slug_words );
		return $slug . '_Section';
	}

	/**
	 * Hook section by slug.
	 *
	 * @param string  $slug section slug.
	 * @param integer $priority section priority.
	 */
	private function hook_section_by_slug( $slug, $priority ) {
		if ( empty( $slug ) ) {
			return;
		}

		$section = $this->get_section_class_name( $slug );

		if ( empty( $section ) || ! class_exists( $section ) ) {
			return;
		}

		$section_instance = new $section;
		add_action( 'hestia_sections', array( $section_instance, 'do_section' ), $priority );
	}
}


