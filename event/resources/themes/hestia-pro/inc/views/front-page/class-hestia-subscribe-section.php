<?php
/**
 * The Subscribe Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Subscribe_Section
 */
class Hestia_Subscribe_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize section.
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section in.
	 */
	private function hook_section() {
		$old_priority     = apply_filters( 'hestia_section_priority', 55, 'hestia_subscribe' );
		$section_priority = apply_filters( 'hestia_section_priority', $old_priority, 'sidebar-widgets-subscribe-widgets' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_subscribe_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_subscribe_section', false );
	}

	/**
	 * Subscribe section content.
	 *
	 * @since    Hestia 1.0
	 * @modified 1.1.51
	 *
	 * @param bool $is_shortcode flag used if section is called via a shortcode.
	 */
	function render_section( $is_shortcode = false ) {

		/**
		 * Don't show section if Disable section is checked.
		 * Show it if it's called as a shortcode.
		 */
		$hide_section  = get_theme_mod( 'hestia_subscribe_hide', true );
		$section_style = '';
		if ( $is_shortcode === false && (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style .= 'display: none;';
			} else {
				return;
			}
		}

		/**
		 * Gather data to display the section.
		 */
		if ( current_user_can( 'edit_theme_options' ) ) {
			/* translators: 1 - link to customizer setting. 2 - 'customizer' */
			$hestia_subscribe_subtitle = get_theme_mod( 'hestia_subscribe_subtitle', sprintf( __( 'Change this subtitle in %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_subscribe_subtitle' ) ), __( 'customizer', 'hestia-pro' ) ) ) );
		} else {
			$hestia_subscribe_subtitle = get_theme_mod( 'hestia_subscribe_subtitle' );
		}
		$hestia_subscribe_title      = get_theme_mod( 'hestia_subscribe_title', __( 'Subscribe to our Newsletter', 'hestia-pro' ) );
		$hestia_subscribe_background = get_theme_mod( 'hestia_subscribe_background', get_template_directory_uri() . '/assets/img/about.jpg' );
		if ( ! empty( $hestia_subscribe_background ) ) {
			$section_style .= 'background-image: url(' . esc_url( $hestia_subscribe_background ) . ');';
		}
		$section_style = 'style="' . esc_attr( $section_style ) . '"';

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$class_to_add  = $is_shortcode === true ? 'is-shortcode ' : '';
		$class_to_add .= ! empty( $hestia_subscribe_background ) ? 'subscribe-line-image' : '';

		$html_allowed_strings = array(
			$hestia_subscribe_title,
			$hestia_subscribe_subtitle,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_subscribe_section_trigger(); ?>
		<section class="hestia-subscribe subscribe-line <?php echo esc_attr( $class_to_add ); ?>" id="subscribe"
				data-sorder="hestia_subscribe" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_subscribe_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_subscribe_hide', true );
			}
			?>
			<div class="container">
				<?php hestia_top_subscribe_section_content_trigger(); ?>
				<div class="row text-center">
					<div class="col-md-8 col-md-offset-2 text-center hestia-subscribe-title-area">
						<?php
						hestia_display_customizer_shortcut( 'hestia_subscribe_title' );
						if ( ! empty( $hestia_subscribe_title ) || is_customize_preview() ) :
							?>
							<h2 class="title"><?php echo wp_kses_post( $hestia_subscribe_title ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $hestia_subscribe_subtitle ) || is_customize_preview() ) : ?>
							<h5 class="subscribe-description"><?php echo hestia_sanitize_string( $hestia_subscribe_subtitle ); ?></h5>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( is_active_sidebar( 'subscribe-widgets' ) ) : ?>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="card card-raised card-form-horizontal" <?php echo hestia_add_animationation( 'fade-down' ); ?>>
								<div class="content">
									<div class="row">
										<?php
										hestia_load_fa();
										dynamic_sidebar( 'subscribe-widgets' );
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php hestia_bottom_subscribe_section_content_trigger(); ?>
			</div>
			<?php hestia_after_subscribe_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_subscribe_section_trigger();
	}

}
