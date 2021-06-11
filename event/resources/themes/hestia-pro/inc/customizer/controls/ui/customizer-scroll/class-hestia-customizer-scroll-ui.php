<?php
/**
 * This class allows developers to implement scrolling to sections.
 *
 * @package    Hestia
 * @since      1.1.49
 * @author     Andrei Baicus <andrei@themeisle.com>
 * @copyright  Copyright (c) 2017, Themeisle
 * @link       http://themeisle.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Scroll to section.
 *
 * @since  1.1.45
 * @access public
 */
class Hestia_Customizer_Scroll_Ui extends Hestia_Abstract_Main {

	/**
	 * Hestia_Customize_Control_Scroll constructor.
	 */
	public function init() {
		add_action( 'customize_preview_init', array( $this, 'helper_script_enqueue' ) );
	}

	/**
	 * The priority of the control.
	 *
	 * @since 1.1.45
	 * @var   string
	 */
	public $priority = 0;

	/**
	 * Enqueue the partials handler script that works synchronously with the hestia-scroller-script
	 */
	public function helper_script_enqueue() {
		wp_enqueue_script( 'hestia-scroller-addon-script', get_template_directory_uri() . '/inc/customizer/controls/ui/customizer-scroll/helper-script.js', array( 'jquery' ), HESTIA_VERSION, true );
	}
}
