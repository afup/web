<?php
/**
 * The About Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_About_Section
 */
class Hestia_About_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize About Section
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section in.
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 15, 'hestia_about' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_about_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_about_section', false );
	}


	/**
	 * About section content.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	public function render_section() {
		{

			/**
			 * Don't show section if Disable section is checked
			 */
			$section_style = '';
			$hide_section  = get_theme_mod( 'hestia_about_hide', false );
		if ( (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style .= 'display: none;';
			} else {
				return;
			}
		}

			/**
			 * Display overlay (section-image class) on about section only if section have a background
			 */
			$class_to_add = '';

			$option_name = 'hestia_feature_thumbnail';
		if ( is_customize_preview() ) {
			$option_name = 'hestia_feature_thumbnail_buffer';
		}
			$hestia_frontpage_featured = get_theme_mod( $option_name, get_template_directory_uri() . '/assets/img/contact.jpg' );
		if ( ! empty( $hestia_frontpage_featured ) ) {
			$class_to_add   = 'section-image';
			$section_style .= 'background-image: url(\'' . esc_url( $hestia_frontpage_featured ) . '\');';
		}
			$section_style = 'style="' . $section_style . '"';

			hestia_before_about_section_trigger(); ?>
			<section class="hestia-about <?php echo esc_attr( $class_to_add ); ?>" id="about" data-sorder="hestia_about" <?php echo wp_kses_post( $section_style ); ?>>
				<?php hestia_display_customizer_shortcut( 'hestia_about_hide', true ); ?>
				<div class="container">
					<div class="row hestia-about-content">
						<?php
						// Show the selected frontpage content
						if ( have_posts() ) {
							while ( have_posts() ) {
								the_post();
								get_template_part( 'template-parts/content', 'frontpage' );
							}
						} else { // I'm not sure it's possible to have no posts when this page is shown, but WTH
							get_template_part( 'template-parts/content', 'none' );
						}
						?>
					</div>
				</div>
			</section>
			<?php
			hestia_after_about_section_trigger();
			}
	}
}
