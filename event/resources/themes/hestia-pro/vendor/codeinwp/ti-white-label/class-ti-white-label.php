<?php
/**
 * ThemeIsle - White Label class.
 * @package ti-white-label
 */


define( 'WHITE_LABEL_NAMESPACE', 'ti-white-label' );
define( 'WHITE_LABEL_VERSION', '1.0.7' );
define( 'WHITE_LABEL_PATH', '/vendor/codeinwp/ti-white-label' );
define( 'WHITE_LABEL_ROOT', dirname( __FILE__ ) );

/**
 * Class Ti_White_Label
 */
class Ti_White_Label {

	/**
	 * Instance of Ti_White_Label
	 *
	 * @var Ti_White_Label
	 */
	protected static $instance = null;

	/**
	 * Instance of Ti_Withe_Label_Admin
	 *
	 * @var Ti_Withe_Label_Admin
	 */
	protected $admin = null;

	/**
	 * Product base file, with the proper metadata.
	 *
	 * @var string $base_file The file with headers.
	 */
	protected static $base_file;

	/**
	 * Type of the product.
	 *
	 * @var string $type The product type ( plugin | theme ).
	 */
	private $type;

	/**
	 * File path to enqueue files.
	 *
	 * @var string $file The file name.
	 */
	private $file_path;

	/**
	 * Product name, fetched from the file headers.
	 *
	 * @var string $name The product name.
	 */
	private $name;

	/**
	 * Product store url.
	 *
	 * @var string $store_url The store url.
	 */
	private $store_url;

	/**
	 * Product store/author name.
	 *
	 * @var string $store_name The store name.
	 */
	private $store_name;

	/**
	 * Product author url.
	 *
	 * @var string $author_url Author url,
	 */
	private $author_url;

	/**
	 * Instantiate the class.
	 *
	 * @static
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $base_file Product base file.
	 *
	 * @return Ti_White_Label
	 */
	public static function instance( $base_file = null ) {
		if ( is_null( self::$instance ) ) {
			self::$base_file = $base_file;
			self::$instance  = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Admin and API init.
	 *
	 * @var null
	 */
	private function init() {
		if ( ! $this->should_load() ) {
			return;
		}
		$this->setup_admin();
		$this->setup_api();
	}

	/**
	 * Setup admin functionality.
	 *
	 * @return bool
	 */
	private function setup_admin() {
		require_once 'includes/class-ti-white-label-admin.php';
		if ( ! class_exists( 'Ti_Withe_Label_Admin' ) ) {
			return false;
		}
		$this->setup_from_path();
		$this->setup_from_file_headers();

		$settings = array(
			'type'         => $this->type,
			'product_name' => $this->name,
			'file_path'    => $this->file_path,
		);
		if ( $this->type === 'plugin' ) {
			$settings['plugin_base_name'] = plugin_basename( self::$base_file );
		}
		$this->admin = new Ti_Withe_Label_Admin( $settings );
		$this->admin->init();

		require_once 'includes/class-ti-white-label-markup.php';
		if ( ! class_exists( 'Ti_White_Label_Markup' ) ) {
			return false;
		}
		new Ti_White_Label_Markup( $settings );
		return true;
	}

	/**
	 * Get product type from path.
	 */
	public function setup_from_path() {
		$exts = explode( '.', self::$base_file );
		$ext  = $exts[ count( $exts ) - 1 ];
		if ( 'css' === $ext ) {
			$this->type      = 'theme';
			$this->file_path = get_template_directory_uri();
		}
		if ( 'php' === $ext ) {
			$this->type      = 'plugin';
			$this->file_path = plugin_dir_url( self::$base_file );
		}
	}

	/**
	 * Setup props from file headers.
	 */
	public function setup_from_file_headers() {
		$file_headers = array();
		if ( 'plugin' === $this->type ) {
			$file_headers['Name']       = 'Plugin Name';
			$file_headers['AuthorName'] = 'Author';
			$file_headers['AuthorURI']  = 'Author URI';
		}
		if ( 'theme' === $this->type ) {
			$file_headers['Name']       = 'Theme Name';
			$file_headers['AuthorName'] = 'Author';
			$file_headers['AuthorURI']  = 'Author URI';
		}
		$file_headers = get_file_data( self::$base_file, $file_headers );

		$this->name       = $file_headers['Name'];
		$this->store_name = $file_headers['AuthorName'];
		$this->author_url = $file_headers['AuthorURI'];
		$this->store_url  = $file_headers['AuthorURI'];
	}

	/**
	 * Setup the restful functionality.
	 *
	 * @return void
	 */
	public function setup_api() {
		require_once 'includes/class-ti-white-label-rest-server.php';
		if ( ! class_exists( 'Ti_White_Label_Rest_Server' ) ) {
			return;
		}
		new Ti_White_Label_Rest_Server();
	}

	/**
	 * Decide if the module should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		return apply_filters( 'ti_white_label_filter_should_load', true );
	}

	/**
	 * Disallow object clone
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __clone() {
	}

	/**
	 * Disable un-serializing
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __wakeup() {
	}
}
