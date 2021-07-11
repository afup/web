<?php
/**
 * The Features Section
 *
 * @package Hestia
 */

/**
 * Class Hestia_Features_Section
 */
class Hestia_Shop_Section extends Hestia_Abstract_Main {
	/**
	 * Initialize Shop Section
	 */
	public function init() {
		$this->hook_section();
	}

	/**
	 * Hook section in.
	 */
	private function hook_section() {
		$section_priority = apply_filters( 'hestia_section_priority', 20, 'hestia_shop' );
		add_action( 'hestia_sections', array( $this, 'do_section' ), absint( $section_priority ) );
		add_action( 'hestia_do_shop_section', array( $this, 'render_section' ) );
	}

	/**
	 * Executes the hook on which the content is rendered.
	 */
	public function do_section() {
		do_action( 'hestia_do_shop_section', false );
	}

	/**
	 * Shop section content.
	 *
	 * @since Hestia 1.0
	 * @modified 1.1.51
	 *
	 * @param bool $is_shortcode flag used if section is called via a shortcode.
	 */
	public function render_section( $is_shortcode = false ) {

		/**
		 * Don't show section if Disable section is checked or it doesn't have any content.
		 * Show it if it's called as a shortcode.
		 */
		$hide_section  = get_theme_mod( 'hestia_shop_hide', false );
		$section_style = '';
		if ( $is_shortcode === false && (bool) $hide_section === true ) {
			if ( is_customize_preview() ) {
				$section_style = 'style="display: none"';
			} else {
				return;
			}
		}

		if ( ! class_exists( 'WooCommerce', false ) ) {
			return;
		}

		/**
		 * Gather data to display the section.
		 */
		if ( current_user_can( 'edit_theme_options' ) ) {
			/* translators: 1 - link to customizer setting. 2 - 'customizer' */
			$hestia_shop_subtitle = get_theme_mod( 'hestia_shop_subtitle', sprintf( __( 'Change this subtitle in %s.', 'hestia-pro' ), sprintf( '<a href="%1$s" class="default-link">%2$s</a>', esc_url( admin_url( 'customize.php?autofocus&#91;control&#93;=hestia_shop_subtitle' ) ), __( 'customizer', 'hestia-pro' ) ) ) );
		} else {
			$hestia_shop_subtitle = get_theme_mod( 'hestia_shop_subtitle' );
		}
		$hestia_shop_title = get_theme_mod( 'hestia_shop_title', esc_html__( 'Products', 'hestia-pro' ) );

		/**
		 * In case this function is called as shortcode, we remove the container and we add 'is-shortcode' class.
		 */
		$wrapper_class   = $is_shortcode === true ? 'is-shortcode' : 'section-gray';
		$container_class = $is_shortcode === true ? '' : 'container';

		hestia_before_shop_section_trigger(); ?>
		<section class="woocommerce hestia-shop products <?php echo esc_attr( $wrapper_class ); ?>" id="products" data-sorder="hestia_shop" <?php echo wp_kses_post( $section_style ); ?>>
			<?php
			hestia_before_shop_section_content_trigger();
			if ( $is_shortcode === false ) {
				hestia_display_customizer_shortcut( 'hestia_shop_hide', true );
			}
			?>
			<div class="<?php echo esc_attr( $container_class ); ?>">
				<?php
				hestia_top_shop_section_content_trigger();
				if ( $is_shortcode === false ) {
					?>
					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center hestia-shop-title-area">
							<?php
							hestia_display_customizer_shortcut( 'hestia_shop_title' );
							if ( ! empty( $hestia_shop_title ) || is_customize_preview() ) :
								?>
								<h2 class="hestia-title"><?php echo wp_kses_post( $hestia_shop_title ); ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( $hestia_shop_subtitle ) || is_customize_preview() ) : ?>
								<h5 class="description"><?php echo hestia_sanitize_string( $hestia_shop_subtitle ); ?></h5>
							<?php endif; ?>
						</div>
					</div>
					<?php
				}
				$this->shop_content();
				hestia_bottom_shop_section_content_trigger();
				?>
			</div>
			<?php hestia_after_shop_section_content_trigger(); ?>
		</section>
		<?php
		hestia_after_shop_section_trigger();
	}

	/**
	 * Get content for shop section.
	 *
	 * @since 1.1.31
	 * @modified 1.1.45
	 * @access public
	 */
	public function shop_content() {
		?>
		<div class="hestia-shop-content">
			<?php
			$hestia_shop_shortcode = get_theme_mod( 'hestia_shop_shortcode' );
			if ( ! empty( $hestia_shop_shortcode ) ) {
				echo do_shortcode( $hestia_shop_shortcode );
				echo '</div>';
				return;
			}
			$hestia_shop_items = get_theme_mod( 'hestia_shop_items', 4 );

			$args                   = array(
				'post_type' => 'product',
			);
			$args['posts_per_page'] = ! empty( $hestia_shop_items ) ? absint( $hestia_shop_items ) : 4;

			/* Exclude hidden products from the loop */
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',

				),
			);

			$hestia_shop_categories = get_theme_mod( 'hestia_shop_categories' );

			if ( ! empty( $hestia_shop_categories ) ) {
				array_push(
					$args['tax_query'],
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $hestia_shop_categories,
					)
				);
			}

			$hestia_shop_order = get_theme_mod( 'hestia_shop_order', 'DESC' );
			if ( ! empty( $hestia_shop_order ) ) {
				$args['order'] = $hestia_shop_order;
			}

			$loop = new WP_Query( $args );

			if ( $loop->have_posts() ) {
				$i = 1;
				echo '<div class="row"' . hestia_add_animationation( 'fade-up' ) . '>';
				while ( $loop->have_posts() ) {
					$loop->the_post();
					global $product;
					global $post;

					?>
					<div class="col-ms-6 col-sm-6 col-md-3 shop-item">
						<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
							<?php
							$thumbnail = function_exists( 'woocommerce_get_product_thumbnail' ) ? woocommerce_get_product_thumbnail() : '';
							if ( empty( $thumbnail ) && function_exists( 'wc_placeholder_img' ) ) {
								$thumbnail = wc_placeholder_img();
							}
							if ( ! empty( $thumbnail ) ) {
								?>
								<div class="card-image">
									<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
										<?php
										echo $thumbnail;

										do_action( 'hestia_shop_after_product_thumbnail' );
										?>
									</a>
									<div class="ripple-container"></div>
								</div>
								<?php
							}
							?>
							<div class="content">
								<?php
								if ( function_exists( 'wc_get_product_category_list' ) ) {
									$prod_id            = get_the_ID();
									$product_categories = wc_get_product_category_list( $prod_id );
								} else {
									$product_categories = $product->get_categories();
								}

								if ( apply_filters( 'hestia_show_category_on_product_card', true ) && ! empty( $product_categories ) ) {
									/**
									 * Explode categories in words by ',' separator and show only the first 2. If the value is modified to -1 or lower in
									 * a function hooked at hestia_shop_category_words, then show all categories.
									 */
									$categories   = explode( ',', $product_categories );
									$nb_of_cat    = apply_filters( 'hestia_shop_category_words', 2 );
									$nb_of_cat    = intval( $nb_of_cat );
									$cat          = $nb_of_cat > -1 ? hestia_limit_content( $categories, $nb_of_cat, ',', false ) : $product_categories;
									$allowed_html = array(
										'a' => array(
											'href' => array(),
											'rel'  => array(),
										),
									);
									echo '<h6 class="category">';
									echo wp_kses( $cat, $allowed_html );
									echo '</h6>';
								}
								?>

								<h4 class="card-title">
									<?php
									/**
									 * Explode title in words by ' ' separator and show only the first 6 words. If the value is modified to -1 or lower in
									 * a function hooked at hestia_shop_title_words, then show the full title
									 */
									$title          = the_title( '', '', false );
									$title_in_words = explode( ' ', $title );
									$title_limit    = apply_filters( 'hestia_shop_title_words', -1 );
									$title_limit    = intval( $title_limit );
									$limited_title  = $title_limit > -1 ? hestia_limit_content( $title_in_words, $title_limit, ' ' ) : $title;
									?>
									<a class="shop-item-title-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo esc_html( $limited_title ); ?></a>

								</h4>

								<?php
								if ( $post->post_excerpt ) {
									/**
									 * Explode the excerpt in words by ' ' separator and show only the first 60 words. If the value is modified to -1 or lower in
									 * a function hooked at hestia_shop_excerpt_words, then use the normal behavior from woocommece ( show post excerpt )
									 */
									$excerpt_in_words = explode( ' ', $post->post_excerpt );
									$excerpt_limit    = apply_filters( 'hestia_shop_excerpt_words', 60 );
									$excerpt_limit    = intval( $excerpt_limit );
									$limited_excerpt  = $excerpt_limit > -1 ? hestia_limit_content( $excerpt_in_words, $excerpt_limit, ' ' ) : $post->post_excerpt;
									?>
									<div class="card-description"><?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $limited_excerpt ) ); ?></div>
									<?php
								}
								?>

								<div class="footer">

									<?php
									$product_price = $product->get_price_html();

									if ( ! empty( $product_price ) ) {

										echo '<div class="price"><h4>';

										echo wp_kses(
											$product_price,
											array(
												'span' => array(
													'class' => array(),
												),
												'del'  => array(),
											)
										);

										echo '</h4></div>';

									}
									?>

									<div class="stats">
										<?php hestia_add_to_cart(); ?>
									</div>
								</div>
							</div>
						<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
					</div>
					<?php
					if ( $i % 4 === 0 ) {
						echo '</div><!-- /.row -->';
						echo '<div class="row">';
					}
					$i ++;
				}
				wp_reset_postdata();
				echo '</div>';
			}
			?>
		</div>
		<?php
	}
}
