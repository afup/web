<?php
/**
 * ThemeIsle - White Label Main Actions
 * @package ti-white-label
 */

/**
 * Class Ti_White_Label_Markup
 */
class Ti_White_Label_Markup {

	/**
	 * Member Variable
	 *
	 * @var array instance
	 */
	public static $branding;

	/**
	 * Details of the product where the module is called.
	 *
	 * @var array $product_settings Product details.
	 */
	private $product_details;

	/**
	 * Ti_White_Label_Markup constructor.
	 *
	 * @param array $settings Product details.
	 */
	public function __construct( $settings ) {
		$this->product_details = $settings;
		self::$branding        = self::get_white_label();

		$this->disable_sdk_features();

		if ( is_admin() ) {
			add_filter( 'wp_prepare_themes_for_js', array( $this, 'themes_page' ) );
			add_filter( 'all_themes', array( $this, 'network_themes_page' ) );

			add_filter( 'all_plugins', array( $this, 'plugins_page' ) );
			add_filter( 'update_right_now_text', array( $this, 'admin_dashboard_page' ) );
		}

		add_filter( 'ti_wl_theme_name', array( $this, 'change_theme_name' ) );
		add_filter( 'ti_wl_agency_url', array( $this, 'change_theme_url' ) );
		add_filter( 'ti_wl_plugin_name', array( $this, 'change_plugin_name' ) );
		add_filter( 'ti_about_config_filter', array( $this, 'about_page' ), 20 );
		add_filter( 'neve_dashboard_page_data', array( $this, 'neve_dashboard_localization' ), 100 );
		add_filter( 'ti_wl_copyright', array( $this, 'copyright_default' ) );
		add_action( 'customize_register', array( $this, 'change_customizer_controls' ) );

		add_filter( 'themeisle_sdk_hide_dashboard_widget', '__return_true' );
		add_filter( 'themeisle_sdk_hide_notifications', '__return_true' );

		add_filter( 'neve_pro_filter_dashboard_modules', array( $this, 'remove_module_documentation' ) );

		/**
		 * Disable HFG Descriptions
		 */
		if ( self::is_theme_whitelabeld() ) {
			add_filter( 'hfg_header_panel_description', '__return_false' );
			add_filter( 'hfg_footer_panel_description', '__return_false' );
		}

	}

	/**
	 * Get white label data.
	 * @return array
	 */
	static public function get_white_label() {
		$branding_default = array(
			'author_name'        => '',
			'author_url'         => '',
			'theme_name'         => '',
			'theme_description'  => '',
			'screenshot_url'     => '',
			'plugin_name'        => '',
			'plugin_description' => '',
			'white_label'        => false,
			'license'            => false,
		);

		$options  = get_option( 'ti_white_label_inputs' );
		$options  = json_decode( $options, true );
		$branding = wp_parse_args( $options, $branding_default );

		return $branding;
	}

	/**
	 * Function to disable sdk features.
	 *
	 * If the product in a plugin, it needs to be able to disable sdk features from the theme too. If it's a theme
	 * it will only disable sdk features for itself.
	 *
	 * @since 1.0.2
	 */
	private function disable_sdk_features() {
		$data = self::$branding;

		/**
		 * The product type can be 'plugin' or 'theme'. When we deactivate the license we do it for the main product,
		 * doesn't matter if it's a plugin or a theme.
		 */
		if ( $data['license'] === true ) {
			add_filter( 'neve_pro_addon_hide_license_field', '__return_true' );
			add_filter( 'neve_pro_addon_hide_license_notices', '__return_true' );
			add_filter( 'hestia_pro_hide_license_field', '__return_true' );
			add_filter( 'hestia_pro_hide_license_notices', '__return_true' );
		}

		/**
		 * In Neve Pro Addon the product type is 'plugin' but if the theme fields are not empty, we need to remove the
		 * uninstall feedback for theme too, that's why we need the theme's sdk key too.
		 */
		if ( ! empty( $data['theme_name'] ) || ! empty( $data['theme_description'] ) || ! empty( $data['screenshot_url'] ) ) {
			add_filter( 'hestia_pro_hide_uninstall_feedback', '__return_true' );
			add_filter( 'neve_hide_uninstall_feedback', '__return_true' );
		}

		/**
		 * In Hestia, the product type will be 'theme' so the following code will not be executed.
		 */
		if ( $this->product_details['type'] === 'plugin' && ( ! empty( $data['plugin_name'] ) || ! empty( $data['plugin_description'] ) ) ) {
			add_filter( 'neve_pro_addon_hide_uninstall_feedback', '__return_true' );
		}
	}

