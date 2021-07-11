<?php
/**
 * The notification model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notification module for ThemeIsle SDK.
 */
class Notification extends Abstract_Module {
	/**
	 * Show notifications only after the user has the product installed after this amount of time, in hours.
	 */
	const MIN_INSTALL_TIME = 100;
	/**
	 *  How much time should we show the notification, in days.
	 */
	const MAX_TIME_TO_LIVE = 7;

	/**
	 * Number of days between notifications.
	 */
	const TIME_BETWEEN_NOTIFICATIONS = 5;

	/**
	 * Holds a possible notification list.
	 *
	 * @var array Notifications list.
	 */
	private static $notifications = [];

	/**
	 * Show notification data.
	 */
	public static function show_notification() {

		$current_notification = self::get_last_notification();

		$notification_details = [];
		// Check if the saved notification is still present among the possible ones.
		if ( ! empty( $current_notification ) ) {
			$notification_details = self::get_notification_details( $current_notification );
			if ( empty( $notification_details ) ) {
				$current_notification = [];
			}
		}
		// Check if the notificatin is expired.
		if ( ! empty( $current_notification ) && self::is_notification_expired( $current_notification ) ) {
			update_option( $current_notification['id'], 'no' );
			self::set_last_active_notification_timestamp();
			$current_notification = [];
		}
		// If we don't have any saved notification, get a new one.
		if ( empty( $current_notification ) ) {
			$notification_details = self::get_random_notification();
			if ( empty( $notification_details ) ) {
				return;
			}
			self::set_active_notification(
				[
					'id'         => $notification_details['id'],
					'display_at' => time(),
				]
			);
		}
		if ( empty( $notification_details ) ) {
			return;
		}
		$notification_html = self::get_notification_html( $notification_details );
		do_action( $notification_details['id'] . '_before_render' );

		echo $notification_html;

		do_action( $notification_details['id'] . '_after_render' );
		self::render_snippets();
	}

	/**
	 * Get last notification details.
	 *
	 * @return array Last notification details.
	 */
	private static function get_last_notification() {
		$notification = self::get_notifications_metadata();

		return isset( $notification['last_notification'] ) ? $notification['last_notification'] : [];
	}

	/**
	 * Get notification center details.
	 *
	 * @return array Notification center details.
	 */
	private static function get_notifications_metadata() {

		$data = get_option(
			'themeisle_sdk_notifications',
			[
				'last_notification'        => [],
				'last_notification_active' => 0,
			]
		);

		return $data;

	}

	/**
	 * Check if the notification is still possible.
	 *
	 * @param array $notification Notification to check.
	 *
	 * @return array Either is still active or not.
	 */
	private static function get_notification_details( $notification ) {
		$notifications = array_filter(
			self::$notifications,
			function ( $value ) use ( $notification ) {
				if ( isset( $value['id'] ) && isset( $notification['id'] ) && $value['id'] === $notification['id'] ) {
					return true;
				}

				return false;
			}
		);

		return ! empty( $notifications ) ? reset( $notifications ) : [];
	}

	/**
	 * Check if the notification is expired.
	 *
	 * @param array $notification Notification to check.
	 *
	 * @return bool Either the notification is due.
	 */
	private static function is_notification_expired( $notification ) {
		if ( ! isset( $notification['display_at'] ) ) {
			return true;
		}

		$notifications = array_filter(
			self::$notifications,
			function ( $value ) use ( $notification ) {
				if ( isset( $value['id'] ) && isset( $notification['id'] ) && $value['id'] === $notification['id'] ) {
					return true;
				}

				return false;
			}
		);

		if ( empty( $notifications ) ) {
			return true;
		}
		$notification_definition = reset( $notifications );

		$when_to_expire = isset( $notification_definition['expires_at'] )
			? $notification_definition['expires_at'] :
			( isset( $notification_definition['expires'] )
				? ( $notification['display_at'] + $notification_definition['expires'] ) :
				( $notification['display_at'] + self::MAX_TIME_TO_LIVE * DAY_IN_SECONDS )
			);

		return ( $when_to_expire - time() ) < 0;
	}

