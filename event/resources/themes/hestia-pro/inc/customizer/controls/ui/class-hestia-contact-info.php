<?php
/**
 * A custom text control for Contact info.
 *
 * @package Hestia
 * @since Hestia 1.1.10
 */

/**
 * A custom text control for Contact info.
 *
 * @since Hestia 1.0
 */
class Hestia_Contact_Info extends WP_Customize_Control {

	/**
	 * Enqueue function.
	 */
	public function enqueue() {
		Hestia_Plugin_Install_Helper::instance()->enqueue_scripts();
	}

	/**
	 * Render content for the control.
	 *
	 * @since Hestia 1.0
	 */
	public function render_content() {
		if ( ! defined( 'WPFORMS_VERSION' ) ) {

			echo '<span class="customize-control-title">' . esc_html__( 'Instructions', 'hestia-pro' ) . '</span>';
			echo $this->create_plugin_install_button(
				'wpforms-lite',
				array(
					'redirect'    => admin_url( 'customize.php' ) . '?autofocus[control]=hestia_contact_form_shortcode',
					'plugin_name' => 'WPForms Lite',
				)
			);
		}
	}

	/**
	 * Create plugin install button.
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return bool
	 */
	public function create_plugin_install_button( $slug, $settings = array() ) {
		return Hestia_Plugin_Install_Helper::instance()->get_button_html( $slug, $settings );
	}
}
