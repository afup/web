<?php
/**
 * Customizer functionality for the Blog settings panel.
 *
 * @package Hestia
 * @since Hestia 1.1.10
 */

/**
 * A custom text control for Subscribe info.
 *
 * @since Hestia 1.0
 */
class Hestia_Subscribe_Info extends WP_Customize_Control {

	/**
	 * Control id
	 *
	 * @var string $id Control id.
	 */
	public $id = '';

	/**
	 * Check plugin state.
	 *
	 * @var string $state Plugin state.
	 */
	private $state = '';

	/**
	 * Plugin you want to install;
	 *
	 * @var string $plugin Plugin slug.
	 */
	public $plugin = 'mailin';

	/**
	 * Plugin path.
	 *
	 * @var string $path Plugin path.
	 */
	public $path = 'mailin/sendinblue.php';

	/**
	 * Hestia_Subscribe_Info constructor.
	 *
	 * @param WP_Customize_Manager $manager Customizer manager.
	 * @param string               $id Control id.
	 * @param array                $args Argument.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->state = $this->check_plugin_state();
		$this->id    = $id;
	}

	/**
	 * Check plugin state.
	 *
	 * @return string
	 */
	private function check_plugin_state() {
		if ( is_file( ABSPATH . 'wp-content/plugins/' . $this->path ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( $this->path ) ) {
				return $this->check_activation();
			}
			return 'activate';
		}
		return 'install';
	}

	/**
	 * This method check if user registered his account for SIB.
	 *
	 * @return string
	 */
	public function check_activation() {
		if ( ! class_exists( 'SIB_Manager', false ) ) {
			return 'install';
		}
		if ( SIB_Manager::is_done_validation( false ) === true ) {
			return 'configure';
		}
		return 'create_account';
	}

	/**
	 * Enqueue function
	 */
	public function enqueue() {
		if ( $this->state === 'activate' || $this->state === 'install' ) {
			Hestia_Plugin_Install_Helper::instance()->enqueue_scripts();
		}
	}

	/**
	 * Render content for the control.
	 *
	 * @since Hestia 1.0
	 */
	public function render_content() {

		$text           = '';
		$display_button = false;
		$sib_account    = false;

		if ( $this->state === 'install' || $this->state === 'activate' ) {
			$text           = esc_html__( 'Here is where you must add the "SendinBlue Newsletter" widget.', 'hestia-pro' );
			$display_button = true;
			$sib_account    = false;
		}

		if ( $this->state === 'create_account' ) {
			$text = sprintf(
				/* translators: %s Path in plugin wrapped*/
				esc_html__( 'After installing the plugin, you need to navigate to %s and configure the plugin.', 'hestia-pro' ),
				sprintf(
					/* translators: %s Path in plugin*/
					'<a target="_blank" href="' . admin_url( 'admin.php?page=sib_page_home' ) . '"><b>%s</b></a>',
					esc_html__( 'SendinBlue > Home', 'hestia-pro' )
				)
			);
			$display_button = false;
			$sib_account    = true;
		}

		if ( $this->state === 'configure' ) {
			$text           = sprintf(
				esc_html__( 'Here is where you must add the "SendinBlue Newsletter" widget.', 'hestia-pro' ) . ' %s',
				sprintf(
					'<a target="_blank" href="https://docs.themeisle.com/article/879-how-to-integrate-sendinblue-wordpress-plugin-to-your-website">%s</a>',
					esc_html__( 'Read full documentation', 'hestia-pro' )
				)
			);
			$display_button = false;
			$sib_account    = false;
		}

		if ( ! empty( $text ) ) {
			echo wp_kses_post( $text );
		}

		if ( $display_button === true ) {
			echo $this->create_plugin_install_button(
				$this->plugin,
				array(
					'redirect' => admin_url( 'customize.php' ) . '?autofocus[control]=' . $this->id,
				)
			);
		}

		if ( $sib_account === true ) {
			echo '<br/>';
			echo '<a target="_blank" href="http://bit.ly/sibwp2" class="button" style="margin-top: 8px">' . esc_html__( 'Create SendinBlue Account', 'hestia-pro' ) . '</a>';
		}
	}


	/**
	 * Check plugin state.
	 *
	 * @param string $slug slug.
	 *
	 * @return bool
	 */
	public function create_plugin_install_button( $slug, $settings = array() ) {
		return Hestia_Plugin_Install_Helper::instance()->get_button_html( $slug, $settings );
	}
}
