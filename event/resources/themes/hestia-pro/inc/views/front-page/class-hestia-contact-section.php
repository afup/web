<?php
/**
 * The Contact Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Contact_Section
 */
class Hestia_Contact_Section extends Hestia_Abstract_Main {
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
		$section_priority = apply_filters( 'hestia_section_priority', 65, 'hestia_contact' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_contact_section', array( $this, 'render_section' ) );
		add_action( 'after_setup_theme', array( $this, 'contact_form_legacy' ) );
	}

	/**
	 * This function executes once because we removed the default for contact form shortcode and
	 * it breaks form for users that didn't change that default.
	 *
	 * TODO: Remove this in the next version after the release on wp.org
	 */
	public function contact_form_legacy() {
		if ( ! defined( 'PIRATE_FORMS_VERSION' ) ) {
			return false;
		}
		$execute = get_option( 'hestia_contact_form_legacy' );
		if ( $execute !== false ) {
			return false;
		}
		$contact_shorcode_with_default    = get_theme_mod( 'hestia_contact_form_shortcode', '[pirate_forms]' );
		$contact_shorcode_without_default = get_theme_mod( 'hestia_contact_form_shortcode' );
		if ( $contact_shorcode_with_default === '[pirate_forms]' && empty( $contact_shorcode_without_default ) ) {
			set_theme_mod( 'hestia_contact_form_shortcode', '[pirate_forms]' );
		}

		update_option( 'hestia_contact_form_legacy', true );
		return true;
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_contact_section', false );
	}

	/**
	 * Contact section content.
	 * This function can be called from a shortcode too.
	 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
	 * it shouldn't matter if is disable on front page.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	function render_section( $is_shortcode = false ) {

		/**
		 * Don't show section if Disable section is checked.
		 * Show it if it's called as a shortcode.
		 */
		$hide_section  = get_theme_mod( 'hestia_contact_hide', false );
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
			$hestia_contact_subtitle = get_theme_mod( 'hestia_contact_subtitle', sprintf( __( 'Change this subtitle in %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_contact_subtitle' ) ), __( 'customizer', 'hestia-pro' ) ) ) );
		} else {
			$hestia_contact_subtitle = get_theme_mod( 'hestia_contact_subtitle' );
		}
		$hestia_contact_title      = get_theme_mod( 'hestia_contact_title', esc_html__( 'Get in Touch', 'hestia-pro' ) );
		$hestia_contact_area_title = get_theme_mod( 'hestia_contact_area_title', esc_html__( 'Contact Us', 'hestia-pro' ) );

		$hestia_contact_background = get_theme_mod( 'hestia_contact_background', apply_filters( 'hestia_contact_background_default', get_template_directory_uri() . '/assets/img/contact.jpg' ) );
		if ( ! empty( $hestia_contact_background ) ) {
			$section_style .= 'background-image: url(' . esc_url( $hestia_contact_background ) . ');';
		}
		$section_style = 'style="' . $section_style . '"';

		$contact_content_default = '';
		if ( current_user_can( 'edit_theme_options' ) ) {
			$contact_content_default = $this->content_default();
		}

		$hestia_contact_content = get_theme_mod( 'hestia_contact_content_new', $contact_content_default );

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$class_to_add  = $is_shortcode === true ? 'is-shortcode ' : '';
		$class_to_add .= ! empty( $hestia_contact_background ) ? 'section-image' : '';

		$html_allowed_strings = array(
			$hestia_contact_title,
			$hestia_contact_subtitle,
			$hestia_contact_content,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_contact_section_trigger(); ?>
		<section class="hestia-contact contactus <?php echo esc_attr( $class_to_add ); ?>" id="contact"
				data-sorder="hestia_contact" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_contact_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_contact_hide', true );
			}
			?>
			<div class="container">
				<?php hestia_top_contact_section_content_trigger(); ?>
				<div class="row">
					<div class="col-md-5 hestia-contact-title-area" <?php echo hestia_add_animationation( 'fade-right' ); ?>>
						<?php
						hestia_display_customizer_shortcut( 'hestia_contact_title' );
						if ( ! empty( $hestia_contact_title ) || is_customize_preview() ) :
							?>
							<h2 class="hestia-title"><?php echo wp_kses_post( $hestia_contact_title ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $hestia_contact_subtitle ) || is_customize_preview() ) : ?>
							<h5 class="description"><?php echo hestia_sanitize_string( $hestia_contact_subtitle ); ?></h5>
						<?php endif; ?>
						<?php

						if ( ! empty( $hestia_contact_content ) ) {
							echo '<div class="hestia-description">';
							echo Hestia_Contact_Controls::sanitize_contact_field( force_balance_tags( $hestia_contact_content ) );
							echo '</div>';
						}

						?>
					</div>
					<?php
					$hestia_contact_form_shortcode = get_theme_mod( 'hestia_contact_form_shortcode' );
					if ( ! empty( $hestia_contact_form_shortcode ) ) {
						?>
						<div class="col-md-5 col-md-offset-2 hestia-contact-form-col" <?php echo hestia_add_animationation( 'fade-left' ); ?>>
							<div class="card card-contact">
								<?php if ( ! empty( $hestia_contact_area_title ) || is_customize_preview() ) : ?>
									<div class="header header-raised header-primary text-center">
										<h4 class="card-title"><?php echo esc_html( $hestia_contact_area_title ); ?></h4>
									</div>
								<?php endif; ?>
								<div class="content">
									<?php
									$this->render_contact_form();
									?>
								</div>
							</div>
						</div>
						<?php

					} elseif ( is_customize_preview() ) {
						echo hestia_contact_form_placeholder();
					}
					?>
				</div>
				<?php hestia_bottom_contact_section_content_trigger(); ?>
			</div>
			<?php hestia_after_contact_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_contact_section_trigger();
	}

	/**
	 * Get the contact default content
	 *
	 * @return string
	 */
	public function content_default() {
		$html = '<div class="hestia-info info info-horizontal">
			<div class="icon icon-primary">
				<i class="fas fa-map-marker-alt"></i>
			</div>
			<div class="description">
				<h4 class="info-title"> Find us at the office </h4>
				<p>Bld Mihail Kogalniceanu, nr. 8,7652 Bucharest, Romania</p>
			</div>
		</div>
		<div class="hestia-info info info-horizontal">
			<div class="icon icon-primary">
				<i class="fas fa-mobile-alt"></i>
			</div>
			<div class="description">
				<h4 class="info-title">Give us a ring</h4>
				<p>Michael Jordan <br> +40 762 321 762<br>Mon - Fri, 8:00-22:00</p>
			</div>
		</div>';
		$html = Hestia_Contact_Controls::sanitize_contact_field( $html );
		return apply_filters( 'hestia_contact_content_default', $html );
	}

	/**
	 * Render contact form via shortcode input.
	 */
	private function render_contact_form() {
		$contact_form_shortcode = get_theme_mod( 'hestia_contact_form_shortcode' );

		if ( empty( $contact_form_shortcode ) ) {
			return;
		}

		echo do_shortcode( wp_kses_post( $contact_form_shortcode ) );
	}


}
