<?php
/**
 * The manager for the first front page section.
 *
 * @package Hestia
 */

/**
 * Class Hestia_First_Front_Page_Section
 */
class Hestia_First_Front_Page_Section extends Hestia_Abstract_Main {

	/**
	 * Hook the section into the header.
	 */
	public function init() {
		add_action( 'hestia_header', array( $this, 'render_section' ) );
	}

	/**
	 * Big title section content.
	 *
	 * @since Hestia 1.0
	 */
	public function render_section() {
		$hide_section = get_theme_mod( 'hestia_big_title_hide', false );
		if ( (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}

		hestia_before_big_title_section_trigger();
		?>
		<div id="carousel-hestia-generic" class="carousel slide" data-ride="carousel" 
		<?php
		if ( ! empty( $section_style ) ) {
			echo $section_style; }
		?>
		>
			<div class="carousel slide" data-ride="carousel">
				<div class="carousel-inner">
					<?php
					do_action( 'hestia_first_front_page_section_content' );
					?>
				</div>
			</div>
		</div>
		<?php
		hestia_after_big_title_section_trigger();
	}

	/**
	 * Display parallax.
	 *
	 * @since 1.1.72
	 */
	public function maybe_render_parallax() {
		if ( ! $this->should_display_parallax() ) {
			return;
		}

		$parallax_layer1 = get_theme_mod( 'hestia_parallax_layer1', apply_filters( 'hestia_parallax_layer1_default', false ) );
		$parallax_layer2 = get_theme_mod( 'hestia_parallax_layer2', apply_filters( 'hestia_parallax_layer2_default', false ) );

		echo '<div id="parallax_move">';
		echo '<div class="layer layer1" data-depth="0.10" style="background-image: url(' . esc_url( $parallax_layer1 ) . ');"></div>';
		echo '<div class="layer layer2" data-depth="0.20" style="background-image: url(' . esc_url( $parallax_layer2 ) . ');"></div>';
		echo '</div>';
	}

	/**
	 * Utility to check if we should display parallax.
	 */
	public static function should_display_parallax() {
		$hestia_big_title_type = get_theme_mod( 'hestia_slider_type', apply_filters( 'hestia_slider_type_default', 'image' ) );
		if ( empty( $hestia_big_title_type ) || $hestia_big_title_type !== 'parallax' ) {
			return false;
		}

		$parallax_layer1 = get_theme_mod( 'hestia_parallax_layer1', apply_filters( 'hestia_parallax_layer1_default', false ) );
		if ( empty( $parallax_layer1 ) ) {
			return false;
		}

		$parallax_layer2 = get_theme_mod( 'hestia_parallax_layer2', apply_filters( 'hestia_parallax_layer2_default', false ) );
		if ( empty( $parallax_layer2 ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determine the classes that should be on widgets and slider content.
	 *
	 * @param string $slider_alignment Slider alignment.
	 *
	 * @return array
	 */
	public function get_big_title_elements_class( $slider_alignment ) {
		$result_array = array(
			'slide'  => ' big-title-slider-content text-' . $slider_alignment,
			'widget' => ' col-md-5 ',
		);

		switch ( $slider_alignment ) {
			case 'left':
				$result_array['slide']  .= ' col-md-7 ';
				$result_array['widget'] .= ' hestia-slider-alignment-left ';
				break;
			case 'center':
				$result_array['slide'] .= ' col-sm-8 col-sm-offset-2 ';
				break;
			case 'right':
				$result_array['slide']  .= ' col-md-7 margin-left-auto ';
				$result_array['widget'] .= ' hestia-slider-alignment-right ';
				break;
		}
		return $result_array;
	}

	/**
	 * Render widgets area on slider or big title.
	 *
	 * @param string $alignment Big title alignment.
	 * @param string $position Sidebar position.
	 * @param string $slide Slide index.
	 */
	public function maybe_render_widgets_area( $alignment, $position, $slide ) {
		if ( $alignment !== $position ) {
			return;
		}

		if ( $slide !== 1 ) {
			return;
		}

		$slider_elements_classes = $this->get_big_title_elements_class( $alignment );
		echo '<div class="big-title-sidebar-wrapper ' . esc_attr( $slider_elements_classes['widget'] ) . '">';
		dynamic_sidebar( 'sidebar-big-title' );
		echo '</div>';
	}
}
