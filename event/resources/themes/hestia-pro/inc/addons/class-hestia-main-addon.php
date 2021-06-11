<?php
/**
 * The Main Addon
 *
 * @package Hestia
 */

/**
 * Class Hestia_Main_Addon
 */
class Hestia_Main_Addon extends Hestia_Abstract_Main {

	/**
	 * Initialize the main addon.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_enqueue' ) );
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_script' ) );
		add_filter( 'hestia_about_page_array', array( $this, 'filter_about_page_array_pro' ) );
		add_filter( 'hestia_contact_support_link', array( $this, 'update_contact_support_pro_link' ) );
		add_action( 'widgets_init', array( $this, 'initialize_widgets' ) );
		add_action( 'after_switch_theme', array( $this, 'migrate_lite_options' ) );
		add_action( 'after_switch_theme', array( $this, 'reset_white_label' ) );
		add_filter( 'hestia_filter_onboarding_data', array( $this, 'add_remote_demos' ) );
		add_filter( 'hestia_editor_color_palette', array( $this, 'filter_editor_colors_palette' ) );

		add_action( 'themeisle_ob_after_customizer_import', array( $this, 'migrate_big_title_to_slider' ) );
	}

	/**
	 * Update the Contact Support link to our themeisle site
	 *
	 * @return string
	 */
	public function update_contact_support_pro_link() {
		return 'https://themeisle.com/contact/';
	}

	/**
	 * Migrate big title to slider when importing lite demo to pro.
	 */
	public function migrate_big_title_to_slider() {
		$big_title_content = array(
			'title'       => get_theme_mod( 'hestia_big_title_title' ),
			'text'        => get_theme_mod( 'hestia_big_title_text' ),
			'button_text' => get_theme_mod( 'hestia_big_title_button_text' ),
			'button_link' => get_theme_mod( 'hestia_big_title_button_link' ),
			'background'  => get_theme_mod( 'hestia_big_title_background' ),
		);

		$data = array();

		foreach ( $big_title_content as $index => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$data[ $index ] = $value;
		}
		if ( empty( $data ) ) {
			return;
		}

		$slider_setup = array(
			array(
				'id'        => 'customizer_repeater_56d7ea7f40a56',
				'title'     => $data['title'],
				'subtitle'  => $data['text'],
				'text'      => $data['button_text'],
				'link'      => $data['button_link'],
				'image_url' => $data['background'],
			),
		);

		set_theme_mod( 'hestia_slider_content', json_encode( $slider_setup ) );
	}

	/**
	 * Add remote import demos.
	 *
	 * @param array $demos the array of demos defined in Hestia_Public.
	 *
	 * @return array
	 */
	public function add_remote_demos( $demos ) {

		$license_data = get_option( 'hestia_pro_license_data' );
		if ( empty( $license_data ) ) {
			return $demos;
		}
		if ( $license_data->license !== 'valid' ) {
			return $demos;
		}
		$plan = $license_data->plan;
		if ( $plan === 1 ) {
			return $demos;
		}
		if ( ! isset( $demos['remote'] ) ) {
			$demos['remote'] = array();
		}

		unset( $demos['upsell'] );

		$remote_demos = array(
			'elementor' => array(
				'hestia-lawyers'     => array(
					'url'                   => 'https://demo.themeisle.com/hestia-lawyers/',
					'screenshot'            => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-lawyers-demo-screenshot-big.png',
					'title'                 => 'Lawyers Demo',
					'edit_content_redirect' => 'customizer',
				),
				'hestia-travel'      => array(
					'url'        => 'https://demo.themeisle.com/hestia-travel/',
					'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-travel-demo-screenshot-big.png',
					'title'      => 'Travel Agency Demo',
				),
				'hestia-coffee-shop' => array(
					'url'        => 'https://demo.themeisle.com/hestia-coffee-shop/',
					'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-coffee-shop-demo-screenshot-big.png',
					'title'      => 'Coffee Shop Demo',
				),
				'hestia-gym'         => array(
					'url'                   => 'https://demo.themeisle.com/hestia-gym/',
					'screenshot'            => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-gym-demo-screenshot-big.png',
					'title'                 => 'Gym Demo',
					'edit_content_redirect' => 'customizer',
				),
			),
		);

		$addon_demos     = array_merge_recursive( $demos['remote'], $remote_demos );
		$demos['remote'] = $addon_demos;

		return $demos;
	}

	/**
	 * Customizer enqueue scripts.
	 */
	public function customizer_enqueue() {

		wp_enqueue_script( 'hestia_scripts_customizer_pro', HESTIA_ADDONS_URI . 'assets/js/scripts-customizer-pro.js', array( 'jquery' ), HESTIA_VENDOR_VERSION, true );
		$control_settings = array(
			'sections_container' => '#accordion-panel-hestia_frontpage_sections > ul, #sub-accordion-panel-hestia_frontpage_sections',
			'blocked_items'      => '#accordion-section-hestia_slider, #accordion-section-hestia_info_jetpack, #accordion-section-hestia_info_woocommerce, #accordion-section-sidebar-widgets-sidebar-big-title',
		);
		wp_localize_script( 'hestia_scripts_customizer_pro', 'shortcode_settings', $control_settings );
	}

