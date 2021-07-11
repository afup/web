<?php
/**
 * Customizer functionality for the Blog settings panel.
 *
 * @package Hestia
 * @since Hestia 1.1.10
 */

/**
 * A customizer control to display text in customizer.
 *
 * @since Hestia 1.1.42
 */
class Hestia_Button extends WP_Customize_Control {


	/**
	 * Control id
	 *
	 * @var string $id Control id.
	 */
	public $id = '';

	/**
	 * Button class.
	 *
	 * @var mixed|string
	 */
	public $button_class = '';

	/**
	 * Icon class.
	 *
	 * @var mixed|string
	 */
	public $icon_class = '';

	/**
	 * Button text.
	 *
	 * @var mixed|string
	 */
	public $button_text = '';

	/**
	 * Button link.
	 *
	 * @var mixed|string
	 */
	public $link = '';

	/**
	 * Control description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Hestia_Button constructor.
	 *
	 * @param WP_Customize_Manager $manager Customizer manager.
	 * @param string               $id Control id.
	 * @param array                $args Argument.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->id = $id;
	}

	/**
	 * Render content for the control.
	 *
	 * @since Hestia 1.1.42
	 */
	public function render_content() {
		if ( ! empty( $this->label ) ) {
			echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span>';
		}
		if ( ! empty( $this->description ) ) {
			echo '<span class="customize-control-description">' . esc_html( $this->description ) . '</span>';
		}
		if ( ! empty( $this->button_text ) ) {

			$params = ' href="#" ';
			if ( ! empty( $this->link ) ) {
				$params = ' href="' . esc_url( $this->link ) . '" target="_blank" ';
			}
			echo '<a ' . $params . ' type="button" class="button menu-shortcut ' . esc_attr( $this->button_class ) . '" tabindex="0">';
			if ( ! empty( $this->button_class ) ) {
				echo '<i class="fas ' . esc_attr( $this->icon_class ) . '" style="margin-right: 10px"></i>';
			}
				echo esc_html( $this->button_text );
			echo '</a>';
		}
	}
}
