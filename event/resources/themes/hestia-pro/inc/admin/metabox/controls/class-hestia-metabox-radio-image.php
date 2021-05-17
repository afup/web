<?php
/**
 * Metabox radio button control.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Metabox_Radio_Image
 *
 * @package Hestia
 */
class Hestia_Metabox_Radio_Image extends Hestia_Metabox_Control_Base {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'radio-image';

	/**
	 * Render control.
	 *
	 * @param string $post_id the post id.
	 *
	 * @return void
	 */
	public function render_content( $post_id ) {
		$control_content  = '';
		$choices          = $this->settings['choices'];
		$selected         = $this->get_value( $post_id );
		$control_content .= '<div id="control-' . esc_attr( $this->id ) . '">';

		$control_content .= '<div class="buttonset">';
		foreach ( $choices as $choice => $choice_setting ) {
			if ( empty( $choice_setting['url'] ) ) {
				continue;
			}

			$control_content .= '<input type="radio" name="' . esc_attr( $this->id ) . '" value="' . esc_attr( $choice ) . '" id="' . esc_attr( $this->id ) . '-' . esc_attr( $choice ) . '" ' . checked( $selected, $choice, false ) . '/>';
			$control_content .= '<label for="' . esc_attr( $this->id ) . '-' . esc_attr( $choice ) . '">';

			if ( ! empty( $choice_setting['label'] ) ) {
				$control_content .= '<span class="screen-reader-text">';
				$control_content .= esc_html( $choice_setting['label'] );
				$control_content .= '</span>';
			}
			$control_content .= '<img src="' . $choice_setting['url'] . '" alt="' . ( array_key_exists( 'label', $choice_setting ) ? esc_attr( $choice_setting['label'] ) : esc_attr( $choice ) ) . '" />';
			$control_content .= '</label>';
		}
		$control_content .= $this->render_default_button( $post_id );
		$control_content .= '</div>';
		$control_content .= '</div>';

		echo $control_content;
	}

	/**
	 * Render default button.
	 *
	 * @return string
	 */
	private function render_default_button( $post_id ) {
		$default_button = '';

		$class_to_add = 'button button-secondary reset-data';
		$value        = $this->get_value( $post_id );
		if ( empty( $value ) || $value === 'default' ) {
			$class_to_add .= ' disabled';
		}

		$default_button .= '<div class="reset-data-wrapper">';
		$default_button .= '<div class="' . esc_attr( $class_to_add ) . '" data-default="' . ( array_key_exists( 'default', $this->settings ) ? esc_attr( $this->settings['default'] ) : '' ) . '" data-id="' . esc_attr( $this->id ) . '" data-pid="' . esc_attr( $post_id ) . '">';
		$default_button .= '<span class="dashicons dashicons-image-rotate"></span>';
		$default_button .= '</div>';
		$default_button .= '</div>';
		return $default_button;
	}


	/**
	 * Enqueue control styles and scripts.
	 */
	public function enqueue() {
		wp_enqueue_script(
			'hestia-meta-radio-buttons-script',
			get_template_directory_uri() . '/inc/admin/metabox/controls/assets/radio-image.js',
			array( 'jquery', 'jquery-ui-button' ),
			HESTIA_VERSION,
			true
		);
		wp_enqueue_style(
			'hestia-meta-radio-buttons-style',
			get_template_directory_uri() . '/inc/admin/metabox/controls/assets/radio-image.css',
			array(),
			HESTIA_VERSION
		);
	}
}