	/**
	 * Set last notification details.
	 */
	private static function set_last_active_notification_timestamp() {
		$metadata                             = self::get_notifications_metadata();
		$metadata['last_notification_active'] = time();
		update_option( 'themeisle_sdk_notifications', $metadata );
	}

	/**
	 * Return notification to show.
	 *
	 * @return array Notification data.
	 */
	public static function get_random_notification() {
		if ( ( time() - self::get_last_active_notification_timestamp() ) < self::TIME_BETWEEN_NOTIFICATIONS * DAY_IN_SECONDS ) {
			return [];
		}

		$notifications = self::$notifications;
		$notifications = array_filter(
			$notifications,
			function ( $value ) {
				if ( isset( $value['sticky'] ) && true === $value['sticky'] ) {
					return true;
				}

				return false;
			}
		);
		// No priority notifications, use all.
		if ( empty( $notifications ) ) {
			$notifications = self::$notifications;
		}
		if ( empty( $notifications ) ) {
			return [];
		}
		$notifications = array_values( $notifications );

		return $notifications[ array_rand( $notifications, 1 ) ];

	}

	/**
	 * Get last notification details.
	 *
	 * @return int Last notification details.
	 */
	private static function get_last_active_notification_timestamp() {
		$notification = self::get_notifications_metadata();

		return isset( $notification['last_notification_active'] ) ? $notification['last_notification_active'] : 0;
	}

	/**
	 * Get last notification details.
	 *
	 * @param  array $notification Notification data.
	 */
	private static function set_active_notification( $notification ) {
		$metadata                      = self::get_notifications_metadata();
		$metadata['last_notification'] = $notification;
		update_option( 'themeisle_sdk_notifications', $metadata );
	}

	/**
	 * Get notification html.
	 *
	 * @param array $notification_details Notification details.
	 *
	 * @return string Html for notice.
	 */
	public static function get_notification_html( $notification_details ) {
		$default              = [
			'id'      => '',
			'heading' => '',
			'message' => '',
			'ctas'    => [
				'confirm' => [
					'link' => '#',
					'text' => '',
				],
				'cancel'  => [
					'link' => '#',
					'text' => '',
				],
			],
		];
		$notification_details = wp_parse_args( $notification_details, $default );

		$notification_html = '<div class="notice notice-success is-dismissible themeisle-sdk-notice" data-notification-id="' . esc_attr( $notification_details['id'] ) . '" id="' . esc_attr( $notification_details['id'] ) . '-notification"> <div class="themeisle-sdk-notification-box">';

		if ( ! empty( $notification_details['heading'] ) ) {
			$notification_html .= sprintf( '<h4>%s</h4>', wp_kses_post( $notification_details['heading'] ) );
		}
		if ( ! empty( $notification_details['message'] ) ) {
			$notification_html .= wp_kses_post( $notification_details['message'] );
		}
		$notification_html .= '<div class="actions">';

		if ( ! empty( $notification_details['ctas']['confirm']['text'] ) ) {
			$notification_html .= sprintf(
				'<a href="%s" target="_blank" class=" button button-primary %s" data-confirm="yes" >%s</a>',
				esc_url( $notification_details['ctas']['confirm']['link'] ),
				esc_attr( $notification_details['id'] . '_confirm' ),
				wp_kses_post( $notification_details['ctas']['confirm']['text'] )
			);
		}

		if ( ! empty( $notification_details['ctas']['cancel']['text'] ) ) {
			$notification_html .= sprintf(
				'<a href="%s" class=" button %s" data-confirm="no">%s</a>',
				esc_url( $notification_details['ctas']['cancel']['link'] ),
				esc_attr( $notification_details['id'] ) . '_cancel',
				wp_kses_post( $notification_details['ctas']['cancel']['text'] )
			);
		}

		$notification_html .= '</div>';
		$notification_html .= '	</div>';
		$notification_html .= '	</div>';

		return $notification_html;
	}

