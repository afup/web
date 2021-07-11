<?php
/**
 * Add a Hook panel in the main WordPress menu
 *
 * Based on WordPress Settings API https://codex.wordpress.org/Settings_API
 *
 * @package Hestia
 * @since   Hestia 1.0
 */

/**
 * Class Hestia_Hooks_Page
 */
class Hestia_Hooks_Page extends Hestia_Abstract_Main {

	/**
	 * Settings base
	 *
	 * @access private
	 * @var string
	 */
	private $settings_base;

	/**
	 * Hooks Settings
	 *
	 * @access private
	 * @var string
	 */
	private $hooks_settings;

	/**
	 * Theme name
	 *
	 * @var string
	 */
	private $theme_name;


	/**
	 * Initialize hooks page.
	 */
	public function init() {

		$this->settings_base = '';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'set_hook_settings_fields' ) );

		// Register plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add settings page to menu
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Properly execute the hooks.
		add_action( 'init', array( $this, 'execute_hooks' ) );
	}

	/**
	 * Execute the hooks.
	 */
	public function execute_hooks() {
		$hooks = get_option( 'hestia_hooks' );
		if ( ! empty( $hooks ) ) {
			foreach ( $hooks as $hook => $action ) {

				add_action(
					$hook,
					function () use ( $hook ) {
						$this->execute_hook( $hook );
					}
				);

			}
		}
	}

	/**
	 * Function to execute actions added for each hook
	 *
	 * @param string $id Hook id.
	 */
	private function execute_hook( $id ) {
		$hooks = get_option( 'hestia_hooks' );

		$content = isset( $hooks[ $id ] ) ? $hooks[ $id ] : null;

		if ( ! $content ) {
			return;
		}

		$php = isset( $hooks[ $id . '_php' ] ) ? $hooks[ $id . '_php' ] : null;

		$value = do_shortcode( $content );

		if ( true === (bool) $php ) {
			eval( "?>$value<?php " );
		} else {
			echo $value;
		}
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function set_hook_settings_fields() {
		$this->hooks_settings = $this->hooks_settings_fields();
	}

	/**
	 * Build hooks settings fields
	 *
	 * @return array Fields displayed on hooks settings page
	 */
	private function hooks_settings_fields() {

		$hooks_settings['standard'] = array(
			'title'  => '',
			'fields' => array(
				array(
					'name' => esc_html__( 'Before Header', 'hestia-pro' ),
					'id'   => 'hestia_before_header_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Header Container', 'hestia-pro' ),
					'id'   => 'hestia_before_header_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Header Container', 'hestia-pro' ),
					'id'   => 'hestia_after_header_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Header', 'hestia-pro' ),
					'id'   => 'hestia_after_header_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Footer', 'hestia-pro' ),
					'id'   => 'hestia_before_footer_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Footer Container', 'hestia-pro' ),
					'id'   => 'hestia_before_footer_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Footer Widgets', 'hestia-pro' ),
					'id'   => 'hestia_before_footer_widgets_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Footer Widgets', 'hestia-pro' ),
					'id'   => 'hestia_after_footer_widgets_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Footer Container', 'hestia-pro' ),
					'id'   => 'hestia_after_footer_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Footer', 'hestia-pro' ),
					'id'   => 'hestia_after_footer_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Big title / Slider Section', 'hestia-pro' ),
					'id'   => 'hestia_before_big_title_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Big title / Slider Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_big_title_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Big title / Slider Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_big_title_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Big title buttons', 'hestia-pro' ),
					'id'   => 'hestia_big_title_section_buttons',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of title / Slider Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_big_title_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Big title / Slider Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_big_title_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Big title buttons', 'hestia-pro' ),
					'id'   => 'hestia_big_title_section_buttons',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Big title / Slider section', 'hestia-pro' ),
					'id'   => 'hestia_after_big_title_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Team Section', 'hestia-pro' ),
					'id'   => 'hestia_before_team_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Team Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_team_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Team Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_team_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Team Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_team_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Team Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_team_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Team Section', 'hestia-pro' ),
					'id'   => 'hestia_after_team_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Features Section', 'hestia-pro' ),
					'id'   => 'hestia_before_features_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Features Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_features_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Features Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_features_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Features Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_features_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Features Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_features_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Features Section', 'hestia-pro' ),
					'id'   => 'hestia_after_features_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Pricing Section', 'hestia-pro' ),
					'id'   => 'hestia_before_pricing_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Pricing Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_pricing_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Pricing Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_pricing_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Pricing Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_pricing_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Pricing Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_pricing_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Pricing Section', 'hestia-pro' ),
					'id'   => 'hestia_after_pricing_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before About section', 'hestia-pro' ),
					'id'   => 'hestia_before_about_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After About Section', 'hestia-pro' ),
					'id'   => 'hestia_after_about_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Shop Section', 'hestia-pro' ),
					'id'   => 'hestia_before_shop_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Shop Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_shop_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Shop Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_shop_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Shop Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_shop_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Shop Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_shop_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Shop Section', 'hestia-pro' ),
					'id'   => 'hestia_after_shop_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Testimonials Section', 'hestia-pro' ),
					'id'   => 'hestia_before_testimonials_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Testimonials Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_testimonials_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Testimonials Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_testimonials_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Testimonials Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_testimonials_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Testimonials Section Content', 'hestia-pro' ),
					'id'   => 'hestia_after_testimonials_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Testimonials Section', 'hestia-pro' ),
					'id'   => 'hestia_after_testimonials_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Subscribe Section', 'hestia-pro' ),
					'id'   => 'hestia_before_subscribe_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Subscribe Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_subscribe_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Subscribe Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_subscribe_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Subscribe Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_subscribe_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Subscribe Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_subscribe_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Subscribe Section', 'hestia-pro' ),
					'id'   => 'hestia_after_subscribe_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Blog Section', 'hestia-pro' ),
					'id'   => 'hestia_before_blog_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Blog Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_blog_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Blog Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_blog_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Blog Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_blog_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Blog Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_blog_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Blog Section', 'hestia-pro' ),
					'id'   => 'hestia_after_blog_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Contact Section', 'hestia-pro' ),
					'id'   => 'hestia_before_contact_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Contact Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_contact_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Contact Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_contact_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Contact Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_contact_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Contact Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_contact_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Contact Section', 'hestia-pro' ),
					'id'   => 'hestia_after_contact_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Portfolio Section', 'hestia-pro' ),
					'id'   => 'hestia_before_portfolio_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Portfolio Section Container', 'hestia-pro' ),
					'id'   => 'hestia_before_portfolio_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Top of Portfolio Section Container', 'hestia-pro' ),
					'id'   => 'hestia_top_portfolio_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Bottom of Portfolio Section Container', 'hestia-pro' ),
					'id'   => 'hestia_bottom_portfolio_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Portfolio Section Container', 'hestia-pro' ),
					'id'   => 'hestia_after_portfolio_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Portfolio Section', 'hestia-pro' ),
					'id'   => 'hestia_after_portfolio_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Clients Bar Section', 'hestia-pro' ),
					'id'   => 'hestia_before_clients_bar_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Clients Bar Section Content', 'hestia-pro' ),
					'id'   => 'hestia_clients_bar_section_content_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Clients Bar Section', 'hestia-pro' ),
					'id'   => 'hestia_after_clients_bar_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'Before Ribbon Section', 'hestia-pro' ),
					'id'   => 'hestia_before_ribbon_section_hook',
					'type' => 'textarea',
				),
				array(
					'name' => esc_html__( 'After Ribbon Section', 'hestia-pro' ),
					'id'   => 'hestia_after_ribbon_section_hook',
					'type' => 'textarea',
				),

			),
		);

		$hooks_settings = apply_filters( 'hestia_hooks_settings_fields', $hooks_settings );

		return $hooks_settings;
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$theme            = wp_get_theme();
		$this->theme_name = apply_filters( 'ti_wl_theme_name', $theme->get( 'Name' ) );

		if ( ! current_user_can( 'edit_files' ) || ! current_user_can( 'edit_themes' ) ) {
			return;
		}

		$page = add_theme_page(
			// translators: %s - Theme name
			sprintf( esc_html__( '%s Hooks', 'hestia-pro' ), $this->theme_name ),
			// translators: %s - Theme name
			sprintf( esc_html__( '%s Hooks', 'hestia-pro' ), $this->theme_name ),
			'edit_theme_options',
			'hestia_hooks_settings',
			array(
				$this,
				'build_hooks_settings_page',
			)
		);
		add_action( 'admin_print_styles-' . $page, array( $this, 'hooks_settings_assets' ) );
	}

	/**
	 * Enqueue scripts and styles needed
	 */
	public function hooks_settings_assets() {
		if ( function_exists( 'wp_enqueue_code_editor' ) ) {
			wp_enqueue_script( 'hestia-codemirror-xml-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/linting/xml.js', array( 'wp-codemirror' ), '', true );
			wp_enqueue_script( 'hestia-codemirror-clike-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/linting/clike.js', array( 'wp-codemirror' ), '', true );
			wp_enqueue_script( 'hestia-codemirror-php-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/linting/php.js', array( 'wp-codemirror' ), '', true );
			wp_enqueue_script( 'hestia-codemirror-php-parser-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/linting/php-parser.js', array( 'wp-codemirror' ), '', true );
			wp_enqueue_script( 'hestia-codemirror-php-lint-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/linting/php-lint.js', array( 'wp-codemirror' ), '', true );
			wp_enqueue_script(
				'hestia-hooks-seetings-js',
				HESTIA_ADDONS_URI . 'admin/hooks-page/js/functions.js',
				array(
					'jquery',
					'wp-codemirror',
					'hestia-codemirror-xml-js',
					'hestia-codemirror-clike-js',
					'hestia-codemirror-php-js',
					'hestia-codemirror-php-parser-js',
					'hestia-codemirror-php-lint-js',
					'csslint',
					'jshint',
					'htmlhint',
					'htmlhint-kses',
				),
				HESTIA_VERSION,
				true
			);
			wp_enqueue_style( 'hestia-hooks-settings-css', HESTIA_ADDONS_URI . 'admin/hooks-page/css/hooks.css', array( 'wp-codemirror' ), HESTIA_VERSION );
			wp_localize_script(
				'hestia-hooks-seetings-js',
				'hestia_hook_var',
				array(
					'php_error' => esc_html__( 'There are some errors in your PHP code. Please fix them before saving the code.', 'hestia-pro' ),
				)
			);
		} else {
			wp_enqueue_script( 'hestia-hooks-seetings-js', HESTIA_ADDONS_URI . 'admin/hooks-page/js/functions.js', array( 'jquery' ), HESTIA_VERSION );
			wp_enqueue_style( 'hestia-hooks-settings-css', HESTIA_ADDONS_URI . 'admin/hooks-page/css/hooks.css', array(), HESTIA_VERSION );
		}
	}

	/**
	 * Register settings
	 */
	public function register_settings() {

		if ( ! empty( $this->hooks_settings ) ) {

			if ( is_array( $this->hooks_settings ) ) {

				foreach ( $this->hooks_settings as $section => $data ) {

					/* Add a new section on the Hooks Settings page for each Hook */
					add_settings_section(
						$section,
						$data['title'],
						array(
							$this,
							'hooks_settings_section_callback',
						),
						'hestia_hooks_settings'
					);

					foreach ( $data['fields'] as $field ) {

						// Register field
						register_setting(
							'hestia_hooks_settings',
							'hestia_hooks',
							array(
								'sanitize_callback' => array( $this, 'sanitize_callback' ),
							)
						);

						// Add field to page
						add_settings_field(
							'hestia_hooks[' . $field['id'] . ']',
							$field['name'],
							array(
								$this,
								'display_field',
							),
							'hestia_hooks_settings',
							$section,
							array(
								'field' => $field,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Sanitize function for hooks.
	 *
	 * @param array $settings Places where you can add php code.
	 *
	 * @return array
	 */
	public function sanitize_callback( $settings ) {

		$sanitized = array();

		foreach ( $settings as $setting => $value ) {

			if ( current_user_can( 'unfiltered_html' ) ) {

				$sanitized[ $setting ] = $value;

			} else {

				$ends_with = substr_compare( $setting, '_php', - 4 ) === 0;
				if ( $ends_with === true ) {
					$sanitized[ $setting ] = false;
				} else {
					$sanitized[ $setting ] = wp_filter_post_kses( $value );
				}
			}
		}

		return $sanitized;
	}

	/**
	 * Callback for add_settings_section
	 */
	public function hooks_settings_section_callback() {
		$html = '';
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 *
	 * @param  array $args Field data.
	 *
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->settings_base . $field['id'];
		$option      = get_option( 'hestia_hooks' );

		$data = '';
		if ( isset( $option[ $option_name ] ) ) {
			$data = $option[ $option_name ];
		} elseif ( isset( $field['default'] ) ) {
			$data = $field['default'];
		}

		switch ( $field['type'] ) {

			case 'textarea':
				$checked = '';
				if ( isset( $option[ $field['id'] . '_php' ] ) && true === (bool) $option[ $field['id'] . '_php' ] ) {
					$checked = 'checked="checked"';
				}
				$html .= '<textarea class="hestia_hook_field_textarea" id="hestia_hooks[' . esc_attr( $field['id'] ) . ']" name="hestia_hooks[' . esc_attr( $field['id'] ) . ']" placeholder="' . ( ( ! empty( $field['description'] ) ) ? esc_attr( $field['description'] ) : '' ) . '" >' . esc_textarea( $data ) . '</textarea>';

				if ( current_user_can( 'unfiltered_html' ) ) {
					$html .= '<div class="execute"><input type="checkbox" name="hestia_hooks[' . esc_attr( $field['id'] ) . '_php]" id="hestia_hooks[' . esc_attr( $field['id'] ) . '_php]" value="true" ' . $checked . ' /> <label for="hestia_hooks[' . esc_attr( $field['id'] ) . '_php]">' . esc_html__( 'Execute PHP', 'hestia-pro' ) . '</label></div>';
				}
				break;

			case 'checkbox':
				break;

		}

		echo $html;
	}

	/**
	 * Build hooks settings page
	 */
	public function build_hooks_settings_page() {

		$html = '';

		$html .= '<div class="wrap" id="hestia_hooks_settings">';
		// translators: %s - Theme name
		$html .= '<h1 class="wp-heading-inline">' . sprintf( esc_html__( '%s Hooks', 'hestia-pro' ), $this->theme_name ) . '</h1>';

		$html .= '<input type="text" id="hestia_search_hooks" onkeyup="hestia_filter_hooks()" placeholder="Search hooks...">';

		$html .= '<div id="poststuff">';
		$html .= '<div id="post-body" class="metabox-holder columns-2">';
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">';

		/* Main page content */
		$html .= '<div id="post-body-content">';
		ob_start();
		settings_fields( 'hestia_hooks_settings' );
		do_settings_sections( 'hestia_hooks_settings' );
		$html .= ob_get_clean();
		$html .= '</div><!-- #post-body-content -->';

		/* Side box */
		$html .= '<div id="postbox-container-1">';
		$html .= '<div class="postbox">';
		// translators: %s - Theme name
		$html .= '<h3 class="hndle">' . sprintf( esc_html__( '%s Hooks', 'hestia-pro' ), $this->theme_name ) . '</h3>';
		$html .= '<div class="inside">';
		$html .= '<div class="submitbox" id="submitpost">';
		$html .= '<div id="minor-publishing">';
		$html .= '<p>' . esc_html__( 'Shortcodes are allowed, and you can even use PHP if you check the Execute PHP checkboxes.', 'hestia-pro' ) . '</p>';
		$html .= '</div><!-- #minor-publishing -->';
		$html .= '<div id="major-publishing-actions">';
		$html .= '<div id="publishing-action">';
		$html .= '<input name="Submit" type="submit" class="button button-primary button-large" value="' . esc_attr( __( 'Save Hooks', 'hestia-pro' ) ) . '" />';
		$html .= '</div><!-- .publishing-action -->';
		$html .= '<div class="clear"></div>';
		$html .= '</div><!-- #major-publishing-actions -->';
		$html .= '</div><!-- #submitpost -->';
		$html .= '</div><!-- .inside -->';
		$html .= '</div><!-- .postbox -->';
		$html .= '</div><!-- #postbox-container-1 -->';

		$html .= '</form>';
		$html .= '</div><!-- #post-body -->';
		$html .= '</div><!-- #poststuff -->';
		$html .= '</div><!-- #hestia_hooks_settings -->';

		echo $html;
	}
}
