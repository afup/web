<?php
/**
 * Handles admin logic for the onboarding.
 *
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 * @soundtrack Smell the Roses - Roger Waters
 */

/**
 * Class Themeisle_OB_Admin
 *
 * @package themeisle-onboarding
 */
class Themeisle_OB_Admin {

	/**
	 * Initialize the Admin.
	 */
	public function init() {
		add_filter( 'query_vars', array( $this, 'add_onboarding_query_var' ) );
		add_filter( 'ti_about_config_filter', array( $this, 'add_demo_import_tab' ), 15 );
		add_action( 'after_switch_theme', array( $this, 'get_previous_theme' ) );
	}

	/**
	 * Memorize the previous theme to later display the import template for it.
	 */
	public function get_previous_theme() {
		$previous_theme = strtolower( get_option( 'theme_switched' ) );
		set_theme_mod( 'ti_prev_theme', $previous_theme );
	}

	/**
	 * Add our onboarding query var.
	 *
	 * @param array $vars_array the registered query vars.
	 *
	 * @return array
	 */
	public function add_onboarding_query_var( $vars_array ) {
		array_push( $vars_array, 'onboarding' );

		return $vars_array;
	}

	/**
	 * Add about page tab list item.
	 *
	 * @param array $config about page config.
	 *
	 * @return array
	 */
	public function add_demo_import_tab( $config ) {
		$config['custom_tabs']['sites_library'] = array(
			'title'           => __( 'Sites Library', 'hestia-pro' ),
			'render_callback' => array(
				$this,
				'add_demo_import_tab_content',
			),
		);

		return $config;
	}

	/**
	 * Add about page tab content.
	 */
	public function add_demo_import_tab_content() {
		?>
		<div id="<?php echo esc_attr( 'demo-import' ); ?>">
			<?php $this->render_site_library(); ?>
		</div>
		<?php
	}

	/**
	 * Render the sites library.
	 */
	public function render_site_library() {
		if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
			echo '<div>' . apply_filters( 'themeisle_onboarding_phprequired_text', 'ti_ob_err_phpv_less_than_5-4-0' ) . '</div>';

			return;
		}

		if ( apply_filters( 'ti_onboarding_filter_module_status', true ) !== true ) {
			return;
		}

		$this->enqueue();
		?>
		<div class="ti-sites-lib__wrap">
			<div id="ti-sites-library">
				<app></app>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue script and styles.
	 */
	public function enqueue() {

		wp_register_script( 'themeisle-site-lib', Themeisle_Onboarding::get_dir() . '/assets/js/bundle.js', array(), Themeisle_Onboarding::VERSION, true );

		wp_localize_script( 'themeisle-site-lib', 'themeisleSitesLibApi', $this->localize_sites_library() );

		wp_enqueue_script( 'themeisle-site-lib' );
	}

	/**
	 * Get return steps.
	 *
	 * @return array Import steps.
	 */
	private function get_import_steps() {
		return array(
			'plugins'    => array(
				'nicename' => __( 'Installing Plugins', 'hestia-pro' ),
				'done'     => 'no',
			),
			'content'    => array(
				'nicename' => __( 'Importing Content', 'hestia-pro' ),
				'done'     => 'no',
			),
			'theme_mods' => array(
				'nicename' => __( 'Setting Up Customizer', 'hestia-pro' ),
				'done'     => 'no',
			),
			'widgets'    => array(
				'nicename' => __( 'Importing Widgets', 'hestia-pro' ),
				'done'     => 'no',
			),
		);
	}

