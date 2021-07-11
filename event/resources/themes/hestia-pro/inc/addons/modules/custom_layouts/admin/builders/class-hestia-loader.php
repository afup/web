<?php
/**
 * Factory for loading the builders compatibility.
 *
 * @package Inc/Addons/Modules/Custom_Layouts/Admin/Builders
 */

/**
 * Class Hestia_Loader
 */
class Hestia_Loader {

	/**
	 * Possible builders list.
	 *
	 * @var array List of them.
	 */
	public static $builders_list = array(
		'Hestia_Default_Editor',
		'Hestia_Php_Editor',
		'Hestia_Elementor',
		'Hestia_Brizy',
		'Hestia_Beaver',
	);

	/**
	 * List of possible builders.
	 *
	 * @var Hestia_Abstract_Builders[] $available_builders List.
	 */
	private static $available_builders = array();

	/**
	 * Hooks map to check.
	 *
	 * @var array Hooks map.
	 */
	protected static $hooks_map = array(
		'hestia_do_header'       => array(
			'hooks_to_deactivate' => array( 'hestia_do_header', 'hestia_do_top_bar' ),
			'posts_map_key'       => 'header',
		),
		'hestia_do_page_header'  => array(
			'hooks_to_deactivate' => array( 'hestia_do_page_header' ),
			'posts_map_key'       => 'page_header',
		),
		'hestia_do_footer'       => array(
			'hooks_to_deactivate' => array( 'hestia_do_footer' ),
			'posts_map_key'       => 'footer',
		),
		'hestia_do_404'          => array(
			'hooks_to_deactivate' => array( 'hestia_do_404' ),
			'posts_map_key'       => 'not_found',
		),
		'hestia_do_offline'      => array(
			'hooks_to_deactivate' => array( 'hestia_do_offline' ),
			'posts_map_key'       => 'offline',
		),
		'hestia_do_server_error' => array(
			'hooks_to_deactivate' => array( 'hestia_do_server_error' ),
			'posts_map_key'       => 'server_error',
		),
	);

	/**
	 * Register actions and editors.
	 */
	public function __construct() {
		if ( function_exists( 'do_blocks' ) ) {
			add_filter( 'hestia_post_content', 'do_blocks' );
		}
		add_filter( 'hestia_post_content', 'wptexturize' );
		add_filter( 'hestia_post_content', 'convert_smilies' );
		add_filter( 'hestia_post_content', 'convert_chars' );
		add_filter( 'hestia_post_content', 'wpautop' );
		add_filter( 'hestia_post_content', 'shortcode_unautop' );
		add_filter( 'hestia_post_content', 'do_shortcode' );

		add_action( 'template_redirect', array( $this, 'render_single' ) );

		foreach ( self::$builders_list as $index => $builder ) {
			$filename  = 'class-' . str_replace( '_', '-', strtolower( $builder ) ) . '.php';
			$full_path = HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts/admin/builders/' . $filename;
			if ( ! is_file( $full_path ) ) {
				continue;
			}

			require $full_path;
			$builder = new $builder;
			/**
			 * Builder instance.
			 *
			 * @var Hestia_Abstract_Builders $builder Builder object.
			 */
			if ( ! $builder->should_load() ) {
				continue;
			}
			$builder->register_hooks();
			self::$available_builders[ $builder->get_builder_id() ] = $builder;
		}

		foreach ( Hestia_Custom_Layouts_Module::$post_map as $layout => $posts ) {
			switch ( $layout ) {
				case 'header':
					add_action( 'hestia_do_header', array( $this, 'render_first_markup' ), 1 );
					break;
				case 'footer':
					add_action( 'hestia_do_footer', array( $this, 'render_first_markup' ), 1 );
					break;
				case 'not_found':
					add_action( 'hestia_do_404', array( $this, 'render_first_markup' ), 1 );
					break;
				case 'offline':
					add_action( 'hestia_do_offline', array( $this, 'render_first_markup' ), 1 );
					break;
				case 'page_header':
					add_action( 'hestia_do_page_header', array( $this, 'render_first_markup' ), 1 );
					break;
				case 'server_error':
					add_action( 'hestia_do_server_error', array( $this, 'render_first_markup' ), 1 );
					break;
				default:
					add_action( $layout, array( $this, 'render_all_markup' ), 1 );
					break;
			}
		}

	}

	/**
	 * Render all custom layouts attached.
	 */
	public function render_all_markup() {
		$this->render_inline_markup( false );
	}

