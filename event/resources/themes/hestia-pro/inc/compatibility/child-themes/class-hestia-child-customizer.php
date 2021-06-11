<?php
/**
 * Customizer compatibility for Orfeo Child Theme
 *
 * @package Hestia
 */

/**
 * Class Hestia_Child_Customizer
 */
class Hestia_Child_Customizer extends Hestia_Register_Customizer_Controls {

	/**
	 * Run init only if Orfeo is installed
	 */
	public function init() {

		if ( ! is_child_theme() ) {
			return;
		}

		if ( ( wp_get_theme()->Template === 'hestia' ) && ( wp_get_theme()->Name === 'Orfeo' || wp_get_theme()->Name === 'Orfeo Pro' ) ) {
			add_action( 'customize_register', array( $this, 'register_controls_callback' ), 100 );
		}
	}

	/**
	 * Implement abstract from parent.
	 */
	public function add_controls() {
	}

	/**
	 * Change controls from child-theme.
	 */
	public function change_controls() {
		$this->change_customizer_object( 'control', 'orfeo_big_title_second_button_text', 'section', 'sidebar-widgets-sidebar-big-title' );
		$this->change_customizer_object( 'control', 'orfeo_big_title_second_button_text', 'priority', 40 );
		$this->change_customizer_object( 'control', 'orfeo_big_title_second_button_link', 'section', 'sidebar-widgets-sidebar-big-title' );
		$this->change_customizer_object( 'control', 'orfeo_big_title_second_button_link', 'priority', 45 );
		$this->change_customizer_object(
			'control',
			'hestia_slider_tabs',
			'controls',
			array(
				'slider' => array(
					'hestia_big_title_upsell' => array(),
					'hestia_big_title_hide'   => array(),
					'hestia_slider_type'      => array(
						'image'    => array(
							'hestia_big_title_background',
							'hestia_big_title_title',
							'hestia_big_title_text',
							'hestia_big_title_button_text',
							'hestia_big_title_button_link',
							'orfeo_big_title_second_button_text',
							'orfeo_big_title_second_button_link',
						),
						'parallax' => array(
							'hestia_parallax_layer1',
							'hestia_parallax_layer2',
							'hestia_big_title_title',
							'hestia_big_title_text',
							'hestia_big_title_button_text',
							'hestia_big_title_button_link',
							'orfeo_big_title_second_button_text',
							'orfeo_big_title_second_button_link',

						),
					),
				),
				'extra'  => array(
					'hestia_slider_alignment' => array(
						'left'   => array(
							'hestia_big_title_widgets_title',
							'widgets',
						),
						'center' => array(),
						'right'  => array(
							'hestia_big_title_widgets_title',
							'widgets',
						),
					),
				),
			)
		);

	}

}
