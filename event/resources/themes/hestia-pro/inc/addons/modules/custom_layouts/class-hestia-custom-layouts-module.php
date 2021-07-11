<?php
/**
 * Custom layouts module
 *
 * @package Inc/Addons/Modules/Custom_Layouts
 */

/**
 * Class Hestia_Custom_Layouts_Module
 */
class Hestia_Custom_Layouts_Module extends Hestia_Abstract_Module {

	/**
	 * Custom layouts posts map.
	 *
	 * @var array $post_map
	 */
	public static $post_map = array();

	/**
	 * Theme hooks.
	 *
	 * @var array
	 */
	static public $hooks = array(
		'header'     => array(
			'hestia_before_header_hook',
			'hestia_after_header_hook',
			'hestia_before_header_content_hook',
			'hestia_after_header_content_hook',
		),
		'footer'     => array(
			'hestia_before_footer_hook',
			'hestia_after_footer_hook',
			'hestia_before_footer_content_hook',
			'hestia_after_footer_content_hook',
			'hestia_before_footer_widgets_hook',
			'hestia_after_footer_widgets_hook',
		),
		'frontpage'  => array(
			'hestia_before_big_title_section_hook',
			'hestia_before_big_title_section_content_hook',
			'hestia_top_big_title_section_content_hook',
			'hestia_big_title_section_buttons',
			'hestia_bottom_big_title_section_content_hook',
			'hestia_after_big_title_section_content_hook',
			'hestia_after_big_title_section_hook',
			'hestia_before_team_section_hook',
			'hestia_before_team_section_content_hook',
			'hestia_top_team_section_content_hook',
			'hestia_bottom_team_section_content_hook',
			'hestia_after_team_section_content_hook',
			'hestia_after_team_section_hook',
			'hestia_before_features_section_hook',
			'hestia_before_features_section_content_hook',
			'hestia_top_features_section_content_hook',
			'hestia_bottom_features_section_content_hook',
			'hestia_after_features_section_content_hook',
			'hestia_after_features_section_hook',
			'hestia_before_pricing_section_hook',
			'hestia_before_pricing_section_content_hook',
			'hestia_top_pricing_section_content_hook',
			'hestia_bottom_pricing_section_content_hook',
			'hestia_after_pricing_section_content_hook',
			'hestia_after_pricing_section_hook',
			'hestia_before_about_section_hook',
			'hestia_after_about_section_hook',
			'hestia_before_shop_section_hook',
			'hestia_before_shop_section_content_hook',
			'hestia_top_shop_section_content_hook',
			'hestia_bottom_shop_section_content_hook',
			'hestia_after_shop_section_content_hook',
			'hestia_after_shop_section_hook',
			'hestia_before_testimonials_section_hook',
			'hestia_before_testimonials_section_content_hook',
			'hestia_top_testimonials_section_content_hook',
			'hestia_bottom_testimonials_section_content_hook',
			'hestia_after_testimonials_section_content_hook',
			'hestia_after_testimonials_section_hook',
			'hestia_before_subscribe_section_hook',
			'hestia_before_subscribe_section_content_hook',
			'hestia_top_subscribe_section_content_hook',
			'hestia_bottom_subscribe_section_content_hook',
			'hestia_after_subscribe_section_content_hook',
			'hestia_after_subscribe_section_hook',
			'hestia_before_blog_section_hook',
			'hestia_before_blog_section_content_hook',
			'hestia_top_blog_section_content_hook',
			'hestia_bottom_blog_section_content_hook',
			'hestia_after_blog_section_content_hook',
			'hestia_after_blog_section_hook',
			'hestia_before_contact_section_hook',
			'hestia_before_contact_section_content_hook',
			'hestia_top_contact_section_content_hook',
			'hestia_bottom_contact_section_content_hook',
			'hestia_after_contact_section_content_hook',
			'hestia_after_contact_section_hook',
			'hestia_before_portfolio_section_hook',
			'hestia_before_portfolio_section_content_hook',
			'hestia_top_portfolio_section_content_hook',
			'hestia_bottom_portfolio_section_content_hook',
			'hestia_after_portfolio_section_content_hook',
			'hestia_after_portfolio_section_hook',
			'hestia_before_clients_bar_section_hook',
			'hestia_clients_bar_section_content_hook',
			'hestia_after_clients_bar_section_hook',
			'hestia_before_ribbon_section_hook',
			'hestia_after_ribbon_section_hook',
		),
		'post'       => array(
			'hestia_before_single_post_article',
			'hestia_after_single_post_article',
		),
		'page'       => array(
			'hestia_before_page_content',
		),
		'sidebar'    => array(
			'hestia_before_sidebar_content',
			'hestia_after_sidebar_content',
		),
		'blog'       => array(
			'hestia_before_index_posts_loop',
			'hestia_before_index_content',
			'hestia_after_archive_content',
		),
		'pagination' => array(
			'hestia_before_pagination',
			'hestia_after_pagination',
		),
	);

