<?php
/**
 * Customizer shop settings controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_WooCommerce_Settings_Controls
 *
 * @since 1.1.85
 */
class Hestia_WooCommerce_Settings_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		$this->add_shop_settings_panel();
		$this->add_shop_settings_section();
		$this->add_checkout_settings_controls();
	}

	/**
	 * Add Shop customizer panel.
	 */
	private function add_shop_settings_panel() {
		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_shop_settings',
				array(
					'priority' => 46,
					'title'    => esc_html__( 'Shop Settings', 'hestia-pro' ),
				)
			)
		);
	}

	/**
	 * Add Shop settings sections
	 */
	private function add_shop_settings_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_checkout_settings',
				array(
					'title'    => apply_filters( 'hestia_checkout_settings_control_label', esc_html__( 'Checkout', 'hestia-pro' ) ),
					'priority' => 46,
					'panel'    => 'hestia_shop_settings',
				)
			)
		);
	}

	/**
	 * Add checkout layout controls.
	 */
	private function add_checkout_settings_controls() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_disable_order_note',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Disable Order Note', 'hestia-pro' ),
					'section'  => 'hestia_checkout_settings',
					'priority' => 40,
					'type'     => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_disable_coupon',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Disable Coupon', 'hestia-pro' ),
					'section'  => 'hestia_checkout_settings',
					'priority' => 40,
					'type'     => 'checkbox',
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_distraction_free_checkout',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
				),
				array(
					'label'    => esc_html__( 'Distraction Free Checkout', 'hestia-pro' ),
					'section'  => 'hestia_checkout_settings',
					'priority' => 40,
					'type'     => 'checkbox',
				)
			)
		);
	}

	/**
	 * Sanitize Shop Layout control.
	 *
	 * @param string $value Control output.
	 *
	 * @return string
	 */
	function sanitize_shop_settings_control( $value ) {
		$value        = sanitize_text_field( $value );
		$valid_values = array(
			'boxed',
			'plain',
		);

		if ( ! in_array( $value, $valid_values, true ) ) {
			wp_die( 'Invalid value, go back and try again.' );
		}

		return $value;
	}
}