	/**
	 * Handle enqueue.
	 */
	public function enqueue() {
		if ( ! is_front_page() ) {
			return;
		}

		if ( get_option( 'show_on_front' ) !== 'page' ) {
			return;
		}

		// Scroll animations enqueue
		$enable_animations = apply_filters( 'hestia_enable_animations', true );
		if ( $enable_animations ) {
			wp_enqueue_script( 'animate-on-scroll', get_template_directory_uri() . '/inc/addons/assets/js/aos.min.js', HESTIA_VENDOR_VERSION, true );
		}
	}

	/**
	 * Enqueue Customizer Script.
	 */
	public function enqueue_customizer_script() {
		wp_enqueue_script(
			'hestia-pro-customizer-preview',
			get_template_directory_uri() . '/inc/addons/assets/js/customizer.js',
			array(
				'jquery',
			),
			HESTIA_VERSION,
			true
		);
	}

	/**
	 * Function to filter the about page settings
	 *
	 * @param array $config The about page config array.
	 *
	 * @return array
	 */
	public function filter_about_page_array_pro( $config ) {
		$theme      = wp_get_theme();
		$theme_name = apply_filters( 'ti_wl_theme_name', $theme->get( 'Name' ) );
		unset( $config['custom_tabs']['free_pro'] );

		// translators: %s - Theme name
		$config['getting_started']['content'][1]['text']                                = sprintf( esc_html__( 'Need more details? Please check our full documentation for detailed information on how to use %s.', 'hestia-pro' ), $theme_name );
		$config['getting_started']['content'][1]['button']['link']                      = apply_filters( 'ti_wl_agency_url', 'http://docs.themeisle.com/article/532-hestia-pro-documentation' );
		$config['recommended_actions']['plugins']['themeisle-companion']['description'] = __( 'Extend your theme functionality with various modules like Social Media Share Buttons & Icons, custom menu-icons, one click import page templates, page builder addons and free stock featured images.', 'hestia-pro' );
		// translators: %1$s - Theme name
		$config['support']['content'][0]['text']           = sprintf( esc_html__( 'We want to make sure you have the best experience using %1$s and that is why we gathered here all the necessary information for you. We hope you will enjoy using %1$s, as much as we enjoy creating great products.', 'hestia-pro' ), $theme_name );
		$config['support']['content'][0]['button']['link'] = apply_filters( 'ti_wl_agency_url', $config['support']['content'][0]['button']['link'] );
		// translators: %s - Theme name
		$config['support']['content'][1]['text']           = sprintf( esc_html__( 'Need more details? Please check our full documentation for detailed information on how to use %s.', 'hestia-pro' ), $theme_name );
		$config['support']['content'][1]['button']['link'] = apply_filters( 'ti_wl_agency_url', 'http://docs.themeisle.com/article/532-hestia-pro-documentation' );
		$config['support']['content'][3]['button']['link'] = apply_filters( 'ti_wl_agency_url', $config['support']['content'][3]['button']['link'] );
		$config['support']['content'][4]['button']['link'] = apply_filters( 'ti_wl_agency_url', $config['support']['content'][4]['button']['link'] );
		$config['support']['content'][5]['button']['link'] = apply_filters( 'ti_wl_agency_url', $config['support']['content'][5]['button']['link'] );
		// translators: %s - Theme name
		$old_config['menu_name'] = apply_filters( 'hestia_about_page_filter', sprintf( __( 'About %s', 'hestia-pro' ), $theme_name ), 'pro_menu_name' );
		// translators: %s - Theme name
		$old_config['page_name'] = apply_filters( 'hestia_about_page_filter', sprintf( __( 'About %s', 'hestia-pro' ), $theme_name ), 'pro_page_name' );
		// translators: %s - Theme name
		$old_config['welcome_title'] = apply_filters( 'hestia_about_page_filter', sprintf( __( 'Welcome to %s! - Version ', 'hestia-pro' ), $theme_name ), 'pro_welcome_title' );
		// translators: %s - Theme name
		$old_config['welcome_content'] = apply_filters( 'hestia_about_page_filter', sprintf( esc_html__( '%s is a modern WordPress theme for professionals. It fits creative business, small businesses (restaurants, wedding planners, sport/medical shops), startups, corporate businesses, online agencies and firms, portfolios, ecommerce (WooCommerce), and freelancers. It has a multipurpose one-page design, widgetized footer, blog/news page and a clean look, is compatible with: Flat Parallax Slider, Photo Gallery, Travel Map and Elementor Page Builder . The theme is responsive, WPML, Retina ready, SEO friendly, and uses Material Kit for design.', 'hestia-pro' ), $theme_name ), 'pro_welcome_content' );

		if ( class_exists( 'Ti_White_Label_Markup' ) && Ti_White_Label_Markup::is_theme_whitelabeld() ) {
			unset( $config['getting_started']['content'][0] );
			unset( $config['getting_started']['content'][1] );
			$config['support']['content'] = array_slice( $config['support']['content'], 0, 1 );
			add_action(
				'admin_head',
				function() {
					echo '
				<style>
				#about-tabs #support .about-col,
				#about-tabs #getting_started .about-col {
					width: 100%;
				}
				</style>';
				}
			);
		}