	/**
	 * Classes to load.
	 *
	 * @var array
	 */
	protected $classes_to_load = array();

	/**
	 * Hestia_Custom_Layouts_Module constructor.
	 */
	public function __construct() {
		$this->classes_to_load = array(
			'Hestia_Server'              => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/rest',
			'Hestia_Conditional_Display' => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin',
			'Hestia_Layouts_Metabox'     => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin',
			'Hestia_Php_Editor_Admin'    => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin',
			'Hestia_View_Hooks'          => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin',
		);
	}

	/**
	 * Check if the module should load.
	 *
	 * @return bool
	 */
	function should_load() {
		return true;
	}

	/**
	 * Get hooks.
	 */
	private function get_additional_hooks() {
		if ( class_exists( 'WooCommerce' ) ) {
			self::$hooks['woocommerce'] = array(
				'hestia_before_shop_sidebar',
				'hestia_after_shop_sidebar',
				'hestia_before_shop_sidebar_content',
				'hestia_after_shop_sidebar_content',
			);
		}
	}

	/**
	 * Run module
	 */
	function run_module() {
		$this->get_additional_hooks();
		$this->do_admin_actions();
		add_action( 'wp', array( $this, 'run_public' ) );
	}

	/**
	 * Run public actions
	 */
	public function run_public() {
		if ( $this->map_custom_layouts() !== true ) {
			return false;
		}
		$this->do_public_actions();
		return true;
	}

