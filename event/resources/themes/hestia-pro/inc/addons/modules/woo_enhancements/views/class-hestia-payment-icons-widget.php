<?php
/**
 * Payment Icons Widget
 *
 * @package Hestia
 */

/**
 * Class Hestia_Payment_Icons_Widget
 */
class Hestia_Payment_Icons_Widget extends WP_Widget {

	/**
	 * Detect the loading of scripts
	 *
	 * @var bool
	 */
	protected static $did_script = false;

	/**
	 * Hestia_Payment_Icons_Widget constructor.
	 */
	function __construct() {
		parent::__construct(
			// widget ID
			'hestia_payment_icons_widget',
			// widget name
			__( 'Payment Icons Widget', 'hestia-pro' ),
			// widget description
			array( 'description' => __( 'Payment Icons Widget', 'hestia-pro' ) )
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Enqueue widget style.
	 */
	public function scripts() {
		if ( ! self::$did_script && is_active_widget( false, false, $this->id_base, true ) ) {
			$css = Hestia_Payment_Icons::get_inline_style();
			wp_add_inline_style( 'hestia_style', $css );
			self::$did_script = true;
		}
		return true;
	}

	/**
	 * Render Payment Icons widget.
	 *
	 * @param array $args Widget Arguments.
	 * @param array $instance Current Instance.
	 */
	public function widget( $args, $instance ) {
		echo '<div class="hestia-payment-cart-total">';
		echo Hestia_Payment_Icons::render_payment_icons();
		echo '</div>';
	}

	/**
	 * Widget form.
	 *
	 * @param array $instance Widget instance.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$query['autofocus[control]'] = 'hestia_payment_icons';
		$control_link                = add_query_arg( $query, admin_url( 'customize.php' ) );
		?>
		<p><span class="quick-links">
		<?php
			printf(
				/* translators: %s is link to section */
				esc_html__( 'Click %s to edit payment icons', 'hestia-pro' ),
				sprintf(
					/* translators: %s is link label */
					'<a href="%s" data-control-focus="hestia_payment_icons" >%s</a>',
					esc_url( $control_link ),
					esc_html__( 'here', 'hestia-pro' )
				)
			)
		?>
			</span></p>
		<?php
	}
}
