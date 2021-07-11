<?php
/**
 * The logger model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Loader;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Logger module for ThemeIsle SDK.
 */
class Logger extends Abstract_Module {
	/**
	 * Endpoint where to collect logs.
	 */
	const TRACKING_ENDPOINT = 'https://api.themeisle.com/tracking/log';


	/**
	 * Check if we should load the module for this product.
	 *
	 * @param Product $product Product to load the module for.
	 *
	 * @return bool Should we load ?
	 */
	public function can_load( $product ) {

		return apply_filters( $product->get_slug() . '_sdk_enable_logger', true );
	}

	/**
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Logger Module object.
	 */
	public function load( $product ) {
		$this->product = $product;
		$this->setup_notification();
		$this->setup_actions();

		return $this;
	}

	/**
	 * Setup notification on admin.
	 */
	public function setup_notification() {
		if ( ! $this->product->is_wordpress_available() ) {
			return;
		}

		add_filter( 'themeisle_sdk_registered_notifications', [ $this, 'add_notification' ] );

	}

	/**
	 * Setup tracking actions.
	 */
	public function setup_actions() {
		if ( ! $this->is_logger_active() ) {
			return;
		}
		$action_key = $this->product->get_key() . '_log_activity';
		if ( ! wp_next_scheduled( $action_key ) ) {
			wp_schedule_single_event( time() + ( rand( 1, 24 ) * 3600 ), $action_key );
		}
		add_action( $action_key, array( $this, 'send_log' ) );

	}

	/**
	 * Check if the logger is active.
	 *
	 * @return bool Is logger active?
	 */
	private function is_logger_active() {
		$default = 'no';

		if ( ! $this->product->is_wordpress_available() ) {
			$default = 'yes';
		} else {
			$pro_slug = $this->product->get_pro_slug();

			if ( ! empty( $pro_slug ) ) {
				$all_products = Loader::get_products();
				if ( isset( $all_products[ $pro_slug ] ) ) {
					$default = 'yes';
				}
			}
		}

		return ( get_option( $this->product->get_key() . '_logger_flag', $default ) === 'yes' );
	}

	/**
	 * Add notification to queue.
	 *
	 * @param array $all_notifications Previous notification.
	 *
	 * @return array All notifications.
	 */
	public function add_notification( $all_notifications ) {

		$message = apply_filters( $this->product->get_key() . '_logger_heading', 'Do you enjoy <b>{product}</b>? Become a contributor by opting in to our anonymous data tracking. We guarantee no sensitive data is collected.' );

		$message       = str_replace(
			array( '{product}' ),
			$this->product->get_friendly_name(),
			$message
		);
		$button_submit = apply_filters( $this->product->get_key() . '_logger_button_submit', 'Sure, I would love to help.' );
		$button_cancel = apply_filters( $this->product->get_key() . '_logger_button_cancel', 'No, thanks.' );

		$all_notifications[] = [
			'id'      => $this->product->get_key() . '_logger_flag',
			'message' => $message,
			'ctas'    => [
				'confirm' => [
					'link' => '#',
					'text' => $button_submit,
				],
				'cancel'  => [
					'link' => '#',
					'text' => $button_cancel,
				],
			],
		];

		return $all_notifications;
	}

	/**
	 * Send the statistics to the api endpoint.
	 */
	public function send_log() {
		$environment                    = array();
		$theme                          = wp_get_theme();
		$environment['theme']           = array();
		$environment['theme']['name']   = $theme->get( 'Name' );
		$environment['theme']['author'] = $theme->get( 'Author' );
		$environment['theme']['parent'] = $theme->parent() !== false ? $theme->parent()->get( 'Name' ) : $theme->get( 'Name' );
		$environment['plugins']         = get_option( 'active_plugins' );
		global $wp_version;
		wp_remote_post(
			self::TRACKING_ENDPOINT,
			array(
				'method'      => 'POST',
				'timeout'     => 3,
				'redirection' => 5,
				'body'        => array(
					'site'        => get_site_url(),
					'slug'        => $this->product->get_slug(),
					'version'     => $this->product->get_version(),
					'wp_version'  => $wp_version,
					'locale'      => get_locale(),
					'data'        => apply_filters( $this->product->get_key() . '_logger_data', array() ),
					'environment' => $environment,
					'license'     => apply_filters( $this->product->get_key() . '_license_status', '' ),
				),
			)
		);
	}
}