	/**
	 * Load public files.
	 */
	private function do_public_actions() {
		if ( is_admin() ) {
			return false;
		}

		require_once( HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin/builders/class-hestia-abstract-builders.php' );
		require_once( HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin/builders/class-hestia-loader.php' );
		new Hestia_Loader();

		return true;
	}

	/**
	 * Map custom layouts.
	 *
	 * @return bool
	 */
	private function map_custom_layouts() {
		if ( $this->is_builder_preview() ) {
			return true;
		}
		$cache = get_transient( 'custom_layouts_posts_map' );
		if ( ! empty( $cache ) ) {
			self::$post_map = $cache;

			return true;
		}
		$query = new \WP_Query(
			array(
				'post_type'              => 'hestia_layouts',
				'posts_per_page'         => 100,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'publish',
			)
		);
		if ( ! $query->have_posts() ) {
			return false;
		}
		foreach ( $query->posts as $pid ) {
			$layout   = get_post_meta( $pid, 'custom-layout-options-layout', true );
			$priority = get_post_meta( $pid, 'custom-layout-options-priority', true );
			if ( $layout === 'hooks' ) {
				$layout = get_post_meta( $pid, 'custom-layout-options-hook', true );
			}
			self::$post_map[ $layout ][ $pid ] = $priority;
		}
		set_transient( 'custom_layouts_posts_map', self::$post_map, DAY_IN_SECONDS );

		return true;
	}

	/**
	 * Do admin related actions.
	 */
	private function do_admin_actions() {
		$this->register_custom_post_type();
		$this->run_hooks();

		return true;
	}

	/**
	 * Register Custom Layouts post type.
	 */
	private function register_custom_post_type() {

		$labels = array(
			'name'          => esc_html_x( 'Custom Layouts', 'advanced-hooks general name', 'hestia-pro' ),
			'singular_name' => esc_html_x( 'Custom Layout', 'advanced-hooks singular name', 'hestia-pro' ),
			'search_items'  => esc_html__( 'Search Custom Layouts', 'hestia-pro' ),
			'all_items'     => esc_html__( 'Custom Layouts', 'hestia-pro' ),
			'edit_item'     => esc_html__( 'Edit Custom Layout', 'hestia-pro' ),
			'view_item'     => esc_html__( 'View Custom Layout', 'hestia-pro' ),
			'add_new'       => esc_html__( 'Add New', 'hestia-pro' ),
			'update_item'   => esc_html__( 'Update Custom Layout', 'hestia-pro' ),
			'add_new_item'  => esc_html__( 'Add New', 'hestia-pro' ),
			'new_item_name' => esc_html__( 'New Custom Layout Name', 'hestia-pro' ),
		);

		$args = array(
			'labels'              => $labels,
			'show_in_menu'        => 'themes.php',
			'public'              => true,
			'show_ui'             => true,
			'query_var'           => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'elementor' ),
		);

		register_post_type( 'hestia_layouts', apply_filters( 'hestia_layouts_post_type_args', $args ) );
	}

	/**
	 * Check if is builder preview.
	 *
	 * @return bool
	 */
	private function is_builder_preview() {
		if ( array_key_exists( 'preview', $_GET ) && ! empty( $_GET['preview'] ) ) {
			return true;
		}

		if ( array_key_exists( 'elementor-preview', $_GET ) && ! empty( $_GET['elementor-preview'] ) ) {
			return true;
		}

		if ( array_key_exists( 'brizy-edit', $_GET ) && ! empty( $_GET['brizy-edit'] ) ) {
			return true;
		}

		if ( class_exists( 'FLBuilderModel', false ) ) {
			if ( \FLBuilderModel::is_builder_active() === true ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add hooks and filters.
	 */
	private function run_hooks() {
		/**
		 * Allow custom layouts cpt to be edited with Beaver Builder.
		 */
		if ( class_exists( 'FLBuilderModel', false ) ) {
			add_filter( 'fl_builder_post_types', array( $this, 'add_custom_layouts_compatibility' ), 10, 1 );
		}

		/**
		 * Add support for Brizy.
		 */
		add_filter( 'brizy_supported_post_types', array( $this, 'add_custom_layouts_compatibility' ) );

		/**
		 * Add a custom template for Custom Layouts cpt preview.
		 */
		add_filter( 'single_template', array( $this, 'custom_layouts_single_template' ) );

		/**
		 * Enqueue admin scripts and styles.
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		/**
		 * Remove custom layouts transient.
		 */
		add_action( 'save_post', array( $this, 'remove_custom_layouts_transient' ) );

		/** Drop page templates for custom layouts post type */
		add_filter( 'theme_hestia_layouts_templates', '__return_empty_array', PHP_INT_MAX );
	}

	/**
	 * Add Beaver Builder Compatibility
	 *
	 * @param array $value Post types.
	 *
	 * @return array
	 */
	public function add_custom_layouts_compatibility( $value ) {
		$value[] = 'hestia_layouts';

		return $value;
	}

	/**
	 * Set path to hestia_layouts template.
	 *
	 * @param string $single Path to single.php .
	 *
	 * @return string
	 */
	public function custom_layouts_single_template( $single ) {
		global $post;
		if ( $post->post_type === 'hestia_layouts' && file_exists( HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin/template.php' ) ) {
			return HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin/template.php';
		}

		return $single;
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		global $post;
		if ( $post !== null && $post->post_type !== 'hestia_layouts' ) {
			return false;
		}

		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return false;
		}

		wp_enqueue_code_editor(
			array(
				'type'       => 'application/x-httpd-php',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);
		wp_enqueue_script( 'hestia-pro-custom-layout', HESTIA_ADDONS_URI . 'modules/custom_layouts/assets/js/script.js', array(), HESTIA_VERSION );
		wp_localize_script(
			'hestia-pro-custom-layout',
			'hestiaCustomLayouts',
			array(
				'customEditorEndpoint' => rest_url( '/wp/v2/hestia_layouts/' . $post->ID ),
				'nonce'                => wp_create_nonce( 'wp_rest' ),
				'phpError'             => esc_html__( 'There are some errors in your PHP code. Please fix them before saving the code.', 'hestia-pro' ),
				'magicTags'            => Hestia_Layouts_Metabox::$magic_tags,
				'strings'              => array(
					'magicTagsDescription' => esc_html__( 'You can add the following tags in your template:', 'hestia-pro' ),
				),
			)
		);

		Hestia_Core::rtl_enqueue_style( 'hestia-custom-layouts', HESTIA_ADDONS_URI . 'modules/custom_layouts/assets/admin_style.min.css', array(), HESTIA_VERSION );

		return true;
	}

	/**
	 * Remove custom layouts transient at post save.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return bool
	 */
	function remove_custom_layouts_transient( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		$post_type = get_post_type( $post_id );
		if ( 'hestia_layouts' !== $post_type ) {
			return false;
		}
		delete_transient( 'custom_layouts_posts_map' );

		return true;
	}
}
