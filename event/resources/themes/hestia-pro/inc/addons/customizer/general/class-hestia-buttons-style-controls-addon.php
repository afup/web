<?php
/**
 * Customizer buttons controls.
 *
 * @package Hestia
 */


/**
 * Class Hestia_Buttons_Style_Controls_Addon
 */
class Hestia_Buttons_Style_Controls_Addon extends Hestia_Buttons_Style_Controls {

	/**
	 * Main add controls method.
	 */
	public function add_controls() {
		parent::add_controls();
		$this->add_buttons_additional_controls();
	}

	/**
	 * Add buttons style additional controls.
	 */
	private function add_buttons_additional_controls() {
		/**
		 * Hover effect
		 */
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_buttons_hover_effect',
				array(
					'default'           => 'shadow',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'type'     => 'select',
					'label'    => esc_html__( 'Hover effect', 'hestia-pro' ),
					'section'  => 'hestia_buttons_style',
					'priority' => 25,
					'choices'  => array(
						'shadow' => esc_html__( 'Shadow', 'hestia-pro' ),
						'color'  => esc_html__( 'Color', 'hestia-pro' ),
					),
				)
			)
		);
	}
}
