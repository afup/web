<?php
/**
 * Footer customizer controls manager.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Footer_Controls_Addon
 */
class Hestia_Footer_Controls extends Hestia_Register_Customizer_Controls {
	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->add_footer_options_section();
		$this->add_footer_copyright_control();
	}

	/**
	 * Add the footer options section.
	 */
	private function add_footer_options_section() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_footer_content',
				array(
					'title'    => esc_html__( 'Footer Options', 'hestia-pro' ),
					'priority' => 36,
				)
			)
		);
	}

	/**
	 * Add the footer copyright control.
	 */
	private function add_footer_copyright_control() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_general_credits',
				array(
					'default'           =>
						sprintf(
							/* translators: %1$s is Theme name wrapped in <a> tag, %2$s is WordPress link */
							esc_html__( '%1$s | Powered by %2$s', 'hestia-pro' ),
							/* translators: %s is Theme name */
							sprintf(
								'<a href="https://themeisle.com/themes/hestia/" target="_blank" rel="nofollow">%s</a>',
								esc_html__( 'Hestia', 'hestia-pro' )
							),
							/* translators: %s is WordPress */
							sprintf( '<a href="http://wordpress.org/" rel="nofollow">%s</a>', esc_html__( 'WordPress', 'hestia-pro' ) )
						),
					'sanitize_callback' => 'wp_kses_post',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'    => esc_html__( 'Footer Credits', 'hestia-pro' ),
					'section'  => 'hestia_footer_content',
					'priority' => 25,
					'type'     => 'textarea',
				),
				null,
				array(
					'selector'        => 'footer .hestia-bottom-footer-content .copyright',
					'settings'        => 'hestia_general_credits',
					'render_callback' => array( $this, 'copyright_callback' ),
				)
			)
		);
	}


	/**
	 * Callback function for Copyright control.
	 *
	 * @return string
	 * @since 1.1.34
	 */
	public function copyright_callback() {
		return get_theme_mod( 'hestia_general_credits' );
	}
}
