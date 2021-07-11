<?php
/**
 * The plugin upgrader quiet skin.
 *
 * Used to silence installation progress for plugins installs.
 *
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 */

/**
 * Class Themeisle_OB_Quiet_Skin
 *
 * Silences plugin install and activate.
 */
class Themeisle_OB_Quiet_Skin extends WP_Upgrader_Skin {
	/**
	 * Done Header.
	 *
	 * @var bool
	 */
	public $done_header = true;

	/**
	 * Done Footer.
	 *
	 * @var bool
	 */
	public $done_footer = true;

	/**
	 * Feedback function overwrite.
	 *
	 * @param string $string feedback string.
	 * @param string $args feedback args.
	 */
	public function feedback( $string, ...$args ) {
		// Keep install quiet.
	}

	/**
	 * Quiet after.
	 */
	public function after() {
		// Quiet after
	}

	/**
	 * Quiet before.
	 */
	public function before() {
		// Quiet before.
	}


}
