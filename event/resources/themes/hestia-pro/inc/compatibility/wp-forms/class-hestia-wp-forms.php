<?php
/**
 * Wp Forms compatibility class.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Wp_Forms
 */
class Hestia_Wp_Forms extends Hestia_Abstract_Main {

	/**
	 * Init function.
	 *
	 * @return bool|void
	 */
	public function init() {
		if ( ! defined( 'WPFORMS_VERSION' ) || defined( 'PIRATE_FORMS_VERSION' ) ) {
			return false;
		}

		add_action( 'themeisle_ob_after_customizer_import', array( $this, 'wpforms_after_theme_mods_import' ) );
		add_action( 'after_setup_theme', array( $this, 'wpforms_default_menu' ) );
	}

	/**
	 * Create WpForm after starter site import.
	 *
	 * @return bool|void
	 */
	public function wpforms_after_theme_mods_import() {
		$contact_shortcode = get_theme_mod( 'hestia_contact_form_shortcode' );
		if ( empty( $contact_shortcode ) ) {
			return false;
		}

		$form_id = $this->create_wp_form();
		if ( empty( $form_id ) ) {
			return false;
		}

		set_theme_mod( 'hestia_contact_form_shortcode', '[wpforms id="' . $form_id . '"]' );

		// No need to create another form.
		update_option( 'hestia_wpforms_default_menu', true );
	}

	/**
	 * Create and insert WPForm.
	 *
	 * @return bool | int
	 */
	private function create_wp_form() {

		require_once ABSPATH . '/wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();

		$form_config_file_path = get_template_directory() . '/inc/compatibility/wp-forms/wpforms-contact.json';
		$config_data           = $wp_filesystem->get_contents( $form_config_file_path );
		$config_data           = json_decode( $config_data, true );
		$config_data           = $config_data[0];

		if ( empty( $config_data ) ) {
			return false;
		}

		$form_id           = wpforms()->form->add( $config_data['settings']['form_title'] );
		$config_data['id'] = $form_id;
		wpforms()->form->update( $form_id, $config_data );

		return $form_id;
	}

	/**
	 * Example WPForms Setup.
	 *
	 * This is an example function would typically fire at the end of theme
	 * install/setup process (after demo content has been created, etc).
	 *
	 * @return bool|void
	 */
	public function wpforms_default_menu() {
		$contact_shorcode_with_default = get_theme_mod( 'hestia_contact_form_shortcode', 'default_value' );
		if ( $contact_shorcode_with_default !== 'default_value' ) {
			return false;
		}

		$setup = get_option( 'hestia_wpforms_default_menu' );
		if ( ! empty( $setup ) || ! function_exists( 'wpforms' ) ) {
			return false;
		}

		$form_id = $this->create_wp_form();
		if ( empty( $form_id ) ) {
			return false;
		}

		set_theme_mod( 'hestia_contact_form_shortcode', '[wpforms id="' . $form_id . '"]' );

		// Set an option to make sure it does not run again.
		update_option( 'hestia_wpforms_default_menu', true );
	}

}