	/**
	 * Replace theme name, description, author, screenshot and and parent theme of child themes.
	 *
	 * @param $themes
	 *
	 * @return mixed
	 */
	public function themes_page( $themes ) {
		$theme        = wp_get_theme();
		$theme_parent = $theme->parent();
		if ( ! empty( $theme_parent ) ) {
			$theme = $theme->parent();
		}
		$theme_slug = $theme->get( 'TextDomain' );
		$theme_name = $theme->get( 'Name' );

		if ( ! isset( $themes[ $theme_slug ] ) ) {
			return $themes;
		}

		$data = self::$branding;

		if ( ! empty( $data['theme_name'] ) ) {
			/**
			 * Change theme name.
			 */
			$themes[ $theme_slug ]['name'] = $data['theme_name'];

			/**
			 * Change child-themes parent.
			 */
			foreach ( $themes as $key => $theme ) {
				if ( ! empty( $theme['parent'] ) && $theme_name === $theme['parent'] ) {
					$themes[ $key ]['parent'] = $data['theme_name'];
				}
			}
		}

		if ( ! empty( $data['author_name'] ) ) {
			$author_url                            = empty( $data['author_url'] ) ? '#' : $data['author_url'];
			$themes[ $theme_slug ]['author']       = $data['author_name'];
			$themes[ $theme_slug ]['authorAndUri'] = '<a href="' . esc_url( $author_url ) . '">' . $data['author_name'] . '</a>';
		}

		if ( ! empty( $data['theme_description'] ) ) {
			$themes[ $theme_slug ]['description'] = $data['theme_description'];
		}

		if ( ! empty( $data['screenshot_url'] ) ) {
			$themes[ $theme_slug ]['screenshot'] = array( $data['screenshot_url'] );
		}

		return $themes;
	}

	/**
	 * White labels the theme on the network admin themes page.
	 *
	 * @param array $themes Themes Array.
	 *
	 * @return array
	 * @throws ReflectionException
	 */
	public function network_themes_page( $themes ) {
		if ( ! is_network_admin() ) {
			return $themes;
		}

		$theme      = wp_get_theme();
		$theme_slug = $theme->get( 'TextDomain' );
		$theme_name = $theme->get( 'Name' );
		if ( ! isset( $themes[ $theme_slug ] ) ) {
			return $themes;
		}

		$data               = self::$branding;
		$network_theme_data = array();

		if ( ! empty( $data['theme_name'] ) ) {
			$network_theme_data['Name'] = $data['theme_name'];
			foreach ( $themes as $theme_key => $theme ) {
				if ( isset( $theme['parent'] ) && $theme_name === $theme['parent'] ) {
					$themes[ $theme_key ]['parent'] = $data['theme_name'];
				}
			}
		}

		if ( ! empty( $data['theme_description'] ) ) {
			$network_theme_data['Description'] = $data['theme_description'];
		}

		if ( ! empty( $data['author_name'] ) ) {
			$author_url                      = empty( $data['author_url'] ) ? '#' : $data['author_url'];
			$network_theme_data['Author']    = $data['author_name'];
			$network_theme_data['AuthorURI'] = $author_url;
			$network_theme_data['ThemeURI']  = $author_url;
		}

		if ( count( $network_theme_data ) > 0 ) {
			$reflection_object = new ReflectionObject( $themes[ $theme_slug ] );
			$headers           = $reflection_object->getProperty( 'headers' );
			$headers->setAccessible( true );
			$default_properties = $headers->getValue( $themes[ $theme_slug ] );
			$network_theme_data = wp_parse_args( $network_theme_data, $default_properties );
			$headers->setValue( $themes[ $theme_slug ], $network_theme_data );

			$headers_sanitized = $reflection_object->getProperty( 'headers_sanitized' );
			$headers_sanitized->setAccessible( true );
			$default_properties = $headers_sanitized->getValue( $themes[ $theme_slug ] );
			$network_theme_data = wp_parse_args( $network_theme_data, $default_properties );
			$headers_sanitized->setValue( $themes[ $theme_slug ], $network_theme_data );

			// Reset back to private.
			$headers->setAccessible( false );
			$headers_sanitized->setAccessible( false );
		}

		return $themes;
	}

