<?php
/**
 * Typography controls addon.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Typography_Controls_Addon
 */
class Hestia_Typography_Controls_Addon extends Hestia_Typography_Controls {

	/**
	 * Main add controls method.
	 */
	public function add_controls() {
		parent::add_controls();
		$this->add_additional_heading();
		$this->add_menu_font_size_control();
	}

	/**
	 * Enable responsive controls for all font size controls in Hestia PRO.
	 */
	public function change_controls() {

		$controls = array(
			'hestia_post_page_headings_fs',
			'hestia_post_page_content_fs',
			'hestia_big_title_fs',
			'hestia_section_primary_headings_fs',
			'hestia_section_secondary_headings_fs',
			'hestia_section_content_fs',
		);

		foreach ( $controls as $control ) {
			$this->change_customizer_object( 'control', $control, 'media_query', true );
		}
	}

	/**
	 * Heading control that is displayed before generic font sizes controls.
	 */
	private function add_additional_heading() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_generic_title',
				array(
					'sanitize_callback' => 'wp_kses',
				),
				array(
					'label'    => esc_html__( 'Generic options', 'hestia-pro' ),
					'section'  => 'hestia_typography',
					'priority' => 300,
				),
				'Hestia_Customizer_Heading'
			)
		);
	}

	/**
	 * Menu font size control
	 * This control allow users to choose a font size for the menu in header
	 * The values area between -25 and +25 px.
	 */
	private function add_menu_font_size_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_menu_fs',
				array(
					'sanitize_callback' => 'hestia_sanitize_range_value',
					'default'           => 0,
					'transport'         => 'postMessage',
				),
				array(
					'label'       => esc_html__( 'Menu', 'hestia-pro' ),
					'section'     => 'hestia_typography',
					'type'        => 'range-value',
					'input_attr'  => array(
						'min'  => - 25,
						'max'  => 25,
						'step' => 1,
					),
					'priority'    => 310,
					'media_query' => true,
					'sum_type'    => true,
				),
				'Hestia_Customizer_Range_Value_Control'
			)
		);
	}
}
