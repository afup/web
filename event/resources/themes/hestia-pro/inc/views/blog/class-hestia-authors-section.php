<?php
/**
 * Authors section on blog
 *
 * @package hestia
 */

/**
 * Class Hestia_Authors_Section
 */
class Hestia_Authors_Section extends Hestia_Abstract_Main {

	/**
	 * Members to display.
	 *
	 * @var array
	 */
	private $members_to_display = array();

	/**
	 * Initialization function for authors section on blog.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'hestia_after_archive_content', array( $this, 'render_authors_section' ), 10 );
	}


	/**
	 * Render function.
	 *
	 * @access public
	 * @return void
	 */
	public function render_authors_section() {
		$this->initialize_members_list();

		if ( empty( $this->members_to_display ) ) {
			return;
		}

		$hestia_authors_on_blog_background = get_theme_mod( 'hestia_authors_on_blog_background' );
		$background_inline                 = ! empty( $hestia_authors_on_blog_background ) ? 'style="background-image: url(' . esc_url( $hestia_authors_on_blog_background ) . ');"' : '';
		$section_class                     = ! empty( $hestia_authors_on_blog_background ) ? 'authors-on-blog section-image' : 'authors-on-blog';

		echo '<section id="authors-on-blog" class="' . esc_attr( $section_class ) . '" ' . $background_inline . '>';
		echo '<div class="container"><div class="row">';
		foreach ( $this->members_to_display as $team_item ) {
			$image    = ! empty( $team_item['image_url'] ) ? apply_filters( 'hestia_translate_single_string', $team_item['image_url'], 'Team section', 'Image' ) : '';
			$title    = ! empty( $team_item['title'] ) ? apply_filters( 'hestia_translate_single_string', $team_item['title'], 'Team section', 'Title' ) : '';
			$subtitle = ! empty( $team_item['subtitle'] ) ? apply_filters( 'hestia_translate_single_string', $team_item['subtitle'], 'Team section', 'Subtitle' ) : '';
			$text     = ! empty( $team_item['text'] ) ? apply_filters( 'hestia_translate_single_string', $team_item['text'], 'Team section', 'Text' ) : '';
			$link     = ! empty( $team_item['link'] ) ? apply_filters( 'hestia_translate_single_string', $team_item['link'], 'Team section', 'Link' ) : '';
			$icons    = ! empty( $team_item['social_repeater'] ) ? $team_item['social_repeater'] : '';

			echo '<div class="col-xs-12 col-ms-6 col-sm-6"><div class="card card-profile card-plain">';
				echo '<div class="col-md-5"><div class="card-image">';
			if ( ! empty( $image ) ) {
				if ( ! empty( $link ) ) {
					echo '<a href="' . esc_url( $link ) . '">';
				}
				echo '<img class="img" src="' . esc_url( $image ) . '">';
				if ( ! empty( $link ) ) {
					echo '</a>';
				}
			}
				echo '</div></div>';

				echo '<div class="col-md-7"><div class="content">';
			if ( ! empty( $title ) ) {
				echo '<h4 class="card-title">' . wp_kses_post( html_entity_decode( $title ) ) . '</h4>';
			}
			if ( ! empty( $subtitle ) ) {
				echo '<h6 class="category text-muted">' . wp_kses_post( html_entity_decode( $subtitle ) ) . '</h6>';
			}
			if ( ! empty( $text ) ) {
				echo '<p class="card-description">' . wp_kses_post( html_entity_decode( $text ) ) . '</p>';
			}
			if ( ! empty( $icons ) ) {
				$icons         = html_entity_decode( $icons );
				$icons_decoded = json_decode( $icons, true );
				if ( ! empty( $icons_decoded ) ) {
					echo '<div class="footer">';
					foreach ( $icons_decoded as $value ) {
						$icon = ! empty( $value['icon'] ) ? apply_filters( 'hestia_translate_single_string', $value['icon'], 'Team section' ) : '';
						$link = ! empty( $value['link'] ) ? apply_filters( 'hestia_translate_single_string', $value['link'], 'Team section' ) : '';
						if ( ! empty( $icon ) ) {
							$icon_class = ! empty( $hestia_authors_on_blog_background ) ? 'btn btn-just-icon btn-simple btn-white' : 'btn btn-just-icon btn-simple';
							echo '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $icon_class ) . '" >';
							echo '<i class="' . esc_attr( hestia_display_fa_icon( $icon ) ) . '"></i>';
							echo '</a>';
						}
					}
					echo '</div>';
				}
			}
				echo '</div></div>';
			echo '</div></div>';
		}
		echo '</div></div>';
		echo '</section>';
	}

	/**
	 * Select from team members just those members that were selected in hestia_authors_on_blog control
	 *
	 * @access private
	 * @return void
	 */
	private function initialize_members_list() {
		$hestia_authors_on_blog = get_theme_mod( 'hestia_authors_on_blog' );
		if ( empty( $hestia_authors_on_blog ) || ( sizeof( $hestia_authors_on_blog ) === 1 && empty( $hestia_authors_on_blog[0] ) ) ) {
			return;
		}

		$default_content     = Hestia_Defaults_Models::instance()->get_team_default();
		$hestia_team_content = get_theme_mod( 'hestia_team_content', $default_content );
		if ( empty( $hestia_team_content ) ) {
			return;
		}

		$hestia_team_content = json_decode( $hestia_team_content, true );
		if ( ! empty( $hestia_team_content ) ) {
			$this->members_to_display = array_filter( $hestia_team_content, array( $this, 'selected_authors' ) );
		}
	}

	/**
	 * Filter function to check if the id is in team members.
	 *
	 * @access private
	 * @return bool
	 */
	private function selected_authors( $arr ) {
		$hestia_authors_on_blog = (array) get_theme_mod( 'hestia_authors_on_blog' );
		if ( empty( $hestia_authors_on_blog ) ) {
			return false;
		}
		return in_array( $arr['id'], $hestia_authors_on_blog, true );
	}
}
