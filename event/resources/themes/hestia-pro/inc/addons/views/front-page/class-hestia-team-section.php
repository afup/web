<?php
/**
 * The Team Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Team_Section
 */
class Hestia_Team_Section extends Hestia_Abstract_Main {

	/**
	 * Initialize
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 30, 'hestia_team' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_team_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_team_section', false );
	}

	/**
	 * Team section content.
	 * This function can be called from a shortcode too.
	 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
	 * it shouldn't matter if is disable on front page.
	 *
	 * @param bool $is_shortcode Flag to know if the function is called as shortcode or not.hestia_enable_seamless_add_to_cart.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	public function render_section( $is_shortcode = false ) {
		/**
		 * Gather data to display the section.
		 */
		$default_title    = false;
		$default_subtitle = false;
		$default_content  = false;
		if ( current_user_can( 'edit_theme_options' ) ) {
			$default_title    = esc_html__( 'Meet our team', 'hestia-pro' );
			$default_subtitle = esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' );
			$default_content  = Hestia_Defaults_Models::instance()->get_team_default();
		}
		$hestia_team_title    = get_theme_mod( 'hestia_team_title', $default_title );
		$hestia_team_subtitle = get_theme_mod( 'hestia_team_subtitle', $default_subtitle );
		$hestia_team_content  = get_theme_mod( 'hestia_team_content', $default_content );
		$section_is_empty     = empty( $hestia_team_title ) && empty( $hestia_team_subtitle ) && empty( $hestia_team_content );
		$hide_section         = get_theme_mod( 'hestia_team_hide', false );
		$section_style        = '';
		/**
		 * Don't show section if Disable section is checked or it doesn't have any content.
		 * Show it if it's called as a shortcode.
		 */
		if ( ( $is_shortcode === false ) && ( $section_is_empty || (bool) $hide_section === true ) ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}

