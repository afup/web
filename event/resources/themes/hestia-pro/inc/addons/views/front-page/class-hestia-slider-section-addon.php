<?php
/**
 * The Slider section handler.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Slider_Section_Addon
 */
class Hestia_Slider_Section_Addon extends Hestia_First_Front_Page_Section {

	/**
	 * Initialize the big title content.
	 */
	public function init() {
		parent::init();
		add_action( 'hestia_first_front_page_section_content', array( $this, 'render_slider_content' ) );
		add_filter( 'hestia_custom_header_settings', array( $this, 'add_video_support' ) );
		add_filter( 'header_video_settings', array( $this, 'video_settings' ) );
	}

	/**
	 * The main render function for this section.
	 */
	public function render_slider_content() {
		$this->maybe_render_parallax();
		$this->maybe_render_video();
		$this->render_content();
	}

	/**
	 * Display video bg.
	 */
	private function maybe_render_video() {
		if ( ! $this->should_display_video() ) {
			return;
		}

		if ( ! has_header_video() ) {
			return;
		}
		the_custom_header_markup();
	}

	/**
	 * Get the slide classes.
	 *
	 * @param int $counter Slide index.
	 *
	 * @return string
	 */
	private function get_slide_class( $counter ) {
		$class = 'item item-' . $counter;

		if ( $counter === 1 ) {
			$class .= ' active';
		}

		return $class;
	}

	/**
	 * Render slider background images if needed.
	 *
	 * @param string $image Background url.
	 */
	private function maybe_render_slide_background_image( $image ) {
		if (
			$this->should_display_parallax() ||
			$this->should_display_video() ||
			empty( $image )
		) {
			echo '<div class="header-filter"></div>';

			return;
		}

		echo '<div class="header-filter" style="background-image: url(' . esc_url( $image ) . ')"></div>';

		return;
	}