	/**
	 * Render first custom layouts attached.
	 */
	public function render_first_markup() {
		$this->render_inline_markup( true );
	}

	/**
	 * Render inline markup.
	 *
	 * @return bool Has rendered?
	 */
	public function render_inline_markup( $single = true ) {
		// Remove rendering on custom layout.
		if ( is_singular( 'hestia_layouts' ) ) {
			return false;
		}
		$current_hook        = current_filter();
		$hooks_to_deactivate = isset( self::$hooks_map[ $current_hook ]['hooks_to_deactivate'] ) ? self::$hooks_map[ $current_hook ]['hooks_to_deactivate'] : array();
		$posts_map_key       = isset( self::$hooks_map[ $current_hook ]['posts_map_key'] ) ? self::$hooks_map[ $current_hook ]['posts_map_key'] : $current_hook;

		$posts = Hestia_Custom_Layouts_Module::$post_map[ $posts_map_key ];

		if ( empty( $posts ) ) {
			return false;
		}
		asort( $posts );
		foreach ( $posts as $post_id => $priority ) {
			$editor = Hestia_Abstract_Builders::get_post_builder( $post_id );

			if ( ! isset( self::$available_builders[ $editor ] ) ) {
				continue;
			}

			if ( ! self::$available_builders[ $editor ]->check_conditions( $post_id ) ) {
				continue;
			}
			if ( $single ) {
				foreach ( $hooks_to_deactivate as $hook ) {
					remove_all_actions( $hook );
				}
			}
			self::$available_builders[ $editor ]->render( $post_id );

			if ( $single ) {
				return true;
			}
		}

		return true;
	}


	/**
	 * Footer markup on Custom Layouts preview.
	 */
	public function render_footer() {
		echo '<footer class="hestia-custom-footer" itemscope="itemscope" itemtype="https://schema.org/WPFooter">';
		$this->render_content();
		echo '</footer>';
	}


	/**
	 * This function handles the display on Custom Layouts preview, the single of Custom Layouts custom post type.
	 *
	 * @return bool
	 */
	public function render_single() {
		if ( ! is_singular( 'hestia_layouts' ) ) {
			return false;
		}
		$post_id = get_the_ID();

		$layout = get_post_meta( $post_id, 'custom-layout-options-layout', true );
		switch ( $layout ) {
			case 'header':
				remove_all_actions( 'hestia_do_header' );
				remove_all_actions( 'hestia_do_top_bar' );
				add_action( 'hestia_do_header', array( $this, 'render_header' ) );
				break;
			case 'footer':
				remove_all_actions( 'hestia_do_footer' );
				add_action( 'hestia_do_footer', array( $this, 'render_footer' ) );
				break;
			case 'offline':
			case 'server_error':
				remove_all_actions( 'hestia_do_footer' );
				remove_all_actions( 'hestia_do_header' );
				add_action( 'hestia_layouts_template_content', array( $this, 'render_content' ) );
				break;
			case 'not_found':
				add_action( 'hestia_layouts_template_content', array( $this, 'render_content' ) );
				break;
			default:
				remove_all_actions( 'hestia_do_footer' );
				remove_all_actions( 'hestia_do_header' );
				remove_all_actions( 'hestia_do_top_bar' );
				remove_all_actions( 'hestia_layouts_template_content' );
				add_action( 'hestia_layouts_template_content', array( $this, 'render_content' ) );
				break;
		}

		return true;
	}


	/**
	 * Header markup on Custom Layouts preview.
	 */
	public function render_header() {
		echo '<header class="hestia-custom-header" itemscope="itemscope" itemtype="https://schema.org/WPHeader">';
		$this->render_content();
		echo '</header>';
	}

	/**
	 * Get the layout content.
	 */
	public function render_content() {
		while ( have_posts() ) {
			the_post();
			$post_id = get_the_ID();
			$builder = Hestia_Abstract_Builders::get_post_builder( $post_id );
			if ( $builder !== 'custom' ) {
				the_content();
				continue;
			}
			$file_name = get_post_meta( $post_id, 'hestia_editor_content', true );
			if ( empty( $file_name ) ) {
				continue;
			}
			$wp_upload_dir = wp_upload_dir( null, false );
			$upload_dir    = $wp_upload_dir['basedir'] . '/hestia-theme/';
			$file_path     = $upload_dir . $file_name . '.php';
			if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
				include_once( $file_path );
			}
		}
	}
}
