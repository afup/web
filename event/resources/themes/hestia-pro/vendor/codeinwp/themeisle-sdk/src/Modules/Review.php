<?php
/**
 * The Review model class for ThemeIsle SDK
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
 * Review module for ThemeIsle SDK.
 */
class Review extends Abstract_Module {

	/**
	 * Check if we should load module for this.
	 *
	 * @param Product $product Product to check.
	 *
	 * @return bool Should load ?
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( ! $product->is_wordpress_available() ) {
			return false;
		}

		return apply_filters( $product->get_slug() . '_sdk_should_review', true );
	}


	/**
	 * Add notification to queue.
	 *
	 * @param array $all_notifications Previous notification.
	 *
	 * @return array All notifications.
	 */
	public function add_notification( $all_notifications ) {

		$developers = [
			'Bogdan',
			'Marius',
			'Hardeep',
			'Rodica',
			'Stefan',
			'Uriahs',
			'Madalin',
			'Cristi',
			'Silviu',
			'Andrei',
		];

		$link = 'https://wordpress.org/support/' . $this->product->get_type() . '/' . $this->product->get_slug() . '/reviews/#wporg-footer';

		$message = apply_filters( $this->product->get_key() . '_feedback_review_message', '<p>Hey, it\'s great to see you have <b>{product}</b> active for a few days now. How is everything going? If you can spare a few moments to rate it on WordPress.org it would help us a lot (and boost my motivation). Cheers! <br/> <br/>~ {developer}, developer of {product}</p>' );

		$button_submit = apply_filters( $this->product->get_key() . '_feedback_review_button_do', 'Ok, I will gladly help.' );
		$button_cancel = apply_filters( $this->product->get_key() . '_feedback_review_button_cancel', 'No, thanks.' );
		$message       = str_replace(
			[ '{product}', '{developer}' ],
			[
				$this->product->get_friendly_name(),
				$developers[ strlen( get_site_url() ) % 10 ],
			],
			$message
		);

		$all_notifications[] = [
			'id'      => $this->product->get_key() . '_review_flag',
			'message' => $message,
			'ctas'    => [
				'confirm' => [
					'link' => $link,
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
	 * Load module logic.
	 *
	 * @param Product $product Product to load.
	 *
	 * @return Review Module instance.
	 */
	public function load( $product ) {

		$this->product = $product;

		add_filter( 'themeisle_sdk_registered_notifications', [ $this, 'add_notification' ] );

		return $this;
	}
}
