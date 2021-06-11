<?php
/**
 * The customizer notices manager extension.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Customizer_Notices_Addon
 */
class Hestia_Customizer_Notices_Addon extends Hestia_Customizer_Notices {

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		parent::add_controls();
		$this->maybe_add_jetpack_notice();
	}

	/**
	 * Maybe add WooCommerce notice.
	 */
	private function maybe_add_jetpack_notice() {
		if ( class_exists( 'Jetpack', false ) && post_type_exists( 'jetpack-portfolio' ) ) {
			return;
		}

		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_info_jetpack',
				array(
					'section_text'        =>
						sprintf(
							/* translators: %1$s is Jetpack Plugin */
							esc_html__( 'To have access to a portfolio section please install and configure %1$s.', 'hestia-pro' ),
							esc_html__( 'Jetpack plugin', 'hestia-pro' )
						),
					'slug'                => 'jetpack',
					'panel'               => 'hestia_frontpage_sections',
					'priority'            => 450,
					'capability'          => 'install_plugins',
					'hide_notice'         => (bool) get_option( 'dismissed-hestia_info_jetpack', false ),
					'options'             => array(
						'redirect' => admin_url( 'customize.php' ) . '?autofocus[panel]=hestia_frontpage_sections',
					),
					'button_screenreader' => '',
				),
				'Hestia_Generic_Notice_Section'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_control_to_enable_jetpack_recommendation',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section' => 'hestia_info_jetpack',
					'type'    => 'hidden',
				)
			)
		);
	}

	/**
	 * Change docs section.
	 */
	public function change_controls() {
		$theme_name = apply_filters( 'ti_wl_theme_name', esc_html__( 'Hestia Pro', 'hestia-pro' ) );
		$url        = apply_filters( 'ti_wl_agency_url', 'http://docs.themeisle.com/article/532-hestia-pro-documentation' );
		$this->change_customizer_object( 'section', 'hestia_docs_section', 'theme_info_title', $theme_name );
		$this->change_customizer_object( 'section', 'hestia_docs_section', 'label_url', esc_url( $url ) );
		$this->change_customizer_object( 'section', 'hestia_info_obfx', 'description', esc_html__( 'Extend your theme functionality with various modules like Social Media Share Buttons & Icons, custom menu-icons, one click import page templates, page builder addons and free stock featured images.', 'hestia-pro' ) );
	}
}