	/**
	 * Render buttons for slide.
	 *
	 * @param array $settings Settings array.
	 */
	private function render_slide_buttons( $settings ) {
		$link     = $settings['link'];
		$button   = $settings['button'];
		$color    = $settings['color'];
		$link2    = $settings['link2'];
		$button2  = $settings['button2'];
		$color2   = $settings['color2'];
		$slide_nb = $settings['counter'];

		if (
			empty( $link ) &&
			empty( $button ) &&
			empty( $link2 ) &&
			empty( $button2 ) ) {

			return;
		}

		$allowed_tags = array(
			'i'      => array(
				'class' => array(),
			),
			'span'   => array(
				'class' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		); ?>
		<div class="buttons">
			<?php

			if ( ! empty( $link ) && ! empty( $button ) ) {
				$button = html_entity_decode( $button );
				echo '<a href="' . esc_url( $link ) . '" title="' . esc_html( $button ) . '" class="btn btn-primary btn-left ' . ( ! empty( $color ) ? 'has-color' : '' ) . '" ' . hestia_is_external_url( $link ) . '>' . wp_kses( $button, $allowed_tags ) . '</a>';
			}
			if ( ! empty( $link2 ) && ! empty( $button2 ) ) {
				$button2 = html_entity_decode( $button2 );
				echo '<a href="' . esc_url( $link2 ) . '" title="' . esc_html( $button2 ) . '" class="btn btn-primary btn-right ' . ( ! empty( $color2 ) ? 'has-color' : '' ) . '" ' . hestia_is_external_url( $link2 ) . '>' . wp_kses( $button2, $allowed_tags ) . '</a>';
			}
			hestia_big_title_section_buttons_trigger( $slide_nb );
			?>
		</div>
		<?php
	}

	/**
	 * Render slider content.
	 */
	public function render_content() {

		$slider_default = Hestia_Defaults_Models::instance()->get_slider_default();

		$slider_content = get_theme_mod( 'hestia_slider_content', json_encode( $slider_default ) );
		if ( empty( $slider_content ) ) {
			return;
		}

		$slider_content = json_decode( $slider_content );
		if ( empty( $slider_content ) ) {
			return;
		}

		$alignment       = get_theme_mod( 'hestia_slider_alignment', 'center' );
		$alignment_class = $this->get_big_title_elements_class( $alignment );
		$alignment_class = $alignment_class['slide'];
		$counter         = 1;

		foreach ( $slider_content as $slider_item ) {
			$title                = ! empty( $slider_item->title ) ? apply_filters( 'hestia_translate_single_string', $slider_item->title, 'Slider section' ) : '';
			$subtitle             = ! empty( $slider_item->subtitle ) ? apply_filters( 'hestia_translate_single_string', $slider_item->subtitle, 'Slider section' ) : '';
			$button               = ! empty( $slider_item->text ) ? apply_filters( 'hestia_translate_single_string', $slider_item->text, 'Slider section' ) : '';
			$link                 = ! empty( $slider_item->link ) ? apply_filters( 'hestia_translate_single_string', $slider_item->link, 'Slider section' ) : '';
			$button2              = ! empty( $slider_item->text2 ) ? apply_filters( 'hestia_translate_single_string', $slider_item->text2, 'Slider section' ) : '';
			$link2                = ! empty( $slider_item->link2 ) ? apply_filters( 'hestia_translate_single_string', $slider_item->link2, 'Slider section' ) : '';
			$image                = ! empty( $slider_item->image_url ) && $this->should_display_video() === false && $this->should_display_parallax() === false ? apply_filters( 'hestia_translate_single_string', $slider_item->image_url, 'Slider section' ) : '';
			$color                = ! empty( $slider_item->color ) ? apply_filters( 'hestia_translate_single_string', $slider_item->color, 'Slider section' ) : '';
			$color2               = ! empty( $slider_item->color2 ) ? apply_filters( 'hestia_translate_single_string', $slider_item->color2, 'Slider section' ) : '';
			$item_class           = $this->get_slide_class( $counter );
			$html_allowed_strings = array(
				$title,
				$subtitle,
				$button,
				$button2,
			);
			maybe_trigger_fa_loading( $html_allowed_strings );
			?>

			<div class="<?php echo esc_attr( $item_class ); ?>">
				<div class="page-header">
					<?php hestia_before_big_title_section_content_trigger(); ?>
					<div class="container">
						<?php hestia_top_big_title_section_content_trigger(); ?>
						<div class="row">
							<?php $this->maybe_render_widgets_area( $alignment, 'right', $counter ); ?>
							<div class="<?php echo esc_attr( $alignment_class ); ?>">

								<?php
								if ( ! empty( $title ) ) {
									$title = html_entity_decode( $title );
									?>
									<h1 class="hestia-title"><?php echo wp_kses_post( $title ); ?></h1>
									<?php
								}

								if ( ! empty( $subtitle ) ) {
									$subtitle = html_entity_decode( $subtitle );
									?>
									<span class="sub-title"><?php echo wp_kses_post( $subtitle ); ?></span>
									<?php
								}

								$settings = array(
									'link'    => $link,
									'button'  => $button,
									'color'   => $color,
									'link2'   => $link2,
									'button2' => $button2,
									'color2'  => $color2,
									'counter' => $counter,
								);
								$this->render_slide_buttons( $settings );
								echo $this->get_button_style( $color, $color2, $counter );
								?>

							</div>
							<?php $this->maybe_render_widgets_area( $alignment, 'left', $counter ); ?>
						</div>

						<?php hestia_bottom_big_title_section_content_trigger(); ?>
					</div>
					<?php
					$this->maybe_render_slide_background_image( $image );
					hestia_after_big_title_section_content_trigger();
					?>
				</div>
			</div>

			<?php
			$counter ++;
		}

		if ( $counter >= 3 ) {
			$left_class  = ! is_rtl() ? 'left' : 'right';
			$right_class = ! is_rtl() ? 'right' : 'left';
			echo '<a class="' . esc_attr( $left_class ) . ' carousel-control" href="#carousel-hestia-generic" data-slide="prev">';
				echo '<i class="fas fa-angle-left"></i>';
			echo '</a>';
			echo '<a class="' . esc_attr( $right_class ) . ' carousel-control" href="#carousel-hestia-generic" data-slide="next">';
				echo '<i class="fas fa-angle-right"></i>';
			echo '</a>';
		}
	}

	/**
	 * Function to style slider button colors.
	 *
	 * @param string $color1      string first color.
	 * @param string $color2      string second color.
	 * @param string $item_number slide number.
	 *
	 * @return string
	 */
	private function get_button_style(
		$color1, $color2, $item_number
	) {

		if ( empty( $color1 ) && empty( $color2 ) ) {
			return '';
		}

		$hover  = get_theme_mod( 'hestia_buttons_hover_effect', 'shadow' );
		$colors = array();

		if ( ! empty( $color1 ) ) {
			$ajusted_color1      = hestia_adjust_brightness( $color1, - 20 );
			$colors['.btn-left'] = array( $color1, $ajusted_color1 );
		}
		if ( ! empty( $color2 ) ) {
			$ajusted_color2       = hestia_adjust_brightness( $color2, - 20 );
			$colors['.btn-right'] = array( $color2, $ajusted_color2 );
		}

		if ( empty( $item_number ) ) {
			return '';
		}

		$style = '<style>';
		foreach ( $colors as $class => $color ) {
			if ( ! empty( $color ) && ! empty( $class ) ) {

				if ( ! empty( $color[0] ) ) {
					$style .= '
					.item.item-' . $item_number . ' .buttons ' . esc_attr( $class ) . '{
						background-color:' . $color[0] . ';
						 -webkit-box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $color[0], '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $color[0], '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $color[0], '0.12' ) . ';
                        box-shadow: 0 2px 2px 0 ' . hestia_hex_rgba( $color[0], '0.14' ) . ',0 3px 1px -2px ' . hestia_hex_rgba( $color[0], '0.2' ) . ',0 1px 5px 0 ' . hestia_hex_rgba( $color[0], '0.12' ) . ';
					}';
					$style .= '
                    .item.item-' . $item_number . ' .buttons ' . esc_attr( $class ) . ':hover {
                        -webkit-box-shadow: 0 14px 26px -12px' . hestia_hex_rgba( $color[0], '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $color[0], '0.2' ) . ';
                        box-shadow: 0 14px 26px -12px ' . hestia_hex_rgba( $color[0], '0.42' ) . ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' . hestia_hex_rgba( $color[0], '0.2' ) . ';
                    }';
				}

				if ( $hover === 'color' && ! empty( $color[1] ) ) {
					$style .= '
					.item.item-' . $item_number . ' .buttons ' . esc_attr( $class ) . ':hover{
						background-color:' . $color[1] . ';
					    box-shadow: none!important;
					}';
				}
			}
		}
		$style .= '</style>';

		return $style;
	}

	/**
	 * Utility to check if we should display video background.
	 */
	private function should_display_video() {
		$slider_type = get_theme_mod( 'hestia_slider_type', apply_filters( 'hestia_slider_type_default', 'image' ) );

		if ( $slider_type !== 'video' ) {
			return false;
		}

		$external_video = get_theme_mod( 'external_header_video' );
		$video_file     = get_theme_mod( 'header_video' );

		if ( empty( $external_video ) && empty( $video_file ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add support for video header.
	 *
	 * @since 1.1.52
	 *
	 * @param array $settings Custom header settings.
	 *
	 * @return array
	 */
	public function add_video_support( $settings ) {
		$settings['video'] = true;

		return $settings;
	}

	/**
	 * Function to change video settings.
	 *
	 * @param array $settings Video settings.
	 *
	 * @since 1.1.52
	 * @return array
	 */
	public function video_settings( $settings ) {
		$settings['width']  = 5120;
		$settings['height'] = 2880;

		return $settings;
	}
}