		return $config;
	}

	/**
	 * Register widgets for the theme.
	 *
	 * @since    Hestia 1.0
	 * @modified 1.1.40
	 */
	public function initialize_widgets() {
		/**
		 * Array of all the main sidebars registered in the theme
		 */
		$sidebars_array = array(
			'blog-subscribe-widgets' => esc_html__( 'Blog Subscribe Section', 'hestia-pro' ),
		);

		foreach ( $sidebars_array as $sidebar_id => $sidebar_name ) {
			$sidebar_settings = array(
				'name'          => $sidebar_name,
				'id'            => $sidebar_id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5>',
				'after_title'   => '</h5>',
			);
			if ( $sidebar_id === 'blog-subscribe-widgets' ) {
				$sidebar_settings['before_widget'] = '';
				$sidebar_settings['after_widget']  = '';
			}

			register_sidebar( $sidebar_settings );
		}
	}

	/**
	 * Import lite options.
	 */
	public function migrate_lite_options() {
		$old_theme = strtolower( get_option( 'theme_switched' ) );
		if ( 'hestia' !== $old_theme ) {
			return;
		}

		/* import Hestia options */
		$hestia_mods = get_option( 'theme_mods_hestia' );

		if ( ! empty( $hestia_mods ) ) {

			foreach ( $hestia_mods as $hestia_mod_k => $hestia_mod_v ) {
				set_theme_mod( $hestia_mod_k, $hestia_mod_v );
			}
		}
	}

	/**
	 * Reset white label option.
	 */
	public function reset_white_label() {
		$white_label_settings                = get_option( 'ti_white_label_inputs' );
		$white_label_settings                = json_decode( $white_label_settings, true );
		$white_label_settings['white_label'] = false;
		update_option( 'ti_white_label_inputs', json_encode( $white_label_settings ) );
	}

	/**
	 * Add more colors in the Gutenberg palette.
	 *
	 * @param array $colors The colors theme-support array.
	 *
	 * @return array
	 */
	function filter_editor_colors_palette( $colors ) {

		// Insert secondary color after the third color.
		array_splice(
			$colors,
			3,
			0,
			array(
				array(
					'name'  => esc_html__( 'Secondary', 'hestia-pro' ),
					'slug'  => 'secondary',
					'color' => get_theme_mod( 'secondary_color', '#2d3359' ),
				),
			)
		);

		// Add the rest of the colors at the end.
		$new_arr = array(
			array(
				'name'  => esc_html__( 'Body', 'hestia-pro' ),
				'slug'  => 'body-color',
				'color' => get_theme_mod( 'body_color', '#999999' ),
			),
			array(
				'name'  => esc_html__( 'Header Overlay', 'hestia-pro' ),
				'slug'  => 'header-overlay-color',
				'color' => get_theme_mod( 'header_overlay_color', 'rgba(0,0,0,0.5)' ),
			),
			array(
				'name'  => esc_html__( 'Header Text', 'hestia-pro' ),
				'slug'  => 'header-text-color',
				'color' => get_theme_mod( 'header_text_color', '#fffffe' ),
			),
			array(
				'name'  => esc_html__( 'Navbar Background', 'hestia-pro' ),
				'slug'  => 'navbar-background',
				'color' => get_theme_mod( 'navbar_background_color', '#fffffd' ),
			),
			array(
				'name'  => esc_html__( 'Navbar Text', 'hestia-pro' ),
				'slug'  => 'navbar-text-color',
				'color' => get_theme_mod( 'navbar_text_color', '#555555' ),
			),
			array(
				'name'  => esc_html__( 'Navbar Text Hover', 'hestia-pro' ),
				'slug'  => 'navbar-text-color-hover',
				'color' => get_theme_mod( 'navbar_text_color_hover', '#e91e63' ),
			),
			array(
				'name'  => esc_html__( 'Transparent Navbar Text Color', 'hestia-pro' ),
				'slug'  => 'navbar-transparent-text-color',
				'color' => get_theme_mod( 'navbar_transparent_text_color', '#fffffc' ),
			),
		);

		$colors = array_merge( $colors, $new_arr );

		return $colors;
	}
}
