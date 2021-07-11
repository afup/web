<?php
/**
 * Color controls addon.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Color_Controls_Addon
 */
class Hestia_Color_Controls_Addon extends Hestia_Register_Customizer_Controls {
	/**
	 * Add controls
	 */
	public function add_controls() {
		$controls_to_add = array(
			'secondary_color'               => array(
				'setting' => array(
					'default'           => '#2d3359',
					'transport'         => $this->selective_refresh,
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Secondary Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 15,
				),
			),
			'body_color'                    => array(
				'setting' => array(
					'default'           => '#999999',
					'transport'         => $this->selective_refresh,
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Body Text Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 25,
				),
			),
			'header_overlay_color'          => array(
				'setting' => array(
					'default'           => apply_filters( 'hestia_overlay_color_default', 'rgba(0,0,0,0.5)' ),
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Header Overlay Color & Opacity', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 30,
				),
			),
			'header_text_color'             => array(
				'setting' => array(
					'default'           => '#fff',
					'transport'         => $this->selective_refresh,
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Header / Slider Text Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 35,
				),
			),
			'navbar_background_color'       => array(
				'setting' => array(
					'default'           => '#fff',
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Navbar Background Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 40,
				),
			),
			'navbar_text_color'             => array(
				'setting' => array(
					'default'           => '#555',
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Navbar Text Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 45,
				),
			),
			'navbar_text_color_hover'       => array(
				'setting' => array(
					'default'           => '#e91e63',
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Navbar Text Color on Hover', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 50,
				),
			),
			'navbar_transparent_text_color' => array(
				'setting' => array(
					'default'           => '#fff',
					'sanitize_callback' => 'hestia_sanitize_colors',
				),
				'control' => array(
					'label'        => esc_html__( 'Transparent Navbar Text Color', 'hestia-pro' ),
					'section'      => 'colors',
					'show_opacity' => true,
					'palette'      => false,
					'priority'     => 55,
				),
			),
		);

		foreach ( $controls_to_add as $control_id => $settings ) {
			$this->add_control(
				new Hestia_Customizer_Control(
					$control_id,
					$settings['setting'],
					$settings['control'],
					'Hestia_Customize_Alpha_Color_Control'
				)
			);
		}

	}

}