	/**
	 * Adds js snippet for hiding the notice.
	 */
	public static function render_snippets() {

		?>
		<style type="text/css">
			.themeisle-sdk-notification-box {
				padding: 3px;
			}

			.themeisle-sdk-notification-box .actions {
				margin-top: 6px;
				margin-bottom: 4px;
			}

			.themeisle-sdk-notification-box .button {
				margin-right: 5px;
			}
		</style>
		<script type="text/javascript">
			(function ($) {
				$(document).ready(function () {
					$('#wpbody-content').on('click', ".themeisle-sdk-notice a.button, .themeisle-sdk-notice .notice-dismiss", function (e) {

						var container = $('.themeisle-sdk-notice');
						var link = $(this);
						var notification_id = container.attr('data-notification-id');
						var confirm = link.attr('data-confirm');
						if (typeof confirm === "undefined") {
							confirm = 'no';
						}
						$.post(
							ajaxurl,
							{
								'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
								'action': 'themeisle_sdk_dismiss_notice',
								'id': notification_id,
								'confirm': confirm
							}
						);
						if (confirm === 'yes') {
							$(this).trigger('themeisle-sdk:confirmed');
						} else {
							$(this).trigger('themeisle-sdk:canceled');
						}
						container.hide();
						if (link.attr('href') === '#') {
							return false;
						}
					});
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Dismiss the notification.
	 */
	static function dismiss() {
		check_ajax_referer( (string) __CLASS__, 'nonce' );

		$id      = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$confirm = isset( $_POST['confirm'] ) ? sanitize_text_field( $_POST['confirm'] ) : 'no';

		if ( empty( $id ) ) {
			wp_send_json( [] );
		}
		$ids = wp_list_pluck( self::$notifications, 'id' );
		if ( ! in_array( $id, $ids, true ) ) {
			wp_send_json( [] );
		}
		self::set_last_active_notification_timestamp();
		update_option( $id, $confirm );
		do_action( $id . '_process_confirm', $confirm );
		wp_send_json( [] );
	}

	/**
	 * Check if we should load the notification module.
	 *
	 * @param Product $product Product to check.
	 *
	 * @return bool Should we load this?
	 */
	public function can_load( $product ) {

		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		if ( ( time() - $product->get_install_time() ) < ( self::MIN_INSTALL_TIME * HOUR_IN_SECONDS ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Setup notifications queue.
	 */
	public static function setup_notifications() {
		$notifications       = apply_filters( 'themeisle_sdk_registered_notifications', [] );
		$notifications       = array_filter(
			$notifications,
			function ( $value ) {
				if ( ! isset( $value['id'] ) ) {
					return false;
				}
				if ( get_option( $value['id'], '' ) !== '' ) {
					return false;
				}

				return apply_filters( $value['id'] . '_should_show', true );
			}
		);
		self::$notifications = $notifications;
	}
	/**
	 * Load the module logic.
	 *
	 * @param Product $product Product to load the module for.
	 *
	 * @return Notification Module instance.
	 */
	public function load( $product ) {
		if ( apply_filters( 'themeisle_sdk_hide_notifications', false ) ) {
			return;
		}
		$this->product = $product;

		$notifications       = apply_filters( 'themeisle_sdk_registered_notifications', [] );
		$notifications       = array_filter(
			$notifications,
			function ( $value ) {
				if ( ! isset( $value['id'] ) ) {
					return false;
				}
				if ( get_option( $value['id'], '' ) !== '' ) {
					return false;
				}

				return apply_filters( $value['id'] . '_should_show', true );
			}
		);
		self::$notifications = $notifications;
		add_action( 'admin_notices', array( __CLASS__, 'show_notification' ) );
		add_action( 'wp_ajax_themeisle_sdk_dismiss_notice', array( __CLASS__, 'dismiss' ) );
		add_action( 'admin_head', array( __CLASS__, 'setup_notifications' ) );

		return $this;
	}
}
