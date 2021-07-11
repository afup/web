<?php
/**
 * Abstract class to be used as base for metabox controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Metabox_Control_Base
 *
 * @package Hestia
 */
abstract class Hestia_Metabox_Control_Base {

	/**
	 * Control id.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Control settings.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Control type [MUST BE DEFINED]
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Control priority.
	 *
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Hestia_Metabox_Control_Base constructor.
	 *
	 * @param string $id       control id.
	 * @param int    $priority the control priority.
	 * @param array  $settings control settings.
	 */
	public function __construct( $id, $priority, $settings ) {
		if ( empty( $this->type ) ) {
			return;
		}
		$this->id       = $id;
		$this->settings = $settings;
		$this->priority = $priority;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue scripts or styles for control.
	 */
	public function enqueue() {
		return;
	}

	/**
	 * Render function for the control.
	 *
	 * @param string $post_id the post id.
	 */
	public function render( $post_id ) {
		if ( empty( $this->type ) ) {
			return;
		}
		if ( ! $this->should_render() ) {
			return;
		}

		$this->render_label();
		$this->render_content( $post_id );
		wp_nonce_field( 'hestia_individual_layout_nonce', 'individual_layout_nonce' );
	}

	/**
	 * Render control label.
	 *
	 * @return string
	 */
	protected function render_label() {
		$label = array_key_exists( 'label', $this->settings ) ? $this->settings['label'] : '';

		if ( empty( $label ) ) {
			return;
		}

		$control_label = '';

		$control_label .= '<p class="post-attributes-label-wrapper" style="margin-top: 20px;">';
		$control_label .= '<span class="post-attributes-label">' . esc_html( $label ) . '</span>';
		$control_label .= '</p>';

		echo wp_kses_post( $control_label );
	}

	/**
	 * Render control.
	 *
	 * @param string $post_id the post id.
	 *
	 * @return void
	 */
	abstract public function render_content( $post_id );

	/**
	 * Determine if a control should be visible or not.
	 *
	 * @return bool
	 */
	private function should_render() {
		if ( ! array_key_exists( 'active_callback', $this->settings ) ) {
			return true;
		}

		if ( empty( $this->settings['active_callback'] ) ) {
			return true;
		}

		$object = $this->settings['active_callback'][0];
		$method = $this->settings['active_callback'][1];
		if ( method_exists( $object, $method ) ) {
			return $object->$method();
		}

		return true;
	}

	/**
	 * Get the value.
	 *
	 * @param string $post_id the post id.
	 *
	 * @return mixed
	 */
	protected final function get_value( $post_id ) {
		$values = get_post_meta( $post_id );

		return isset( $values[ $this->id ] ) ? esc_attr( $values[ $this->id ][0] ) : $this->settings['default'];
	}

	/**
	 * Save control data.
	 *
	 * @param string $post_id Post id.
	 *
	 * @return void
	 */
	public final function save( $post_id ) {
		if ( ! isset( $_POST['individual_layout_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['individual_layout_nonce'], 'hestia_individual_layout_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( isset( $_POST[ $this->id ] ) ) {
			$value = wp_unslash( $_POST[ $this->id ] );
			if ( $value === $this->settings['default'] ) {
				delete_post_meta( $post_id, $this->id );

				return;
			}

			update_post_meta( $post_id, $this->id, $this->sanitize_value( $value ) );

			return;
		}
		delete_post_meta( $post_id, $this->id );

	}

	/**
	 * Sanitize the value.
	 *
	 * @param int|string $value the value to sanitize.
	 *
	 * @return int|string
	 */
	protected function sanitize_value( $value ) {
		switch ( $this->type ) {
			case 'radio-image':
				$allowed_values = $this->settings['choices'];
				if ( ! array_key_exists( $value, $allowed_values ) ) {
					return esc_html( $this->settings['default'] );
				}

				return sanitize_text_field( $value );
				break;
			case 'checkbox':
				$allowed_values = array( 'on', 'off' );
				if ( ! in_array( $value, $allowed_values, true ) ) {
					return esc_html( $this->settings['default'] );
				}

				return sanitize_text_field( $value );
				break;
			default:
				break;
		}
	}
}