		$html_allowed_strings = array(
			$hestia_team_title,
			$hestia_team_subtitle,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );
		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';
		hestia_before_team_section_trigger(); ?>
		<section class="hestia-team <?php echo esc_attr( $wrapper_class ); ?>" id="team" data-sorder="hestia_team" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_team_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_team_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_team_section_content_trigger();
				if ( $is_shortcode === false ) {
					?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center hestia-team-title-area">
							<?php
							hestia_display_customizer_shortcut( 'hestia_team_title' );
							if ( ! empty( $hestia_team_title ) || is_customize_preview() ) {
								echo '<h2 class="hestia-title">' . wp_kses_post( $hestia_team_title ) . '</h2>';
							}
							if ( ! empty( $hestia_team_subtitle ) || is_customize_preview() ) {
								echo '<h5 class="description">' . wp_kses_post( $hestia_team_subtitle ) . '</h5>';
							}
							?>
						</div>
					</div>
					<?php
				}
				$this->team_content( $hestia_team_content );
				hestia_bottom_team_section_content_trigger();
				?>
			</div>
			<?php hestia_after_team_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_team_section_trigger();
	}

	/**
	 * Get content for team section.
	 *
	 * @since 1.1.31
	 * @access public
	 * @param string $hestia_team_content Section content in json format.
	 * @return bool
	 */
	public function team_content( $hestia_team_content ) {

		if ( empty( $hestia_team_content ) ) {
			return false;
		}

		$hestia_team_content = json_decode( $hestia_team_content );
		if ( empty( $hestia_team_content ) ) {
			return false;
		}

		echo '<div class="hestia-team-content">';
		echo '<div class="row">';

		foreach ( $hestia_team_content as $team_item ) {
			$image    = ! empty( $team_item->image_url ) ? apply_filters( 'hestia_translate_single_string', $team_item->image_url, 'Team section' ) : '';
			$title    = ! empty( $team_item->title ) ? apply_filters( 'hestia_translate_single_string', $team_item->title, 'Team section' ) : '';
			$subtitle = ! empty( $team_item->subtitle ) ? apply_filters( 'hestia_translate_single_string', $team_item->subtitle, 'Team section' ) : '';
			$text     = ! empty( $team_item->text ) ? apply_filters( 'hestia_translate_single_string', $team_item->text, 'Team section' ) : '';
			$link     = ! empty( $team_item->link ) ? apply_filters( 'hestia_translate_single_string', $team_item->link, 'Team section' ) : '';

			$data = array( $image, $title, $subtitle, $text, $link );
			if ( ! array_filter( $data ) ) {
				continue;
			}

			$link_markup_open  = '';
			$link_markup_close = '';
			if ( ! empty( $link ) ) {
				$link_markup_open = '<a href="' . esc_url( $link ) . '"';
				if ( function_exists( 'hestia_is_external_url' ) ) {
					$link_markup_open .= hestia_is_external_url( $link );
				}
				$link_markup_open .= '>';

				$link_markup_close = '</a>';
			}

			maybe_trigger_fa_loading( $text );

			echo '<div class="col-xs-6 col-ms-6 col-sm-6" ' . hestia_add_animationation( 'fade-right' ) . '>';
			echo '<div class="card card-profile card-plain">';
			echo '<div class="col-md-5">';
			echo '<div class="card-image">';

			if ( ! empty( $image ) ) {

				/**
				 * Alternative text for the Team box image
				 * It first checks for the Alt Text option of the attachment
				 * If that field is empty, uses the Title of the Testimonial box as alt text
				 */
				$alt_image = $title;
				$image_id  = function_exists( 'attachment_url_to_postid' ) ? attachment_url_to_postid( preg_replace( '/-\d{1,4}x\d{1,4}/i', '', $image ) ) : '';
				if ( ! empty( $image_id ) && $image_id !== 0 ) {
					$alt_image = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				}

				echo $link_markup_open;

				echo '<img class="img" src="' . esc_url( $image ) . '" ';
				if ( ! empty( $alt_image ) ) {
					echo ' alt="' . esc_attr( $alt_image ) . '" ';
				}
				if ( ! empty( $title ) ) {
					echo ' title="' . esc_attr( $title ) . '" ';
				}
				echo '/>';

				echo $link_markup_close;
			}
			echo '</div>';
			echo '</div>';

			echo '<div class="col-md-7">';
			echo '<div class="content">';

			echo  $link_markup_open;
			if ( ! empty( $title ) ) {
				echo '<h4 class="card-title">' . esc_html( $title ) . '</h4>';
			}

			if ( ! empty( $subtitle ) ) {
				echo '<h6 class="category text-muted">' . esc_html( $subtitle ) . '</h6>';
			}

			if ( ! empty( $text ) ) {
				echo '<p class="card-description">' . wp_kses_post( html_entity_decode( $text ) ) . '</p>';
			}
			echo $link_markup_close;

			if ( ! empty( $team_item->social_repeater ) ) {
				$icons         = html_entity_decode( $team_item->social_repeater );
				$icons_decoded = json_decode( $icons, true );
				if ( ! empty( $icons_decoded ) ) {
					echo '<div class="footer">';

					foreach ( $icons_decoded as $value ) {
						$social_icon = ! empty( $value['icon'] ) ? apply_filters( 'hestia_translate_single_string', $value['icon'], 'Team section' ) : '';
						$social_link = ! empty( $value['link'] ) ? apply_filters( 'hestia_translate_single_string', $value['link'], 'Team section' ) : '';
						if ( ! empty( $social_icon ) ) {
							$link = '<a href="' . esc_url( $social_link ) . '"';
							if ( function_exists( 'hestia_is_external_url' ) ) {
								$link .= hestia_is_external_url( $social_link );
							}
							$link .= ' class="btn btn-just-icon btn-simple"><i class="' . esc_attr( hestia_display_fa_icon( $social_icon ) ) . '"></i></a>';
							echo $link;
						}
					}

					echo '</div>';
				}
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		return true;
	}
}
