<?php
/**
 * Subscribe controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Subscribe_Controls
 */
class Hestia_Subscribe_Controls extends Hestia_Front_Page_Section_Controls_Abstract {

	/**
	 * Add section details
	 *
	 * @return array
	 */
	protected function set_section_data() {
		return array(
			'slug'             => 'subscribe',
			'title'            => esc_html__( 'Subscribe', 'hestia-pro' ),
			'priority'         => 55,
			'initially_hidden' => true,
			'section'          => 'sidebar-widgets-subscribe-widgets',
		);

	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_tabs();
		$this->add_info_control();
		$this->add_background_control();
	}

	/**
	 * Add section tabs/
	 */
	private function add_tabs() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_subscribe_tabs',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'  => 'sidebar-widgets-subscribe-widgets',
					'tabs'     => array(
						'general'    => array(
							'label' => esc_html__( 'General Settings', 'hestia-pro' ),
						),
						'sendinblue' => array(
							'label' => esc_html__( 'SendinBlue plugin', 'hestia-pro' ),
						),
					),
					'controls' => array(
						'general'    => array(
							'hestia_subscribe_hide'       => array(),
							'hestia_subscribe_background' => array(),
							'hestia_subscribe_title'      => array(),
							'hestia_subscribe_subtitle'   => array(),
						),
						'sendinblue' => array(
							'hestia_subscribe_info' => array(),
							'widgets'               => array(),
						),

					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);
	}

	/**
	 * Add background control.
	 */
	private function add_background_control() {

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_subscribe_background',
				array(
					'default'           => get_template_directory_uri() . '/assets/img/about.jpg',
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Background Image', 'hestia-pro' ),
					'section'  => 'sidebar-widgets-subscribe-widgets',
					'priority' => 10,
				),
				'WP_Customize_Image_Control'
			)
		);
	}

	/**
	 * Add the info control.
	 */
	private function add_info_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_subscribe_info',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'      => esc_html__( 'Instructions', 'hestia-pro' ),
					'section'    => 'sidebar-widgets-subscribe-widgets',
					'capability' => 'install_plugins',
					'priority'   => 25,
				),
				'Hestia_Subscribe_Info'
			)
		);
	}

	/**
	 * Change any controls that may need change.
	 *
	 * @return void
	 */
	public function change_controls() {
		$this->change_customizer_object( 'section', 'sidebar-widgets-subscribe-widgets', 'panel', 'hestia_frontpage_sections' );
		$this->change_customizer_object( 'section', 'sidebar-widgets-subscribe-widgets', 'priority', apply_filters( 'hestia_section_priority', 55, 'sidebar-widgets-subscribe-widgets' ) );
		$this->change_customizer_object( 'setting', 'hestia_subscribe_title', 'default', esc_html__( 'Subscribe to our Newsletter', 'hestia-pro' ) );
		$this->change_customizer_object( 'setting', 'hestia_subscribe_subtitle', 'default', esc_html__( 'Change this subtitle in the Customizer', 'hestia-pro' ) );
		$controls_to_move = array(
			'hestia_subscribe_subtitle',
			'hestia_subscribe_title',
			'hestia_subscribe_background',
			'hestia_subscribe_hide',
			'hestia_subscribe_info',
			'hestia_subscribe_tabs',
		);

		foreach ( $controls_to_move as $index => $control_id ) {
			$this->change_customizer_object( 'control', $control_id, 'priority', -$index );
		}
	}
}
