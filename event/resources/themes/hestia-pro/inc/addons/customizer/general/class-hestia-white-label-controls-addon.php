<?php
/**
 * White label module link
 *
 * @package Hestia
 */
/**
 * Class Hestia_White_Label_Controls_Addon
 */
class Hestia_White_Label_Controls_Addon extends Hestia_Register_Customizer_Controls {

	/**
	 * Initialize the control. Add all the hooks necessary.
	 */
	public function init() {
		parent::init();
		add_action( 'ti_change_customizer_controls', array( $this, 'change_white_label_controls' ) );
	}

	/**
	 * Remove documentation section in customizer.
	 *
	 * @return bool
	 */
	public function change_white_label_controls() {
		if ( ! class_exists( 'Ti_White_Label_Markup' ) ) {
			return false;
		}

		if ( ! Ti_White_Label_Markup::is_theme_whitelabeld() ) {
			return false;
		}

		global $wp_customize;
		$docs_section = $wp_customize->get_section( 'hestia_docs_section' );
		if ( ! empty( $docs_section ) ) {
			$wp_customize->remove_section( 'hestia_docs_section' );
		}

		return true;
	}

	/**
	 * Main add controls method.
	 */
	public function add_controls() {
		if ( ! $this->should_load() ) {
			return;
		}
		$this->add_white_label_section();
		$this->add_white_label_controls();
	}
	/**
	 * Decide if the section should appear in customizer.
	 *
	 * @return bool
	 */
	private function should_load() {
		$license_data = get_option( 'hestia_pro_license_data' );
		if ( $license_data->license !== 'valid' ) {
			return false;
		}
		$plan = $license_data->plan;
		if ( $plan !== 3 ) {
			return false;
		}

		$white_label_settings = get_option( 'ti_white_label_inputs' );
		if ( empty( $white_label_settings ) ) {
			return false;
		}
		$white_label_settings  = json_decode( $white_label_settings, true );
		$white_label_is_hidden = $white_label_settings['white_label'];
		if ( $white_label_is_hidden === true ) {
			return false;
		}
		return class_exists( 'Ti_White_Label', false ) ? true : false;
	}
	/**
	 * Add white label section.
	 */
	private function add_white_label_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_white_label',
				array(
					'title'    => apply_filters( 'hestia_white_label_section_label', esc_html__( 'White Label', 'hestia-pro' ) ),
					'priority' => 150,
				)
			)
		);
	}
	/**
	 * Add white label control.
	 */
	private function add_white_label_controls() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_white_label_shortcut',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'description' => esc_html__( 'Using the White Label module provided by Hestia Pro, you can easily rename and present the theme as your own. White labeling is mostly used by agencies and developers who are building websites for their own clients and want to prove they are the developers of the theme.', 'hestia-pro' ),
					'priority'    => 1,
					'section'     => 'hestia_white_label',
					'button_text' => esc_html__( 'White Label Settings', 'hestia-pro' ),
					'link'        => admin_url( '?page=ti-white-label' ),
				),
				'Hestia_Button'
			)
		);
	}
}
