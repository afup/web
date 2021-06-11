<?php
/**
 * ThemeIsle - About page class
 *
 * @package ti-about-page
 */

/**
 * Class Ti_About_Page_Main
 *
 * @package Themeisle
 */
class Ti_About_Page {

	/**
	 * @var
	 * About Page instance
	 */
	public static $instance;
	/**
	 * @var
	 * About page content that should be rendered
	 */
	public $config = array();
	/**
	 * @var
	 * Current theme args
	 */
	private $theme_args = array();

	/**
	 * The Main Themeisle_About_Page instance.
	 *
	 * We make sure that only one instance of Themeisle_About_Page exists in the memory at one time.
	 *
	 * @param array $config The configuration array.
	 */
	public static function init( $config ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Ti_About_Page ) ) {
			self::$instance = new Ti_About_Page();
			if ( ! empty( $config ) && is_array( $config ) ) {
				self::$instance->config = apply_filters( 'ti_about_config_filter', $config );
				self::$instance->setup_config();
				self::$instance->setup_actions();
				self::$instance->set_recommended_plugins_visibility();
			}
		}
	}

	/**
	 * Setup the class props based on current theme
	 */
	private function setup_config() {

		$theme = wp_get_theme();

		$this->theme_args['name']        = apply_filters( 'ti_wl_theme_name', $theme->__get( 'Name' ) );
		$this->theme_args['template']    = $theme->get( 'Template' );
		$this->theme_args['version']     = $theme->__get( 'Version' );
		$this->theme_args['description'] = apply_filters( 'ti_wl_theme_description', $theme->__get( 'Description' ) );
		$this->theme_args['slug']        = $theme->__get( 'stylesheet' );
	}

	/**
	 * Setup the actions used for this page.
	 */
	public function setup_actions() {

		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action(
			'wp_ajax_update_recommended_plugins_visibility',
			array(
				$this,
				'update_recommended_plugins_visibility',
			)
		);

		if ( ! class_exists( 'Ti_Notice_Manager' ) && isset( $this->config['welcome_notice'] ) ) {
			require_once 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-ti-notice-manager.php';
			add_action( 'init', array( Ti_Notice_Manager::instance(), 'init' ) );
		}
	}

	/**
	 * Set an option with recommended plugins slugs and visibility
	 * Based on visibility flag the plugin should be shown/hidden in recommended_plugins tab
	 */
	public function set_recommended_plugins_visibility() {
		$recommended_plugins = get_theme_mod( 'ti_about_recommended_plugins' );
		if ( ! empty( $recommended_plugins ) ) {
			return;
		}
		$required_plugins           = $this->get_recommended_plugins();
		$required_plugins_visbility = array();
		foreach ( $required_plugins as $slug => $req_plugin ) {
			$required_plugins_visbility[ $slug ] = 'visible';
		}
		set_theme_mod( 'ti_about_recommended_plugins', $required_plugins_visbility );
	}

	/**
	 * Get the list of recommended plugins
	 *
	 * @return array - either recommended plugins or empty array.
	 */
	public function get_recommended_plugins() {
		foreach ( $this->config as $index => $content ) {
			if ( isset( $content['type'] ) && $content['type'] === 'recommended_actions' ) {
				$plugins = $content['plugins'];

				return $plugins;
				break;
			}
		}

		return array();
	}

	/**
	 * Register the menu page under Appearance menu.
	 */
	public function register() {
		$theme = $this->theme_args;

		if ( empty( $theme['name'] ) || empty( $theme['slug'] ) ) {
			return;
		}

		$page_title = $theme['name'] . ' ' . __( 'Options', 'hestia-pro' ) . ' ';

		$menu_name        = $theme['name'] . ' ' . __( 'Options', 'hestia-pro' ) . ' ';
		$required_actions = $this->get_recommended_actions_left();
		if ( $required_actions > 0 ) {
			$menu_name .= '<span class="badge-action-count update-plugins">' . esc_html( $required_actions ) . '</span>';
		}

		$theme_page = ! empty( $theme['template'] ) ? $theme['template'] . '-welcome' : $theme['slug'] . '-welcome';
		add_theme_page(
			$page_title,
			$menu_name,
			'activate_plugins',
			$theme_page,
			array(
				$this,
				'render',
			)
		);
	}

	/**
	 * Utility function for checking the number of recommended actions uncompleted
	 *
	 * @return int $actions_left - the number of uncompleted recommended actions.
	 */
	public function get_recommended_actions_left() {

		$nb_of_actions       = 0;
		$actions_left        = 0;
		$recommended_plugins = get_theme_mod( 'ti_about_recommended_plugins' );

		if ( ! empty( $recommended_plugins ) ) {
			foreach ( $recommended_plugins as $slug => $visibility ) {
				if ( $recommended_plugins[ $slug ] === 'visible' ) {
					$nb_of_actions += 1;

					if ( Ti_About_Plugin_Helper::instance()->check_plugin_state( $slug ) !== 'deactivate' ) {
						$actions_left += 1;
					}
				}
			}
		}

		return $actions_left;
	}

	/**
	 * Instantiate the render class which will render all the tabs based on config
	 */
	public function render() {
		require_once 'includes/class-ti-about-render.php';
		new TI_About_Render( $this->theme_args, $this->config, $this );
	}

	/**
	 * Load css and scripts for the about page
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( ! isset( $screen->id ) ) {
			return;
		}
		$theme      = $this->theme_args;
		$theme_page = ! empty( $theme['template'] ) ? $theme['template'] . '-welcome' : $theme['slug'] . '-welcome';
		if ( $screen->id !== 'appearance_page_' . $theme_page ) {
			return;
		}

		wp_enqueue_style( 'ti-about-style', TI_ABOUT_PAGE_URL . 'assets/css/about.css', array(), TI_ABOUT_PAGE_VERSION );
		wp_register_script(
			'ti-about-scripts',
			TI_ABOUT_PAGE_URL . 'assets/js/script.js',
			array(
				'jquery',
			),
			TI_ABOUT_PAGE_VERSION,
			true
		);

		wp_localize_script(
			'ti-about-scripts',
			'tiAboutPageObject',
			array(
				'nr_actions_required' => $this->get_recommended_actions_left(),
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'ti-about-nonce' ),
				'template_directory'  => get_template_directory_uri(),
				'activating_string'   => esc_html__( 'Activating', 'hestia-pro' ),
			)
		);

		wp_enqueue_script( 'ti-about-scripts' );
		Ti_About_Plugin_Helper::instance()->enqueue_scripts();
	}

	/**
	 * Update recommended plugins visibility flag if the user dismiss one of them
	 */
	public function update_recommended_plugins_visibility() {
		$nonce = $_POST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'ti-about-nonce' ) ) {
			return;
		}
		$recommended_plugins = get_theme_mod( 'ti_about_recommended_plugins' );

		$plugin_to_update                         = $_POST['slug'];
		$recommended_plugins[ $plugin_to_update ] = 'hidden';

		set_theme_mod( 'ti_about_recommended_plugins', $recommended_plugins );

		$required_actions_left = array( 'required_actions' => $this->get_recommended_actions_left() );
		wp_send_json( $required_actions_left );
	}
}
