<?php
/**
 * Metabox radio button control.
 *
 * @package Hestia
 */

/**
 * Class Checkbox
 *
 * @package Hestia
 */
class Hestia_Metabox_Checkbox extends Hestia_Metabox_Control_Base {
	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'checkbox';

	/**
	 * Render control.
	 *
	 * @param string $post_id the post id.
	 *
	 * @return void
	 */
	public function render_content( $post_id ) {
		$value  = $this->get_value( $post_id );
		$markup = '';

		$markup .= '<p>';
		$markup .= '<div class="checkbox-toggle-wrap">';
		$markup .= '<label for="' . esc_attr( $this->id ) . '">';
		$markup .= '<input type="checkbox" id="' . esc_attr( $this->id ) . '" name="' . esc_attr( $this->id ) . '" ' . '';
		if ( $value === 'on' ) {
			$markup .= ' checked="checked" ';
		}
		$markup .= '/>';
		$markup .= esc_html( $this->settings['input_label'] ) . '</label>';
		$markup .= '</div>';
		$markup .= '</p>';

		echo $markup;
	}
}
