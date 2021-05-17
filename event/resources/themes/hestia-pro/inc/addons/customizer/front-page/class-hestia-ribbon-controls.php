<?php
/**
 * Ribbon section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Ribbon_Section_Controls
 */
class Hestia_Ribbon_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_customizer_section();
		$this->add_hide_control();
		$this->add_background_image_control();
		$this->add_content_controls();
	}

	/**
	 * Add the customizer section.
	 */
	private function add_customizer_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_ribbon',
				array(
					'title'          => esc_html__( 'Ribbon', 'hestia-pro' ),
					'panel'          => 'hestia_frontpage_sections',
					'priority'       => apply_filters( 'hestia_section_priority', 40, 'hestia_ribbon' ),
					'hiding_control' => 'hestia_ribbon_hide',
				),
				'Hestia_Hiding_Section'
			)
		);
	}

	/**
	 * Add the hide control.
	 */
	private function add_hide_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_ribbon_hide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => true,
					'transport'         => $this->selective_refresh,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia-pro' ),
					'section'  => 'hestia_ribbon',
					'priority' => 1,
				)
			)
		);
	}

	/**
	 * Add background image control.
	 */
	private function add_background_image_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_ribbon_background',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'default'           => get_template_directory_uri() . '/assets/img/contact.jpg',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Background Image', 'hestia-pro' ),
					'section'  => 'hestia_ribbon',
					'priority' => 5,
				),
				'WP_Customize_Image_Control'
			)
		);
	}

	/**
	 * Add button controls.
	 */
	private function add_content_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_ribbon_text',
				array(
					'sanitize_callback' => 'wp_kses_post',
					'default'           => esc_html__( 'Subscribe to our Newsletter', 'hestia-pro' ),
					'transport'         => $this->selective_refresh,
				),
				array(
					'type'     => 'textarea',
					'label'    => esc_html__( 'Text', 'hestia-pro' ),
					'section'  => 'hestia_ribbon',
					'priority' => 10,
				)
			)
		);
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_ribbon_button_text',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => esc_html__( 'Subscribe', 'hestia-pro' ),
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Button Text', 'hestia-pro' ),
					'section'  => 'hestia_ribbon',
					'priority' => 15,
				)
			)
		);
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_ribbon_button_url',
				array(
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
					'default'           => '#',
				),
				array(
					'label'    => esc_html__( 'Link', 'hestia-pro' ),
					'section'  => 'hestia_ribbon',
					'priority' => 20,
				)
			)
		);
	}

}