	/**
	 * Replace plugin description and author name and url.
	 *
	 * @param $plugins
	 *
	 * @return mixed
	 */
	public function plugins_page( $plugins ) {
		if ( $this->product_details['type'] !== 'plugin' || empty( $this->product_details['plugin_base_name'] ) ) {
			return $plugins;
		}

		$data = self::$branding;
		$key  = $this->product_details['plugin_base_name'];

		if ( isset( $plugins[ $key ] ) && '' !== $data['plugin_name'] ) {
			$plugins[ $key ]['Name']        = $data['plugin_name'];
			$plugins[ $key ]['Description'] = $data['plugin_description'];
		}

		$author     = $data['author_name'];
		$author_uri = $data['author_url'];

		if ( ! empty( $author ) ) {
			$plugins[ $key ]['Author']     = $author;
			$plugins[ $key ]['AuthorName'] = $author;
		}

		if ( ! empty( $author_uri ) ) {
			$plugins[ $key ]['AuthorURI'] = $author_uri;
			$plugins[ $key ]['PluginURI'] = $author_uri;
		}

		return $plugins;
	}

	/**
	 * White labels the theme on the dashboard 'At a Glance' metabox
	 *
	 * @param mixed $content Content.
	 *
	 * @return string
	 */
	public function admin_dashboard_page( $content ) {
		$data = self::$branding;

		if ( is_admin() && ! empty( $data['theme_name'] ) ) {
			return sprintf( $content, get_bloginfo( 'version', 'display' ), '<a href="themes.php">' . $data['theme_name'] . '</a>' );
		}

		return $content;
	}

	/**
	 * Filter for changing theme name in about page.
	 *
	 * @param string $theme_name Current theme name.
	 *
	 * @return mixed
	 */
	public function change_theme_name( $theme_name ) {
		$data = self::$branding;
		if ( ! empty( $data['theme_name'] ) ) {
			return $data['theme_name'];
		}

		return $theme_name;
	}

	/**
	 * Filter for changing an url with Agency Url.
	 *
	 * @param string $url Current url.
	 *
	 * @return string
	 */
	public function change_theme_url( $url ) {
		$data = self::$branding;
		if ( ! empty( $data['author_url'] ) ) {
			return $data['author_url'];
		}

		return $url;
	}

	/**
	 * Filter for changing theme name in about page.
	 *
	 * @param $page_title
	 *
	 * @return mixed
	 */
	public function change_plugin_name( $plugin_name ) {
		$data = self::$branding;
		if ( ! empty( $data['plugin_name'] ) ) {
			return $data['plugin_name'];
		}

		return $plugin_name;
	}

	/**
	 * Change Neve new dashboard page configuration.
	 *
	 * @param array $config the dashboard localization array.
	 * @return array
	 */
	public function neve_dashboard_localization( $config ) {
		$data = self::$branding;

		if ( $this->is_plugin_whitelabeld() || self::is_theme_whitelabeld() ) {
			$config['whiteLabel'] = array(
				'agencyURL'        => $data['author_url'] ? $data['author_url'] : null,
				'hideStarterSites' => $data['starter_sites'] === true,
				'hideLicense'      => $data['license'] === true,
			);
		}

		return $config;
	}

