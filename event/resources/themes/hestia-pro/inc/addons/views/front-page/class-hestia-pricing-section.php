<?php
/**
 * The Pricing Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Pricing_Section
 */
class Hestia_Pricing_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook the section.
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 35, 'hestia_pricing' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_pricing_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_pricing_section', false );
	}

	/**
	 * Pricing section content.
	 * This function can be called from a shortcode too.
	 * When it's called as shortcode, the title and the subtitle shouldn't appear and it should be visible all the time,
	 * it shouldn't matter if is disable on front page.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 */
	public function render_section( $is_shortcode = false ) {
		/**
		 * Don't show section if Disable section is checked or it doesn't have any content.
		 * Show it if it's called as a shortcode.
		 */
		$hide_section  = get_theme_mod( 'hestia_pricing_hide', true );
		$section_style = '';
		if ( ! $is_shortcode && (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}

		/**
		 * Gather data to display the section.
		 */
		$hestia_pricing_title    = get_theme_mod( 'hestia_pricing_title', esc_html__( 'Choose a plan for your next project', 'hestia-pro' ) );
		$hestia_pricing_subtitle = get_theme_mod( 'hestia_pricing_subtitle', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ) );

		$hestia_pricing_table_one_title    = get_theme_mod( 'hestia_pricing_table_one_title', esc_html__( 'Basic Package', 'hestia-pro' ) );
		$card_pricing_table_one_icon_type  = get_theme_mod( 'hestia_pricing_table_one_icon' );
		$hestia_pricing_table_one_price    = get_theme_mod( 'hestia_pricing_table_one_price', '<small>$</small>0' );
		$default                           = sprintf( '<b>%1$s</b> %2$s', esc_html__( '1', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
											sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '1GB', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
											sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '100GB', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
											sprintf( '\n<b>%1$s</b> %2$s', esc_html__( '2', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );
		$hestia_pricing_table_one_features = get_theme_mod( 'hestia_pricing_table_one_features', $default );
		if ( ! is_array( $hestia_pricing_table_one_features ) ) {
			$hestia_pricing_table_one_features = explode( '\n', str_replace( '\r', '', wp_kses_post( force_balance_tags( $hestia_pricing_table_one_features ) ) ) );
		}
		$hestia_pricing_table_one_link = get_theme_mod( 'hestia_pricing_table_one_link', '#' );

		$hestia_pricing_table_one_text     = get_theme_mod( 'hestia_pricing_table_one_text', esc_html__( 'Free Download', 'hestia-pro' ) );
		$hestia_pricing_table_two_title    = get_theme_mod( 'hestia_pricing_table_two_title', esc_html__( 'Premium Package', 'hestia-pro' ) );
		$card_pricing_table_two_icon_type  = get_theme_mod( 'hestia_pricing_table_two_icon' );
		$hestia_pricing_table_two_price    = get_theme_mod( 'hestia_pricing_table_two_price', '<small>$</small>49' );
		$default                           = sprintf( '<b>%1$s</b> %2$s', esc_html__( '5', 'hestia-pro' ), esc_html__( 'Domain', 'hestia-pro' ) ) .
											sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Storage', 'hestia-pro' ) ) .
											sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Bandwidth', 'hestia-pro' ) ) .
											sprintf( ' \n<b>%1$s</b> %2$s', esc_html__( 'Unlimited', 'hestia-pro' ), esc_html__( 'Databases', 'hestia-pro' ) );
		$hestia_pricing_table_two_features = get_theme_mod( 'hestia_pricing_table_two_features', $default );
		if ( ! is_array( $hestia_pricing_table_two_features ) ) {
			$hestia_pricing_table_two_features = explode( '\n', str_replace( '\r', '', wp_kses_post( force_balance_tags( $hestia_pricing_table_two_features ) ) ) );
		}
		$hestia_pricing_table_two_link = get_theme_mod( 'hestia_pricing_table_two_link', '#' );
		$hestia_pricing_table_two_text = get_theme_mod( 'hestia_pricing_table_two_text', esc_html__( 'Order Now', 'hestia-pro' ) );

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';

		$html_allowed_strings = array(
			$hestia_pricing_title,
			$hestia_pricing_subtitle,
			$hestia_pricing_table_one_price,
			$hestia_pricing_table_one_features,
			$hestia_pricing_table_two_price,
			$hestia_pricing_table_two_features,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_pricing_section_trigger(); ?>
		<section class="hestia-pricing pricing section-gray <?php echo esc_attr( $wrapper_class ); ?>" id="pricing"
				data-sorder="hestia_pricing" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_pricing_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_pricing_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php hestia_top_pricing_section_content_trigger(); ?>
				<div class="row">
					<div class="col-md-4 col-lg-4 hestia-pricing-title-area">
						<?php
						hestia_display_customizer_shortcut( 'hestia_pricing_title' );
						if ( ! empty( $hestia_pricing_title ) || is_customize_preview() ) :
							?>
							<h2 class="hestia-title"><?php echo wp_kses_post( $hestia_pricing_title ); ?></h2>
						<?php endif; ?>
						<?php if ( ! empty( $hestia_pricing_subtitle ) || is_customize_preview() ) : ?>
							<p class="text-gray"><?php echo wp_kses_post( $hestia_pricing_subtitle ); ?></p>
						<?php endif; ?>
					</div>
					<div class="col-md-8 col-lg-7 col-lg-offset-1">
						<div class="row">
							<div class="col-ms-6 col-sm-6 hestia-table-one" <?php echo hestia_add_animationation( 'fade-up' ); ?>>
								<?php hestia_display_customizer_shortcut( 'hestia_pricing_table_one_title' ); ?>
								<div class="card card-pricing card-raised">
									<div class="content">
										<?php if ( ! empty( $hestia_pricing_table_one_title ) || is_customize_preview() ) : ?>
											<h6 class="category"><?php echo esc_html( $hestia_pricing_table_one_title ); ?></h6>
										<?php endif; ?>
										<?php
										if ( ! empty( $card_pricing_table_one_icon_type ) ) {
											$accent_color = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );
											if ( ! empty( $accent_color ) ) {
												$inline_style = 'color: ' . $accent_color . ';';
												echo ' <div class="hestia-pricing-icon-wrapper pricing-has-icon" style="' . esc_attr( $inline_style ) . '">';
													echo '<i class="' . esc_attr( hestia_display_fa_icon( $card_pricing_table_one_icon_type ) ) . '"></i>';
												echo '</div>';
											} else {
												echo ' <div class="hestia-pricing-icon-wrapper pricing-has-icon"><i class="' . esc_attr( hestia_display_fa_icon( $card_pricing_table_one_icon_type ) ) . '"></i></div>';
											}
										} else {
											echo '<div class="hestia-pricing-icon-wrapper" style="display: none;"><i></i></div>';
										}
										?>
										<?php if ( ! empty( $hestia_pricing_table_one_price ) || is_customize_preview() ) : ?>
											<h3 class="card-title"><?php echo wp_kses_post( $hestia_pricing_table_one_price ); ?></h3>
										<?php endif; ?>

										<?php if ( ! empty( $hestia_pricing_table_one_features ) ) : ?>
											<ul>
												<?php foreach ( $hestia_pricing_table_one_features as $feature ) : ?>
													<li><?php echo wp_kses_post( $feature ); ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>

										<?php
										if ( ( ! empty( $hestia_pricing_table_one_link ) && ! empty( $hestia_pricing_table_one_text ) ) || is_customize_preview() ) {
											$link_html = '<a href="' . esc_url( $hestia_pricing_table_one_link ) . '"';
											if ( function_exists( 'hestia_is_external_url' ) ) {
												$link_html .= hestia_is_external_url( $hestia_pricing_table_one_link );
											}
											$link_html .= ' class="btn btn-primary">';
											$link_html .= esc_html( $hestia_pricing_table_one_text );
											$link_html .= '</a>';
											echo wp_kses_post( $link_html );
										}
										?>
									</div>
								</div>
							</div>
							<div class="col-ms-6 col-sm-6 hestia-table-two" <?php echo hestia_add_animationation( 'fade-left' ); ?>>
								<?php hestia_display_customizer_shortcut( 'hestia_pricing_table_two_title' ); ?>
								<div class="card card-pricing card-plain">
									<div class="content">
										<?php if ( ! empty( $hestia_pricing_table_two_title ) || is_customize_preview() ) : ?>
											<h6 class="category"><?php echo esc_html( $hestia_pricing_table_two_title ); ?></h6>
										<?php endif; ?>
										<?php
										if ( ! empty( $card_pricing_table_two_icon_type ) ) {
											$accent_color = get_theme_mod( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ) );
											if ( ! empty( $accent_color ) ) {
												$inline_style = 'color: ' . $accent_color . ';';
												echo ' <div class="hestia-pricing-icon-wrapper pricing-has-icon" style="' . esc_attr( $inline_style ) . '"><i class="' . esc_attr( hestia_display_fa_icon( $card_pricing_table_two_icon_type ) ) . '"></i></div>';
											} else {
												echo ' <div class="hestia-pricing-icon-wrapper pricing-has-icon"><i class="' . esc_attr( hestia_display_fa_icon( $card_pricing_table_two_icon_type ) ) . '"></i></div>';
											}
										} else {
											echo '<div class="hestia-pricing-icon-wrapper" style="display: none;"><i></i></div>';
										}
										?>
										<?php if ( ! empty( $hestia_pricing_table_two_price ) || is_customize_preview() ) : ?>
											<h3 class="card-title"><?php echo wp_kses_post( $hestia_pricing_table_two_price ); ?></h3>
										<?php endif; ?>
										<?php if ( ! empty( $hestia_pricing_table_two_features ) ) : ?>
											<ul>
												<?php foreach ( $hestia_pricing_table_two_features as $feature ) : ?>
													<li><?php echo wp_kses_post( $feature ); ?></li>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>
										<?php
										if ( ( ! empty( $hestia_pricing_table_two_link ) && ! empty( $hestia_pricing_table_two_text ) ) || is_customize_preview() ) {
											echo '<a href="' . esc_url( $hestia_pricing_table_two_link ) . '"';
											if ( function_exists( 'hestia_is_external_url' ) ) {
												echo hestia_is_external_url( $hestia_pricing_table_two_link );
											}
											echo ' class="btn btn-primary">' . esc_html( $hestia_pricing_table_two_text ) . '</a>';
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php hestia_bottom_pricing_section_content_trigger(); ?>
			</div>
			<?php hestia_after_pricing_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_pricing_section_trigger();
	}


}
