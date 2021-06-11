<?php
/**
 * The Blog Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Blog_Section
 */
class Hestia_Blog_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize Blog Section
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section in/
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 60, 'hestia_blog' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ), 2 );
		add_action( 'hestia_do_blog_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_blog_section', false );
	}

	/**
	 * Blog section content.
	 */
	public function render_section( $is_shortcode = false ) {

		/**
		 * Don't show section if Disable section is checked.
		 * Show it if it's called as a shortcode.
		 */
		$hide_section  = get_theme_mod( 'hestia_blog_hide', false );
		$section_style = '';
		if ( $is_shortcode === false && (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}

		/**
		 * Gather data to display the section.
		 */
		if ( current_user_can( 'edit_theme_options' ) ) {
			/* translators: 1 - link to customizer setting. 2 - 'customizer' */
			$hestia_blog_subtitle = get_theme_mod( 'hestia_blog_subtitle', sprintf( __( 'Change this subtitle in the %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_blog_subtitle' ) ), __( 'Customizer', 'hestia-pro' ) ) ) );
		} else {
			$hestia_blog_subtitle = get_theme_mod( 'hestia_blog_subtitle' );
		}
		$hestia_blog_title = get_theme_mod( 'hestia_blog_title', __( 'Blog', 'hestia-pro' ) );
		if ( $is_shortcode ) {
			$hestia_blog_title    = '';
			$hestia_blog_subtitle = '';
		}

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : '';
		$container_class = $is_shortcode === true ? '' : 'container';

		$html_allowed_strings = array(
			$hestia_blog_title,
			$hestia_blog_subtitle,
		);
		maybe_trigger_fa_loading( $html_allowed_strings );

		hestia_before_blog_section_trigger(); ?>
		<section class="hestia-blogs <?php echo esc_attr( $wrapper_class ); ?>" id="blog"
			data-sorder="hestia_blog" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_blog_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_blog_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_blog_section_content_trigger();
				if ( $is_shortcode === false ) {
					?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center hestia-blogs-title-area">
							<?php
							hestia_display_customizer_shortcut( 'hestia_blog_title' );
							if ( ! empty( $hestia_blog_title ) || is_customize_preview() ) {
								echo '<h2 class="hestia-title">' . wp_kses_post( $hestia_blog_title ) . '</h2>';
							}
							if ( ! empty( $hestia_blog_subtitle ) || is_customize_preview() ) {
								echo '<h5 class="description">' . hestia_sanitize_string( $hestia_blog_subtitle ) . '</h5>';
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
				<div class="hestia-blog-content">
				<?php
				$this->blog_content();
				?>
				</div>
				<?php hestia_bottom_blog_section_content_trigger(); ?>
			</div>
			<?php hestia_after_blog_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_blog_section_trigger();
	}

	/**
	 * Blog content/
	 */
	public function blog_content() {

		$hestia_blog_items      = get_theme_mod( 'hestia_blog_items', 3 );
		$args                   = array(
			'ignore_sticky_posts' => true,
		);
		$args['posts_per_page'] = ! empty( $hestia_blog_items ) ? absint( $hestia_blog_items ) : 3;

		$hestia_blog_categories = get_theme_mod( 'hestia_blog_categories' );

		if ( ! empty( $hestia_blog_categories[0] ) && sizeof( $hestia_blog_categories ) >= 1 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $hestia_blog_categories,
				),
			);
		}

		$loop = new WP_Query( $args );

		$allowed_html = array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'i'      => array(
				'class' => array(),
			),
			'span'   => array(),
		);

		if ( ! $loop->have_posts() ) {
			return;
		}
			$i = 1;
			echo '<div class="row" ' . hestia_add_animationation( 'fade-up' ) . '>';
		while ( $loop->have_posts() ) :
			$loop->the_post();
			?>
			<article class="col-xs-12 col-ms-10 col-ms-offset-1 col-sm-8 col-sm-offset-2 <?php echo esc_attr( apply_filters( 'hestia_blog_per_row_class', 'col-md-4' ) ); ?> hestia-blog-item">
				<div class="card card-plain card-blog">
					<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-image">
								<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
									<?php the_post_thumbnail( 'hestia-blog' ); ?>
								</a>
							</div>
						<?php endif; ?>
					<div class="content">
						<h6 class="category"><?php echo hestia_category(); ?></h6>
						<h4 class="card-title entry-title">
							<a class="blog-item-title-link" href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
								<?php echo wp_kses( force_balance_tags( get_the_title() ), $allowed_html ); ?>
							</a>
						</h4>
						<p class="card-description"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
					</div>
				</div>
				</article>
				<?php
				if ( $i % apply_filters( 'hestia_blog_per_row_no', 3 ) === 0 ) {
					echo '</div><!-- /.row -->';
					echo '<div class="row" ' . hestia_add_animationation( 'fade-up' ) . '>';
				}
				$i++;
			endwhile;
			echo '</div>';

			wp_reset_postdata();
	}

}