	/**
	 * Change about page config array.
	 * Make sure that there are no Community and Leave us a review boxes.
	 *
	 * @param array $about_config Configuration array for About Page.
	 *
	 * @return array
	 */
	public function about_page( $about_config ) {

		/**
		 * Disable starter sites tab if the option is enabled
		 */
		$data = self::$branding;
		if ( array_key_exists( 'starter_sites', $data ) && $data['starter_sites'] === true ) {
			if ( array_key_exists( 'custom_tabs', $about_config ) && array_key_exists( 'sites_library', $about_config['custom_tabs'] ) ) {
				unset( $about_config['custom_tabs']['sites_library'] );
			}

			if ( array_key_exists( 'getting_started', $about_config ) && array_key_exists( 'content', $about_config['getting_started'] ) ) {
				unset( $about_config['getting_started']['content'][0] );
			}
		}

		if ( $this->is_plugin_whitelabeld() || self::is_theme_whitelabeld() ) {

			/**
			 * Disable footer messages.
			 */
			if ( array_key_exists( 'footer_messages', $about_config ) ) {
				unset( $about_config['footer_messages'] );
			}

			/**
			 * Disable Recommended actions, Useful plugins and Changelog tabs.
			 */
			if ( array_key_exists( 'recommended_actions', $about_config ) ) {
				unset( $about_config['recommended_actions'] );
			}
			if ( array_key_exists( 'useful_plugins', $about_config ) ) {
				unset( $about_config['useful_plugins'] );
			}
			if ( array_key_exists( 'recommended_plugins', $about_config ) ) {
				unset( $about_config['recommended_plugins'] );
			}
			if ( array_key_exists( 'changelog', $about_config ) ) {
				unset( $about_config['changelog'] );
			}

			/**
			 * Disable links to articles in documentation page.
			 */
			if ( array_key_exists( 'support', $about_config ) && array_key_exists( 'content', $about_config['support'] ) ) {
				$about_config['support']['content'] = $output = array_slice( $about_config['support']['content'], 0, 2 );
			}
		}

		return $about_config;
	}

	/**
	 * Check if any fields from theme are filled.
	 *
	 * @return bool
	 */
	static function is_theme_whitelabeld() {
		$data = self::$branding;
		if ( array_key_exists( 'theme_name', $data ) && ! empty( $data['theme_name'] ) ) {
			return true;
		}
		if ( array_key_exists( 'theme_description', $data ) && ! empty( $data['theme_description'] ) ) {
			return true;
		}
		if ( array_key_exists( 'screenshot_url', $data ) && ! empty( $data['screenshot_url'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if any fields from plugin are filled.
	 *
	 * @return bool
	 */
	private function is_plugin_whitelabeld() {
		$data = self::$branding;
		if ( array_key_exists( 'plugin_name', $data ) && ! empty( $data['plugin_name'] ) ) {
			return true;
		}
		if ( array_key_exists( 'plugin_description', $data ) && ! empty( $data['plugin_description'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Change the default value of a copyright control.
	 *
	 * @param string $value String value.
	 *
	 * @return string
	 */
	public function copyright_default( $value ) {
		$data = self::$branding;
		if ( ! empty( $data['theme_name'] ) ) {
			$author_url = empty( $data['author_url'] ) ? '#' : $data['author_url'];

			return sprintf(
			/* translators: %1$s is Theme Name, %2$s is WordPress */
				esc_html__( '%1$s | Powered by %2$s', 'hestia-pro' ),
				wp_kses_post( '<a href="' . esc_url( $author_url ) . '" rel="nofollow">' . $data['theme_name'] . '</a>' ),
				wp_kses_post( '<a href="http://wordpress.org" rel="nofollow">WordPress</a>' )
			);
		}

		return $value;
	}

	/**
	 * Change teheme name in customizer
	 */
	public function change_customizer_controls() {
		global $wp_customize;
		$panel_title                                = $wp_customize->get_panel( 'themes' )->title;
		$wp_customize->get_panel( 'themes' )->title = apply_filters( 'ti_wl_theme_name', $panel_title );

		do_action( 'ti_change_customizer_controls' );
	}

	/**
	 * Remove module documentation links if white label for plugins are not empty.
	 *
	 * @param array $modules Module settings array.
	 *
	 * @return array
	 */
	public function remove_module_documentation( $modules ) {
		$data = self::$branding;
		if ( ! empty( $data['plugin_name'] ) || ! empty( $data['plugin_description'] ) ) {
			foreach ( $modules as $module => $module_settings ) {
				$modules[ $module ]['documentation'] = array();
			}
		}

		return $modules;
	}

}
