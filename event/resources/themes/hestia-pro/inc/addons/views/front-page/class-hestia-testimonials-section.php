<?php
/**
 * The Testimonials Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Testimonials_Section
 */
class Hestia_Testimonials_Section extends Hestia_Abstract_Main {

	/**
	 * Initialize
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section.
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 45, 'hestia_testimonials' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_testimonial_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_testimonial_section', false );
	}

	/**
	 * Testimonials section content.
	 * This function can be called from a shortcode too.
	 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
	 * it shouldn't matter if is disable on front page.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	public function render_section( $is_shortcode = false ) {

		/**
		 * Gather data to display the section.
		 */
		$default_title    = '';
		$default_subtitle = '';
		$default_content  = '';
		if ( current_user_can( 'edit_theme_options' ) ) {
			$default_title    = esc_html__( 'What clients say', 'hestia-pro' );
			$default_subtitle = esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' );
			$default_content  = Hestia_Defaults_Models::instance()->get_testimonials_default();
		}
		$hestia_testimonials_title    = get_theme_mod( 'hestia_testimonials_title', $default_title );
		$hestia_testimonials_subtitle = get_theme_mod( 'hestia_testimonials_subtitle', $default_subtitle );
		if ( $is_shortcode ) {
			$hestia_testimonials_title    = '';
			$hestia_testimonials_subtitle = '';
		}
		$hestia_testimonials_content = get_theme_mod( 'hestia_testimonials_content', $default_content );
		$hide_section                = get_theme_mod( 'hestia_testimonials_hide', false );
		$section_is_empty            = empty( $hestia_testimonials_title ) && empty( $hestia_testimonials_subtitle ) && empty( $hestia_testimonials_content );
		/**
		 * Don't show section if Disable section is checked or it doesn't have any content.
		 * Show it if it's called as a shortcode.
		 */
		$section_style = '';
		if ( ( $is_shortcode === false ) && ( $section_is_empty || (bool) $hide_section === true ) ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}
		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';

		$html_allowed_strings = array(
			$hestia_testimonials_title,
			$hestia_testimonials_subtitle,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_testimonials_section_trigger(); ?>
		<section class="hestia-testimonials <?php echo esc_attr( $wrapper_class ); ?>" id="testimonials" data-sorder="hestia_testimonials" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_testimonials_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_testimonials_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_testimonials_section_content_trigger();
				if ( $is_shortcode === false ) {
					?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center hestia-testimonials-title-area">
							<?php
							hestia_display_customizer_shortcut( 'hestia_testimonials_title' );
							if ( ! empty( $hestia_testimonials_title ) || is_customize_preview() ) {
								echo '<h2 class="hestia-title">' . wp_kses_post( $hestia_testimonials_title ) . '</h2>';
							}
							if ( ! empty( $hestia_testimonials_subtitle ) || is_customize_preview() ) {
								echo '<h5 class="description">' . wp_kses_post( $hestia_testimonials_subtitle ) . '</h5>';
							}
							?>
						</div>
					</div>
					<?php
				}
				$this->testimonials_content( $hestia_testimonials_content );
				hestia_bottom_testimonials_section_content_trigger();
				?>
			</div>
			<?php hestia_after_testimonials_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_testimonials_section_trigger();
	}

	/**
	 * Display content for testimonials section.
	 *
	 * @since 1.1.31
	 * @access public
	 * @param string $hestia_testimonials_content Section content in json format.
	 */
	public function testimonials_content( $hestia_testimonials_content ) {
		?>
		<div class="hestia-testimonials-content">
			<?php
			if ( ! empty( $hestia_testimonials_content ) ) :
				$hestia_testimonials_content = json_decode( $hestia_testimonials_content );
				if ( ! empty( $hestia_testimonials_content ) ) {
					echo '<div class="row">';
					foreach ( $hestia_testimonials_content as $testimonial_item ) :
						$image    = ! empty( $testimonial_item->image_url ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->image_url, 'Testimonials section' ) : '';
						$title    = ! empty( $testimonial_item->title ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->title, 'Testimonials section' ) : '';
						$subtitle = ! empty( $testimonial_item->subtitle ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->subtitle, 'Testimonials section' ) : '';
						$text     = ! empty( $testimonial_item->text ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->text, 'Testimonials section' ) : '';
						$link     = ! empty( $testimonial_item->link ) ? apply_filters( 'hestia_translate_single_string', $testimonial_item->link, 'Testimonials section' ) : '';
						maybe_trigger_fa_loading( $text );
						?>
						<div class="col-xs-12 col-ms-6 col-sm-6 <?php echo apply_filters( 'hestia_testimonials_per_row_class', 'col-md-4' ); ?>">
							<div class="card card-testimonial card-plain" <?php echo hestia_add_animationation( 'fade-right' ); ?>>
								<?php
								if ( ! empty( $image ) ) :
									/**
									 * Alternative text for the Testimonial box image
									 * It first checks for the Alt Text option of the attachment
									 * If that field is empty, uses the Title of the Testimonial box as alt text
									 */
									$alt_image = '';
									$image_id  = function_exists( 'attachment_url_to_postid' ) ? attachment_url_to_postid( preg_replace( '/-\d{1,4}x\d{1,4}/i', '', $image ) ) : '';
									if ( ! empty( $image_id ) && $image_id !== 0 ) {
										$alt_image = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
									}
									if ( empty( $alt_image ) ) {
										if ( ! empty( $title ) ) {
											$alt_image = $title;
										}
									}
									?>
									<div class="card-avatar">
										<?php
										if ( ! empty( $link ) ) {
											$link_html = '<a href="' . esc_url( $link ) . '"';
											if ( function_exists( 'hestia_is_external_url' ) ) {
												$link_html .= hestia_is_external_url( $link );
											}
											$link_html .= '>';
											echo wp_kses_post( $link_html );
										}
										echo '<img class="img" src="' . esc_url( $image ) . '" ';
										if ( ! empty( $alt_image ) ) {
											echo ' alt="' . esc_attr( $alt_image ) . '" ';
										}
										if ( ! empty( $title ) ) {
											echo ' title="' . esc_attr( $title ) . '" ';
										}
										echo '/>';
										if ( ! empty( $link ) ) {
											echo '</a>';
										}
										?>
									</div>
								<?php endif; ?>
								<div class="content">
									<?php if ( ! empty( $title ) ) : ?>
										<h4 class="card-title"><?php echo esc_html( $title ); ?></h4>
									<?php endif; ?>
									<?php if ( ! empty( $subtitle ) ) : ?>
										<h6 class="category text-muted"><?php echo esc_html( $subtitle ); ?></h6>
									<?php endif; ?>
									<?php if ( ! empty( $text ) ) : ?>
										<p class="card-description"><?php echo wp_kses_post( html_entity_decode( $text ) ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
					endforeach;
					echo '</div>';
				}// End if().
			endif;
			?>
		</div>
		<?php
	}
}
