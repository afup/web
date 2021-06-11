<?php
/**
 * Handles front end setup.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Public
 */
class Hestia_Public {

	/**
	 * Generic strings.
	 *
	 * @var array
	 */
	var $generic_strings;

	/**
	 * Enqueue theme scripts.
	 */
	public function enqueue_scripts() {
		// Bootstrap
		if ( ! class_exists( 'Elementor\Frontend', false ) ) {
			wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css', array(), HESTIA_VENDOR_VERSION );
			wp_style_add_data( 'bootstrap', 'rtl', 'replace' );
			wp_style_add_data( 'bootstrap', 'suffix', '.min' );
			wp_enqueue_style( 'hestia-font-sizes', get_template_directory_uri() . '/assets/css/font-sizes' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		}

		$stylesheet = $this->get_stylesheet();
		// Main Stylesheet
		wp_enqueue_style( 'hestia_style', $stylesheet, array(), apply_filters( 'hestia_version_filter', HESTIA_VERSION ) );
		wp_style_add_data( 'hestia_style', 'rtl', 'replace' );
		if ( ! HESTIA_DEBUG ) {
			wp_style_add_data( 'hestia_style', 'suffix', '.min' );
		}

		$this->enqueue_woocommerce();
		$this->enqueue_custom_fonts();

		// Customizer Style
		if ( is_customize_preview() ) {
			wp_enqueue_style( 'hestia-customizer-preview-style', get_template_directory_uri() . '/assets/css/customizer-preview' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		}

		if ( is_singular() ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script( 'jquery-bootstrap', get_template_directory_uri() . '/assets/bootstrap/js/bootstrap.min.js', array( 'jquery' ), HESTIA_VENDOR_VERSION, true );

		$dependencies = array(
			'jquery-ui-core',
			'jquery',
		);

		if ( self::should_enqueue_masonry() ) {
			array_push( $dependencies, 'masonry' );
		}

		wp_register_script(
			'hestia_scripts',
			get_template_directory_uri() . '/assets/js/script.min.js',
			$dependencies,
			HESTIA_VERSION,
			true
		);

		wp_localize_script(
			'hestia_scripts',
			'requestpost',
			array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'disable_autoslide' => get_theme_mod( 'hestia_slider_disable_autoplay' ),
				'masonry'           => self::should_enqueue_masonry() ? true : false,
			)
		);
		wp_enqueue_script( 'hestia_scripts' );

		$this->maybe_enqueue_parallax();
	}

	/**
	 * Get stylesheet uri depending on child themes.
	 *
	 * @return string
	 */
	private function get_stylesheet() {
		$stylesheet_dir = get_stylesheet_directory_uri();
		$template_dir   = get_template_directory_uri();
		if ( $template_dir === $stylesheet_dir ) {
			return get_template_directory_uri() . '/style' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css';
		}

		return get_stylesheet_uri();
	}

	/**
	 * Handle WooCommerce Enqueue.
	 */
	private function enqueue_woocommerce() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

	}

	/**
	 * Enqueue Custom fonts.
	 */
	public function enqueue_custom_fonts() {
		$hestia_headings_font = get_theme_mod( 'hestia_headings_font' );
		$hestia_body_font     = get_theme_mod( 'hestia_body_font' );
		if ( empty( $hestia_headings_font ) || empty( $hestia_body_font ) ) {
			wp_enqueue_style( 'hestia_fonts', $this->get_fonts_url(), array(), HESTIA_VERSION );
		}
	}

	/**
	 * Get fonts url.
	 *
	 * @return string fonts that need to be enqueued.
	 */
	private function get_fonts_url() {
		$fonts_url = '';
		/**
		 * Translators: If there are characters in your language that are not
		 * supported by Roboto or Roboto Slab, translate this to 'off'. Do not translate
		 * into your own language.
		 */
		$roboto      = _x( 'on', 'Roboto font: on or off', 'hestia-pro' );
		$roboto_slab = _x( 'on', 'Roboto Slab font: on or off', 'hestia-pro' );

		if ( 'off' !== $roboto || 'off' !== $roboto_slab ) {
			$font_families = array();

			if ( 'off' !== $roboto ) {
				$font_families[] = 'Roboto:300,400,500,700';
			}

			if ( 'off' !== $roboto_slab ) {
				$font_families[] = 'Roboto Slab:400,700';
			}

			$query_args = array(
				'family' => rawurlencode( implode( '|', $font_families ) ),
				'subset' => rawurlencode( 'latin,latin-ext' ),
			);
			$fonts_url  = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return $fonts_url;
	}

	/**
	 * Determine if we should load masonry.
	 */
	public static function should_enqueue_masonry() {
		if ( self::is_blog() === false ) {
			return false;
		}

		$hestia_alternative_blog_layout = get_theme_mod( 'hestia_alternative_blog_layout', 'blog_normal_layout' );
		if ( $hestia_alternative_blog_layout !== 'blog_alternative_layout2' ) {
			return false;
		}

		$hestia_grid_layout = get_theme_mod( 'hestia_grid_layout', 1 );
		if ( $hestia_grid_layout === 1 ) {
			return false;
		}

		return get_theme_mod( 'hestia_enable_masonry', false );
	}

	/**
	 * Detect if is blog page.
	 *
	 * @return bool
	 */
	public static function is_blog() {
		return is_home() && 'post' === get_post_type();
	}

	/**
	 * Maybe enqueue Parallax Script.
	 */
	private function maybe_enqueue_parallax() {
		if ( ! Hestia_First_Front_Page_Section::should_display_parallax() ) {
			return;
		}

		wp_enqueue_script( 'hestia-parallax', get_template_directory_uri() . '/assets/js/parallax.min.js', array( 'jquery' ), HESTIA_VENDOR_VERSION );
	}

	/**
	 * Filter the front page template so it's bypassed entirely if the user selects
	 * to display blog posts on their homepage instead of a static page.
	 */
	public function filter_front_page_template( $template ) {
		return is_home() ? '' : $template;
	}

	/**
	 * Enqueue font sizes before elementor.
	 */
	public function enqueue_before_elementor() {
		if ( class_exists( 'Elementor\Frontend', false ) ) {
			wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css', array(), HESTIA_VENDOR_VERSION );
			wp_style_add_data( 'bootstrap', 'rtl', 'replace' );
			wp_style_add_data( 'bootstrap', 'suffix', '.min' );
			wp_enqueue_style( 'hestia-font-sizes', get_template_directory_uri() . '/assets/css/font-sizes' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		}
	}

	/**
	 * Executed after theme has been activated.
	 */
	public function theme_activated() {
		update_option( 'hestia_time_activated', time() );
		$this->import_flagship_content();
		$this->import_child_themes_content();
	}

	/**
	 * Instantiates Classes that handle the content migration from other TI themes.
	 */
	private function import_flagship_content() {
		if ( class_exists( 'Hestia_Content_Import', false ) ) {
			$importer = new Hestia_Content_Import();
			$importer->import();
		}

		if ( class_exists( 'Hestia_Import_Zerif', false ) ) {
			$zerif_importer = new Hestia_Import_Zerif();
			$zerif_importer->import();
		}
	}

	/**
	 * Import theme mods when switching from a Themeisle Hestia child theme to Hestia
	 */
	private function import_child_themes_content() {

		// Get the name of the previously active theme.
		$previous_theme = strtolower( get_option( 'theme_switched' ) );
		$allowed_themes = array(
			'christmas-hestia',
			'tiny-hestia',
			'orfeo',
			'hestia-child',
			'hestia-child-theme',
			'hestia-pro-child',
			'hestia-pro-child-theme',
		);
		if ( ! in_array( $previous_theme, $allowed_themes, true ) ) {
			return;
		}

		// Get the theme mods from the previous theme.
		$previous_theme_content = get_option( 'theme_mods_' . $previous_theme );

		if ( ! empty( $previous_theme_content ) ) {
			foreach ( $previous_theme_content as $previous_theme_mod_k => $previous_theme_mod_v ) {
				set_theme_mod( $previous_theme_mod_k, $previous_theme_mod_v );
			}
		}
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
			'sidebar-1'           => esc_html__( 'Sidebar', 'hestia-pro' ),
			'subscribe-widgets'   => esc_html__( 'Subscribe', 'hestia-pro' ),
			'sidebar-woocommerce' => esc_html__( 'WooCommerce Sidebar', 'hestia-pro' ),
			'sidebar-top-bar'     => esc_html__( 'Very Top Bar', 'hestia-pro' ),
			'header-sidebar'      => esc_html__( 'Navigation', 'hestia-pro' ),
			'sidebar-big-title'   => apply_filters( 'hestia_big_title_fs_label', esc_html__( 'Big Title Section', 'hestia-pro' ) ),
		);

		/**
		 * Array of sidebars registered in the footer area.
		 * The hestia_footer_widget_areas_array filter is used in the PRO version to add the extra forth sidebar.
		 */
		$footer_sidebars_array = apply_filters(
			'hestia_footer_widget_areas_array',
			array(
				'footer-one-widgets'   => esc_html__( 'Footer One', 'hestia-pro' ),
				'footer-two-widgets'   => esc_html__( 'Footer Two', 'hestia-pro' ),
				'footer-three-widgets' => esc_html__( 'Footer Three', 'hestia-pro' ),
				'footer-four-widgets'  => esc_html__( 'Footer Four', 'hestia-pro' ),
			)
		);

		/**
		 * Number of footer sidebars that need to be registered.
		 * This option is available only in the PRO version. In Hestia, the value is always 3.
		 */
		$hestia_nr_footer_widgets = is_customize_preview() ? '4' : get_theme_mod( 'hestia_nr_footer_widgets', '3' );

		/**
		 * If the Number of widgets areas option is selected, add the specific number of footer sidebars in the main sidebars array to be registered.
		 */
		if ( ! empty( $hestia_nr_footer_widgets ) ) {
			$footer_sidebars_array = array_slice( $footer_sidebars_array, 0, $hestia_nr_footer_widgets );
		}

		if ( ! empty( $footer_sidebars_array ) ) {
			$sidebars_array = array_merge( $sidebars_array, $footer_sidebars_array );
		}

		/**
		 * Register the sidebars
		 */
		foreach ( $sidebars_array as $sidebar_id => $sidebar_name ) {
			$sidebar_settings = array(
				'name'          => $sidebar_name,
				'id'            => $sidebar_id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5>',
				'after_title'   => '</h5>',
			);
			if ( $sidebar_id === 'subscribe-widgets' || $sidebar_id === 'blog-subscribe-widgets' ) {
				$sidebar_settings['before_widget'] = '';
				$sidebar_settings['after_widget']  = '';
			}

			register_sidebar( $sidebar_settings );
		}
	}

	/**
	 * Setup the theme.
	 *
	 * @since Hestia 1.0
	 */
	public function setup_theme() {
		// Maximum allowed width for any content in the theme, like oEmbeds and images added to posts.  https://codex.wordpress.org/Content_Width
		global $content_width;
		if ( ! isset( $content_width ) ) {
			$content_width = 750;
		}

		load_theme_textdomain( 'hestia-pro', get_template_directory() . '/languages' );

		$header_settings = apply_filters(
			'hestia_custom_header_settings',
			array(
				'width'       => 2000,
				'flex-height' => true,
				'height'      => 1150,
				'flex-width'  => true,
				'header-text' => false,
			)
		);

		$logo_settings = array(
			'flex-width'  => true,
			'flex-height' => true,
			'height'      => 100,
		);

		$custom_background_settings = array(
			'default-color' => apply_filters( 'hestia_default_background_color', 'E5E5E5' ),
		);

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'custom-logo', $logo_settings );
		add_theme_support( 'html5', array( 'search-form' ) );
		add_theme_support( 'custom-header', $header_settings );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-background', $custom_background_settings );
		add_theme_support( 'themeisle-demo-import', $this->get_ti_demo_content_support_data() );
		add_theme_support( 'align-wide' );
		add_theme_support( 'header-footer-elementor' );
		if ( class_exists( 'Hestia_Starter_Content', false ) ) {
			add_theme_support( 'starter-content', ( new Hestia_Starter_Content() )->get() );
		}

		/**
		 * Add support for wide alignments.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
		 */
		add_theme_support( 'align-wide' );

		/**
		 * Add support for block color palettes.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
		 */
		add_theme_support(
			'editor-color-palette',
			apply_filters(
				'hestia_editor_color_palette',
				array(
					array(
						'name'  => 'White',
						'slug'  => 'white',
						'color' => '#ffffff',
					),
					array(
						'name'  => 'Black',
						'slug'  => 'black',
						'color' => '#000000',
					),
					array(
						'name'  => esc_html__( 'Accent Color', 'hestia-pro' ),
						'slug'  => 'accent',
						'color' => get_theme_mod( 'accent_color', '#e91e63' ),
					),
					array(
						'name'  => esc_html__( 'Body', 'hestia-pro' ),
						'slug'  => 'background_color',
						'color' => '#' . get_theme_mod( 'background_color', 'E5E5E5' ),
					),
					array(
						'name'  => esc_html__( 'Header Background', 'hestia-pro' ),
						'slug'  => 'header-gradient',
						'color' => get_theme_mod( 'hestia_header_gradient_color', apply_filters( 'hestia_header_gradient_default', '#a81d84' ) ),
					),
				)
			)
		);

		register_nav_menus(
			array(
				'primary'      => esc_html__( 'Primary Menu', 'hestia-pro' ),
				'footer'       => esc_html__( 'Footer Menu', 'hestia-pro' ),
				'top-bar-menu' => esc_html__( 'Very Top Bar', 'hestia-pro' ) . ' ' . esc_html__( 'Menu', 'hestia-pro' ),
			)
		);

		add_image_size( 'hestia-blog', 360, 240, true );

		add_editor_style();

		$this->setup_woocommerce();
		$this->setup_jetpack();
		$this->maybe_register_front_page_strings();

		add_filter( 'themeisle_gutenberg_templates', array( $this, 'add_gutenberg_templates' ) );

		add_action( 'after_switch_theme', array( $this, 'should_load_shim' ) );

		add_action( 'wp_loaded', array( $this, 'should_load_shim' ) );

		add_action( 'wp_footer', array( $this, 'check_fa4_styles' ) );

		add_action( 'wp_footer', array( $this, 'enqueue_font_awesome' ) );

		add_filter( 'widget_custom_html_content', array( $this, 'filter_html_widget_content' ) );
	}

	/**
	 * Filter HTML widget and check if font awesome should load
	 *
	 * @param string $content HTML widget content.
	 *
	 * @return mixed
	 */
	public function filter_html_widget_content( $content ) {
		maybe_trigger_fa_loading( $content );
		return $content;
	}

	/**
	 * Get the themeisle demo content support data.
	 *
	 * @return array
	 */
	private function get_ti_demo_content_support_data() {

		$theme_name = apply_filters( 'ti_wl_theme_name', wp_get_theme()->Name );

		$onboarding_sites = array(
			'editors'     => array(
				'elementor',
			),
			'remote'      => array(
				'elementor' => array(
					'hestia-default'       => array(
						'url'                   => 'https://demo.themeisle.com/hestia-default',
						'screenshot'            => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2020/03/hestia-default.png',
						'title'                 => 'Hestia Original',
						'edit_content_redirect' => 'customizer',
					),
					'hestia-woocommerce'   => array(
						'url'                   => 'https://demo.themeisle.com/hestia-woocommerce',
						'screenshot'            => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2020/03/hestia-woocommerce.png',
						'title'                 => 'WooCommerce Demo',
						'edit_content_redirect' => 'customizer',
					),
					'hestia-energy-panels' => array(
						'url'        => 'https://demo.themeisle.com/hestia-energy-panels',
						'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2020/03/hestia-energy-panels.png',
						'title'      => 'Energy Panels Demo',
					),
					'hestia-vet-center'    => array(
						'url'        => 'https://demo.themeisle.com/hestia-vet-center',
						'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2020/03/hestia-vet-center.png',
						'title'      => 'Vet Center Demo',
					),
					'hestia-zelle'         => array(
						'url'        => 'https://demo.themeisle.com/hestia-zelle',
						'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2020/03/hestia-zelle.png',
						'title'      => 'Agency Travel Demo',
					),
				),
			),
			'upsell'      => array(
				'elementor' => array(
					'hestia-lawyers'     => array(
						'url'        => 'https://demo.themeisle.com/hestia-lawyers/',
						'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-lawyers-demo-screenshot-big.png',
						'title'      => 'Lawyers Demo',
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
						'url'        => 'https://demo.themeisle.com/hestia-gym/',
						'screenshot' => 'https://demo.themeisle.com/hestia-pro-demo-content/wp-content/uploads/sites/105/2019/03/hestia-gym-demo-screenshot-big.png',
						'title'      => 'Gym Demo',
					),
				),
			),
			'i18n'        => array(
				'onboard_description_old' => __( 'This process will set up your website, install required plugins, import demo content (pages, posts, media) and set up the customizer options.', 'hestia-pro' ),
				'templates_title'         => __( 'Get started here', 'hestia-pro' ),
				/* translators: %s - theme name */
				'templates_description'   => sprintf( __( 'With %s, you can choose from multiple unique demos, specially designed for you, that can be installed with a single click. You just need to choose your favorite, and we will take care of everything else.', 'hestia-pro' ), $theme_name ),
			),
			'can_migrate' => array(
				'zerif-pro'  => array(
					'theme_name'        => 'Zelle Pro',
					'theme_mod_check'   => 'zerif_frontpage_was_imported',
					'template'          => 'zelle',
					'heading'           => __( 'Want to keep using Zelle\'s homepage?', 'hestia-pro' ),
					'description'       => __( 'Hi! We\'ve noticed you were using Zelle before. To make your transition easier, we can help you keep the same beautiful homepage you had before, by converting it into an Elementor template. This option will also import your homepage content.', 'hestia-pro' ),
					'mandatory_plugins' => array(
						'elementor' => 'Elementor Page Builder',
					),
				),
				'zerif-lite' => array(
					'theme_name'        => 'Zelle Lite',
					'theme_mod_check'   => 'zerif_frontpage_was_imported',
					'template'          => 'zelle',
					'heading'           => __( 'Want to keep using Zelle\'s homepage?', 'hestia-pro' ),
					'description'       => __( 'Hi! We\'ve noticed you were using Zelle before. To make your transition easier, we can help you keep the same beautiful homepage you had before, by converting it into an Elementor template. This option will also import your homepage content.', 'hestia-pro' ),
					'mandatory_plugins' => array(
						'elementor' => 'Elementor Page Builder',
					),
				),
			),
			'pro_link'    => 'https://themeisle.com/themes/hestia-pro/upgrade/',
		);

		return apply_filters( 'hestia_filter_onboarding_data', $onboarding_sites );
	}

	/**
	 * Setup Woocommerce Support
	 */
	private function setup_woocommerce() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			$woocommerce_notice = HESTIA_PHP_INCLUDE . 'customizer/utils/customizer-info/class/class-hestia-customizer-info-singleton.php';
			if ( is_file( $woocommerce_notice ) ) {
				require_once( $woocommerce_notice );
			}

			return;
		}

