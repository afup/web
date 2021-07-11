<?php
/**
 * Clients bar section controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Clients_Bar_Controls
 */
class Hestia_Clients_Bar_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_customizer_section();
		$this->add_hide_control();
		$this->add_content_controls();
	}

	/**
	 * Add the customizer section.
	 */
	private function add_customizer_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_clients_bar',
				array(
					'title'          => esc_html__( 'Clients Bar', 'hestia-pro' ),
					'panel'          => 'hestia_frontpage_sections',
					'priority'       => apply_filters( 'hestia_section_priority', 50, 'hestia_clients_bar' ),
					'hiding_control' => 'hestia_clients_bar_hide',
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
				'hestia_clients_bar_hide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => true,
					'transport'         => $this->selective_refresh,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia-pro' ),
					'section'  => 'hestia_clients_bar',
					'priority' => 1,
				)
			)
		);
	}

	/**
	 * Add content controls.
	 */
	private function add_content_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_clients_bar_content',
				array(
					'sanitize_callback' => 'hestia_repeater_sanitize',
					'transport'         => $this->selective_refresh,
					'default'           => apply_filters( 'hestia_clients_bar_default_content', false ),
				),
				array(
					'label'                             => esc_html__( 'Clients Bar Content', 'hestia-pro' ),
					'section'                           => 'hestia_clients_bar',
					'priority'                          => 5,
					'item_name'                         => esc_html__( 'Clients', 'hestia-pro' ),
					'customizer_repeater_image_control' => true,
					'customizer_repeater_link_control'  => true,
				),
				'Hestia_Repeater',
				array(
					'selector'            => '.hestia-clients-bar',
					'container_inclusive' => true,
					'render_callback'     => array( $this, 'clients_render_callback' ),
				)
			)
		);
	}

	/**
	 * The render callback function for this section.
	 */
	public function clients_render_callback() {
		$section = new Hestia_Clients_Bar_Section();
		return $section->render_section();
	}

}
