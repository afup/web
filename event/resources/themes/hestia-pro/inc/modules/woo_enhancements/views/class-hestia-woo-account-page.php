<?php
/**
 * Class that manages WooCommerce Account page.
 *
 * @package Inc/Modules/Woo_Enhancements/Views
 */

/**
 * Class Hestia_Woo_Account_Page
 */
class Hestia_Woo_Account_Page {

	/**
	 * Check if this module should load.
	 *
	 * @return bool|void
	 */
	protected function should_load() {
		return is_account_page();
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @return bool
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return false;
		}
		hestia_load_fa();
		return true;
	}
}