		$woocommerce_settings = apply_filters(
			'hestia_woocommerce_args',
			array(
				'single_image_width'            => 600,
				'thumbnail_image_width'         => 230,
				'gallery_thumbnail_image_width' => 160,
				'product_grid'                  => array(
					'default_columns' => 3,
					'default_rows'    => 4,
					'min_columns'     => 1,
					'max_columns'     => 6,
					'min_rows'        => 1,
				),
			)
		);

		add_theme_support( 'woocommerce', $woocommerce_settings );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		if ( function_exists( 'wc_get_image_size' ) ) {
			$thumbnail = wc_get_image_size( 'thumbnail' );
			if ( ! empty( $thumbnail['width'] ) && ! empty( $thumbnail['height'] ) && ! empty( $thumbnail['crop'] ) ) {
				add_image_size( 'woocommerce_thumbnail_2x', $thumbnail['width'] * 2, $thumbnail['height'] * 2, $thumbnail['crop'] );
			}
		}
	}

	/**
	 * Setup Jetpack Support
	 */
	private function setup_jetpack() {
		if ( ! class_exists( 'Jetpack', false ) ) {
			return;
		}
		add_theme_support( 'jetpack-portfolio' );
		if ( Jetpack::is_module_active( 'custom-content-types' ) ) {
			add_image_size( 'hestia-portfolio', 360, 300, true );
		}
	}

	/**
	 * Maybe register front page strings.
	 */
	private function maybe_register_front_page_strings() {
		if ( function_exists( 'hestia_features_register_strings' ) ) {
			add_action( 'after_setup_theme', 'hestia_features_register_strings', 11 );
		}
	}

	/**
	 * Add new Gutenberg templates on Otter plugin.
	 *
	 * @return array
	 */
	function add_gutenberg_templates( $templates_list ) {

		$current_theme = apply_filters( 'ti_wl_theme_name', wp_get_theme()->Name );

		$templates = array(
			array(
				'title'          => '',
				'type'           => 'block',
				'author'         => $current_theme,
				'keywords'       => array( 'big title', 'header' ),
				'categories'     => array( 'header' ),
				'template_url'   => get_template_directory_uri() . '/gutenberg/blocks/big-title/template.json',
				'screenshot_url' => get_template_directory_uri() . '/gutenberg/blocks/big-title/screenshot.png',
			),
			array(
				'title'          => '',
				'type'           => 'block',
				'author'         => $current_theme,
				'keywords'       => array( 'features', 'services', 'icons' ),
				'categories'     => array( 'content' ),
				'template_url'   => get_template_directory_uri() . '/gutenberg/blocks/features/template.json',
				'screenshot_url' => get_template_directory_uri() . '/gutenberg/blocks/features/screenshot.png',
			),
			array(
				'title'          => '',
				'type'           => 'block',
				'author'         => $current_theme,
				'keywords'       => array( 'about', 'description' ),
				'categories'     => array( 'content' ),
				'template_url'   => get_template_directory_uri() . '/gutenberg/blocks/about/template.json',
				'screenshot_url' => get_template_directory_uri() . '/gutenberg/blocks/about/screenshot.png',
			),
			array(
				'title'          => '',
				'type'           => 'block',
				'author'         => $current_theme,
				'keywords'       => array( 'testimonial', 'clients', 'customer' ),
				'categories'     => array( 'content' ),
				'template_url'   => get_template_directory_uri() . '/gutenberg/blocks/clients/template.json',
				'screenshot_url' => get_template_directory_uri() . '/gutenberg/blocks/clients/screenshot.png',
			),
			array(
				'title'          => '',
				'type'           => 'block',
				'author'         => $current_theme,
				'keywords'       => array( 'team', 'people' ),
				'categories'     => array( 'content' ),
				'template_url'   => get_template_directory_uri() . '/gutenberg/blocks/team/template.json',
				'screenshot_url' => get_template_directory_uri() . '/gutenberg/blocks/team/screenshot.png',
			),
		);

		$list = array_merge( $templates, $templates_list );

		return $list;
	}

	/**
	 * Set generic strings.
	 */
	public function set_i18n() {
		$this->generic_strings = array(
			'header_title_defaut'      => esc_html__( 'Lorem Ipsum', 'hestia-pro' ),
			'header_content_defaut'    => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
			'theme_info_title'         => esc_html__( 'Hestia', 'hestia-pro' ),
			'blog_subscribe_widgets'   => esc_html__( 'Blog Subscribe Section', 'hestia-pro' ),
			'onboarding_message'       => esc_html__( 'This process will set up your website, install required plugins, import demo content (pages, posts, media) and set up the customizer options.', 'hestia-pro' ),
			'sites_library'            => esc_html__( 'Sites Library', 'hestia-pro' ),
			'contact_form_description' => sprintf(
				/* translators: %1$s is Plugin name */
				esc_html__( 'In order to add a contact form to this section, you need to install the %1$s plugin. Then follow %2$sthis guide%3$s to create your form.', 'hestia-pro' ),
				esc_html( 'WPForms Lite' ),
				'<a href="' . esc_url( 'https://docs.themeisle.com/article/949-how-to-create-the-hestia-contact-form-in-wpforms' ) . '" target="_blank">',
				'</a>'
			),
		);
	}

	/**
	 * Add font awesome & v4 shims.
	 */
	public function enqueue_font_awesome() {
		global $hestia_load_fa;

		if ( $hestia_load_fa !== true ) {
			return false;
		}

		wp_enqueue_style( 'font-awesome-5-all', get_template_directory_uri() . '/assets/font-awesome/css/all.min.css', array(), HESTIA_VENDOR_VERSION );
		if ( $this->should_load_shim() ) {
			wp_enqueue_style( 'font-awesome-4-shim', get_template_directory_uri() . '/assets/font-awesome/css/v4-shims.min.css', array(), HESTIA_VENDOR_VERSION );
		}

		return true;
	}

	/**
	 * Detect if we should load font awesome shim based on if it's a new install of the theme or not.
	 *
	 * @return bool
	 */
	public function should_load_shim() {

		$should_load = get_option( 'hestia_load_shim' );

		if ( ! empty( $should_load ) ) {
			return $should_load === 'yes';
		}

		$is_switch_theme    = current_filter() === 'after_switch_theme';
		$slug               = $this->get_theme_slug( $is_switch_theme );
		$key                = str_replace( '-', '_', strtolower( trim( $slug ) ) );
		$theme_install_time = get_option( $key . '_install' );
		$current_time       = time();
		if ( empty( $current_time ) || empty( $theme_install_time ) ) {
			return false;
		}

		if ( ( $current_time - $theme_install_time ) > 60 ) {
			update_option( 'hestia_load_shim', 'yes' );
			return true;
		}

		update_option( 'hestia_load_shim', 'no' );
		return false;
	}

	/**
	 * Get switched theme slug or parent theme slug if it's a child theme.
	 * SDK does not register activation time for child themes.
	 *
	 * @param bool $switch Is switch theme flag.
	 *
	 * @return string
	 */
	private function get_theme_slug( $switch = false ) {
		$base_file = get_template_directory() . '/style.css';
		$dir       = dirname( $base_file );
		$slug      = basename( $dir );

		if ( $switch !== true ) {
			return $slug;
		}

		$previous_theme_slug = get_option( 'theme_switched' );
		$previous_theme      = wp_get_theme( $previous_theme_slug );
		if ( $previous_theme->exists() ) {
			// Get parent theme activation time, sdk does not register for child themes
			$slug = ! empty( $previous_theme->get( 'Template' ) ) ? $previous_theme->get( 'Template' ) : $previous_theme->get( 'TextDomain' );
		}

		return $slug;
	}


	/**
	 *  Check for all enqueued styles for font-awesome.min.css.
	 */
	function check_fa4_styles() {

		global $wp_styles;
		foreach ( $wp_styles->queue as $style ) {
			if ( strstr( $wp_styles->registered[ $style ]->src, 'font-awesome.min.css' ) ) {
				update_option( 'hestia_load_shim', 'yes' );
				return true;
			}
		}
		return false;
	}

}