	/**
	 * Localize the sites library.
	 *
	 * @return array
	 */
	private function localize_sites_library() {

		$theme = wp_get_theme();

		$api = array(
			'root'            => esc_url_raw( rest_url( Themeisle_Onboarding::API_ROOT ) ),
			'nonce'           => wp_create_nonce( 'wp_rest' ),
			'homeUrl'         => esc_url( home_url() ),
			'i18ln'           => $this->get_strings(),
			'onboarding'      => 'no',
			'readyImport'     => '',
			'contentImported' => $this->escape_bool_text( get_theme_mod( 'ti_content_imported', 'no' ) ),
			'aboutUrl'        => esc_url( admin_url( 'themes.php?page=' . $theme->__get( 'stylesheet' ) . '-welcome' ) ),
			'importSteps'     => $this->get_import_steps(),
			'logUrl'          => Themeisle_OB_WP_Import_Logger::get_instance()->get_log_url(),
		);

		$is_onboarding = isset( $_GET['onboarding'] ) && $_GET['onboarding'] === 'yes';
		if ( $is_onboarding ) {
			$api['onboarding'] = 'yes';
		}

		if ( isset( $_GET['readyimport'] ) ) {
			$api['readyImport'] = $_GET['readyimport'];
		}

		return $api;
	}

	/**
	 * Get strings.
	 *
	 * @return array
	 */
	private function get_strings() {
		return array(
			'preview_btn'                 => __( 'Preview', 'hestia-pro' ),
			'import_btn'                  => __( 'Import', 'hestia-pro' ),
			'pro_btn'                     => __( 'Get the PRO version!', 'hestia-pro' ),
			'importing'                   => __( 'Importing', 'hestia-pro' ),
			'cancel_btn'                  => __( 'Cancel', 'hestia-pro' ),
			'loading'                     => __( 'Loading', 'hestia-pro' ),
			'go_to_site'                  => __( 'View Website', 'hestia-pro' ),
			'edit_template'               => __( 'Add your own content', 'hestia-pro' ),
			'back'                        => __( 'Back to Sites Library', 'hestia-pro' ),
			'note'                        => __( 'Note', 'hestia-pro' ),
			'advanced_options'            => __( 'Advanced Options', 'hestia-pro' ),
			'plugins'                     => __( 'Plugins', 'hestia-pro' ),
			'general'                     => __( 'General', 'hestia-pro' ),
			'later'                       => __( 'Keep current layout', 'hestia-pro' ),
			'search'                      => __( 'Search', 'hestia-pro' ),
			'content'                     => __( 'Content', 'hestia-pro' ),
			'customizer'                  => __( 'Customizer', 'hestia-pro' ),
			'widgets'                     => __( 'Widgets', 'hestia-pro' ),
			'backup_disclaimer'           => __( 'We recommend you backup your website content before attempting a full site import.', 'hestia-pro' ),
			'placeholders_disclaimer'     => __( 'Due to copyright issues, some of the demo images will not be imported and will be replaced by placeholder images.', 'hestia-pro' ),
			'placeholders_disclaimer_new' => __( 'Some of the demo images will not be imported and will be replaced by placeholder images.', 'hestia-pro' ),
			'images_gallery_link'       => __( 'Here is our own collection of related images you can use for your site.', 'hestia-pro' ),
			'import_done'                 => __( 'Content was successfully imported. Enjoy your new site!', 'hestia-pro' ),
			'pro_demo'                    => __( 'Available in the PRO version', 'hestia-pro' ),
			'copy_error_code'             => __( 'Copy error code', 'hestia-pro' ),
			'download_error_log'          => __( 'Download error log', 'hestia-pro' ),
			'external_plugins_notice'     => __( 'To import this demo you have to install the following plugins:', 'hestia-pro' ),
			'error_report'                => sprintf(
				__( 'Hi! It seems there is a configuration issue with your server that\'s causing the import to fail. Please %1$s with us with the error code below, so we can help you fix this.', 'hestia-pro' ),
				sprintf( '<a href="https://themeisle.com/contact">%1$s <i class="dashicons dashicons-external"></i></a>', __( 'get in touch', 'hestia-pro' ) )
			),
		);
	}

	/**
	 * Escape settings that return 'yes', 'no'.
	 *
	 * @param $value
	 *
	 * @return string
	 */
	private function escape_bool_text( $value ) {
		$allowed = array( 'yes', 'no' );

		if ( ! in_array( $value, $allowed, true ) ) {
			return 'no';
		}

		return esc_html( $value );
	}
}
