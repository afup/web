<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-04-15
 * @package class-themeisle-ob-import-logger.php
 */

/**
 * Class Themeisle_OB_WP_Import_Logger
 */
class Themeisle_OB_WP_Import_Logger {

	/**
	 * Emojis mapped for each case.
	 *
	 * @var array
	 */
	private $icon_map = array(
		'success'  => '✅',
		'warning'  => '⚠️',
		'progress' => '🔵',
		'error'    => '🔴️',
		'generic'  => '⚪️',
		'info'     => 'ℹ️',
	);

	/**
	 * Log file path.
	 *
	 * @var string
	 */
	private $log_file_path;

	/**
	 * Log file path url.
	 *
	 * @var string
	 */
	private $log_file_path_url;

	/**
	 * Log file name
	 *
	 * @var string
	 */
	private $log_file_name = 'ti_theme_onboarding.log';

	/**
	 * @var string
	 */
	private $log_string = '';

	/**
	 * @var Themeisle_OB_WP_Import_Logger
	 */
	private static $_instance;

	/**
	 * Themeisle_OB_WP_Import_Logger constructor.
	 */
	public function __construct() {
		if ( ! defined( 'TI_OB_DEBUG_LOG' ) ) {
			define( 'TI_OB_DEBUG_LOG', true );
		}

		if ( TI_OB_DEBUG_LOG !== true ) {
			return;
		}
		require_once( ABSPATH . 'wp-admin/includes/file.php' ); // you have to load this file
		add_action( 'shutdown', array( $this, 'log_to_file' ) );
		$this->set_log_path();
		$this->clear_log();
		$this->log_client_info();
	}

	/**
	 * Log info.
	 *
	 * @param string $label log label.
	 * @param string $value log value.
	 */
	public function log_info( $label, $value = null ) {
		if ( $value === null ) {
			$this->log( "{$label}", 'info' );

			return;
		}
		$this->log( "{$label} : {$value}", 'info' );
	}

	/**
	 * Log client info for debug purposes.
	 */
	private function log_client_info() {
		$this->log_info( "WordPress Instance Info:\n" );
		$this->log_info( 'Home URL', home_url() );
		$this->log_info( 'Site URL', site_url() );
		$this->log_info( 'WordPress Version', get_bloginfo( 'version' ) );
		$this->log_info( 'Onboarding Version', Themeisle_Onboarding::VERSION );
		$this->log_info( 'Multisite', is_multisite() ? 'Yes' : 'No' );
		$this->log_info( 'Server Info', isset( $_SERVER['SERVER_SOFTWARE'] ) ? wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) : '' );
		$this->log_info( 'PHP Version', phpversion() );
		$this->log_info( 'HTTPS', is_ssl() ? 'Yes' : 'No' );
		$this->log_info( 'PHP Max Execution Time', ini_get( 'max_execution_time' ) );
		$this->log_info( 'PHP Max Input Vars', ini_get( 'max_input_vars' ) );
		$this->log_info( 'Max Upload Size', wp_max_upload_size() );
		$this->log_info( 'Plugins:' );
		foreach ( $this->get_plugins() as $plugin ) {
			$author = strip_tags( $plugin['Author'] );
			$this->log_info( '[PLUGIN] ' . $plugin['Name'], 'v' . $plugin['Version'] . " ({$author}) " );
		}
	}

	/**
	 * Get active plugins.
	 *
	 * @return array
	 */
	private function get_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$active_plugins_data = array();
		foreach ( $active_plugins as $plugin ) {
			$data                  = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$active_plugins_data[] = $data;
		}

		return $active_plugins_data;
	}

	/**
	 * Returns the instance of the class.
	 *
	 * @return Themeisle_OB_WP_Import_Logger
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Set the log path.
	 */
	private function set_log_path() {
		$wp_upload_dir       = wp_upload_dir( null, false );
		$this->log_file_path = $wp_upload_dir['basedir'] . DIRECTORY_SEPARATOR;

		if ( ! is_dir( $this->log_file_path ) ) {
			wp_mkdir_p( $this->log_file_path );
		}

		$this->log_file_path_url = $wp_upload_dir['baseurl'] . DIRECTORY_SEPARATOR;
	}

	/**
	 * Clear the log file.
	 */
	private function clear_log() {
		if ( is_writable( $this->log_file_path . $this->log_file_name ) ) {
			unlink( $this->log_file_path . $this->log_file_name );
		}
		global $wp_filesystem;
		WP_Filesystem();
		$wp_filesystem->put_contents( $this->log_file_path . $this->log_file_name, '', 0644 );
	}

	/**
	 * Log entry.
	 *
	 * @param string $message log message.
	 * @param string $type    log type.
	 */
	public function log( $message = 'No message provided.', $type = 'error' ) {
		$log_entry         = array(
			'message' => $message,
			'type'    => array_key_exists( $type, $this->icon_map ) ? $this->icon_map[ $type ] : $this->icon_map['generic'],
			'time'    => gmdate( '[d/M/Y:H:i:s]' ),
		);
		$this->log_string .= $this->get_log_entry( $log_entry );
	}

	/**
	 * Log to file.
	 */
	public function log_to_file() {
		$log_file = $this->log_file_path . $this->log_file_name;
		global $wp_filesystem;
		WP_Filesystem();
		$content  = file_exists( $log_file ) ? $wp_filesystem->get_contents( $log_file ) : '';
		$content .= $this->log_string;
		$wp_filesystem->put_contents( $log_file, $content, 0644 );
	}

	/**
	 * Get the formatted log entry.
	 *
	 * @return string
	 */
	private function get_log_entry( $log_entry ) {
		if ( ! is_string( $log_entry['message'] ) ) {
			$log_entry['message'] = json_encode( $log_entry['message'] );
		}

		return trim( preg_replace( '/\s\s+/', ' ', "{$log_entry['time']} ({$log_entry['type']}): {$log_entry['message']}" ) ) . PHP_EOL;
	}

	/**
	 * Get the log URL.
	 */
	public function get_log_url() {
		return $this->log_file_path_url . $this->log_file_name;
	}
}
