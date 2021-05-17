<?php
/**
 * Customizer color controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Color_Controls
 */
class Hestia_Color_Controls extends Hestia_Register_Customizer_Controls {
	/**
	 * Add controls
	 */
	public function add_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'accent_color',
				array(
					'default'           => apply_filters( 'hestia_accent_color_default', '#e91e63' ),
					'transport'         => $this->selective_refresh,
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				array(
					'label'    => esc_html__( 'Accent Color', 'hestia-pro' ),
					'section'  => 'colors',
					'palette'  => apply_filters( 'hestia_accent_color_palette', false ),
					'priority' => 10,
				),
				'Hestia_Customize_Alpha_Color_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_gradient_color',
				array(
					'default'           => apply_filters( 'hestia_header_gradient_default', '#a81d84' ),
					'transport'         => 'postMessage',
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				array(
					'label'    => esc_html__( 'Header Gradient', 'hestia-pro' ),
					'section'  => 'header_image',
					'priority' => 30,
				),
				'WP_Customize_Color_Control'
			)
		);
	}
}
