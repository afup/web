<?php
/**
 * ThemeIsle - White Label Admin Class
 * @package ti-white-label
 */

/**
 * Class Ti_Withe_Label_Admin
 */
class Ti_Withe_Label_Admin {

	/**
	 * Details of the product where the module is called.
	 *
	 * @var array $product_settings Product details.
	 */
	private $product_details;

	/**
	 * White label data.
	 *
	 * @var Ti_White_Label_Data $data Forms data.
	 */
	private $data;


	/**
	 * Ti_Withe_Label_Admin constructor.
	 *
	 * @var array $settings Product
	 */
	public function __construct( $settings ) {
		$this->product_details = $settings;

		require_once __DIR__ . '/class-ti-white-label-data.php';
		$this->data = new Ti_White_Label_Data();
	}

	/**
	 * Hooks and filters.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
	}

	/**
	 * Create the page for White Label module.
	 */
	public function register_sub_menu() {
		add_submenu_page(
			null,
			'White Label',
			'White Label',
			'manage_options',
			'ti-white-label',
			array(
				$this,
				'render_white_label_module',
			)
		);
	}

	/**
	 * Render White Label module.
	 */
	public function render_white_label_module() {
		$this->enqueue();
		echo '<div class="ti-wl-wrap__wrap">';
		echo '<app id="ti-lib-app"></app>';
		echo '</div>';
	}

	/**
	 * Enqueue script and styles.
	 */
	public function enqueue() {
		wp_enqueue_script( 'ti-white-label-app', $this->product_details['file_path'] . WHITE_LABEL_PATH . '/assets/js/bundle.js', array(), WHITE_LABEL_VERSION, true );
		wp_localize_script( 'ti-white-label-app', 'tiWhiteLabelLib', $this->localize_sites_library() );

	}

	/**
	 * Localize the sites library.
	 *
	 * @return array
	 */
	private function localize_sites_library() {
		$api = array(
			'root'     => esc_url_raw( rest_url( WHITE_LABEL_NAMESPACE . '/v1' ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'i18ln'    => $this->get_strings(),
			'fields'   => $this->data->get_input_fields(),
			'settings' => $this->product_details,
		);

		return $api;
	}

	/**
	 * Get strings.
	 *
	 * @return array
	 */
	private function get_strings() {

		$white_label_description = sprintf( __( 'This will remove the white label module from the dashboard. If you want to access white label settings in future, simply deactivate the %s plugin and activate it again.', 'hestia-pro' ), $this->product_details['product_name'] );
		if ( $this->product_details['type'] === 'theme' ) {
			$white_label_description = sprintf( __( 'This will remove the white label module from customizer. If you want to access white label settings in future, simply change the %s theme and activate it again.', 'hestia-pro' ), $this->product_details['product_name'] );
		}

		return array(
			'block_form_title_agency'             => __( 'Agency Branding', 'hestia-pro' ),
			'block_form_title_theme'              => __( 'Theme Branding', 'hestia-pro' ),
			'block_form_title_plugin'             => __( 'Plugin Branding', 'hestia-pro' ),
			'block_form_title_enable_white_label' => __( 'Enable White Label', 'hestia-pro' ),
			'white_label_description'             => $white_label_description,
			'license_field_label'                 => __( 'Enable License Hiding', 'hestia-pro' ),
			'license_field_description'           => __( 'This will remove the license field from the Settings page and all the admin notices related to it.', 'hestia-pro' ),
			'agency_author_label'                 => __( 'Agency Author', 'hestia-pro' ),
			'agency_author_url_label'             => __( 'Agency Author URL', 'hestia-pro' ),
			'agency_starter_sites_label'          => __( 'Hide Sites Library', 'hestia-pro' ),
			'theme_name_label'                    => __( 'Theme Name', 'hestia-pro' ),
			'theme_description_label'             => __( 'Theme Description', 'hestia-pro' ),
			'screenshot_url_label'                => __( 'Screenshot URL', 'hestia-pro' ),
			'plugin_name_label'                   => __( 'Plugin Name', 'hestia-pro' ),
			'plugin_description_label'            => __( 'Plugin Description', 'hestia-pro' ),
			'submit_button_label'                 => __( 'Save Changes', 'hestia-pro' ),
			'not_valid'                           => __( 'is not valid', 'hestia-pro' ),
		);
	}
}
