<?php
/**
 * Functions for WooCommerce which only needs to be used when WooCommerce is active.
 *
 * @package Hestia
 * @since   Hestia 1.0
 */

// Get hooks file.
require_once( 'hooks.php' );

if ( ! function_exists( 'hestia_add_to_cart' ) ) :
	/**
	 * Custom add to cart button for WooCommerce.
	 *
	 * @since Hestia 1.0
	 */
	function hestia_add_to_cart() {
		global $product;

		if ( function_exists( 'method_exists' ) && method_exists( $product, 'get_type' ) ) {
			$prod_type = $product->get_type();
		} else {
			$prod_type = $product->product_type;
		}

		$prod_in_stock = $product->is_in_stock();
		if ( function_exists( 'method_exists' ) && method_exists( $product, 'get_stock_status' ) ) {
			$prod_in_stock = $product->get_stock_status();
		}

		if ( $product ) {
			$args     = array();
			$defaults = array(
				'quantity' => 1,
				'class'    => implode(
					' ',
					array_filter(
						array(
							'button',
							'product_type_' . $prod_type,
							$product->is_purchasable() && $prod_in_stock ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
						)
					)
				),
			);

			$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

			wc_get_template( 'inc/compatibility/woocommerce/add-to-cart.php', $args );
		}
	}
endif;

/**
 * Refresh WooCommerce cart count instantly.
 *
 * @since Hestia 1.0
 */
function hestia_woocommerce_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;

	$fragments['a.cart-contents']  = '<a class="cart-contents btn btn-white pull-right" href="' . esc_url( wc_get_cart_url() ) . '"
			title="' . esc_attr__( 'View your shopping cart', 'hestia-pro' ) . '">';
	$fragments['a.cart-contents'] .= '<i class="fas fa-shopping-cart"></i>';
	$fragments['a.cart-contents'] .= sprintf(
		/* translators: %d is number of items */
		_n( '%d item', '%d items', absint( $woocommerce->cart->cart_contents_count ), 'hestia-pro' ),
		absint( $woocommerce->cart->cart_contents_count )
	);
	$fragments['a.cart-contents'] .= ' - ';
	$fragments['a.cart-contents'] .= wp_kses(
		$woocommerce->cart->get_cart_total(),
		array(
			'span' => array(
				'class' => array(),
			),
		)
	);
	$fragments['a.cart-contents'] .= '</a>';
	return $fragments;
}

/**
 * Change the layout before the shop page main content
 */
function hestia_woocommerce_before_main_content() {

	do_action( 'hestia_before_woocommerce_wrapper' );

	$sidebar_layout = hestia_get_shop_sidebar_layout();
	$wrapper_class  = apply_filters( 'hestia_filter_woocommerce_content_classes', 'content-full col-md-12' );

	$main_container_classes = hestia_layout();

	echo '<div class="' . esc_attr( $main_container_classes ) . '">';
	echo '<div class="blog-post">';
	echo '<div class="container">';

	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
		echo '<div class="before-shop-main">';
			echo '<div class="row">';
				do_action( 'hestia_before_woocommerce_content' );

				echo '<div class="col-xs-12 col-md-9';
		if ( is_active_sidebar( 'sidebar-woocommerce' ) && ! is_singular( 'product' ) && $sidebar_layout !== 'full-width' ) {
			echo ' col-sm-12">';
		} else {
			echo ' col-sm-9">';
		}

					do_action( 'hestia_woocommerce_custom_reposition_left_shop_elements' );
				echo '</div>';

				$shop_ordering_class = 'col-xs-12 col-sm-3';

		if ( is_active_sidebar( 'sidebar-woocommerce' ) && ! is_singular( 'product' ) && $sidebar_layout !== 'full-width' ) {
			$shop_ordering_class = 'shop-sidebar-active col-xs-9 col-sm-9';
			hestia_render_sidebar_toggle( 'mobile' );
		}

		echo '<div class="' . esc_attr( $shop_ordering_class ) . ' col-md-3">';
		do_action( 'hestia_woocommerce_custom_reposition_right_shop_elements' );
		echo '</div>';

		hestia_render_sidebar_toggle( 'desktop' );

		echo '</div>';
		echo '</div>';
	} ?>
	<article id="post-<?php the_ID(); ?>" class="section section-text">
	<div class="row">
	<?php
	if ( $sidebar_layout === 'sidebar-left' || $sidebar_layout === 'off-canvas' ) {
		hestia_shop_sidebar();
	}
	?>
	<div id="woo-products-wrap" class="<?php echo esc_attr( $wrapper_class ); ?>">
	<?php
}

/**
 * Render sidebar toggle.
 *
 * @param string $type Mobile or Desktop.
 */
function hestia_render_sidebar_toggle( $type ) {
	echo '<div class="col-xs-3 col-sm-3 col-md-3 row-sidebar-toggle ' . esc_attr( $type ) . '">';
	echo '<span class="hestia-sidebar-open btn btn-border">';
	echo '<i class="fas fa-filter" aria-hidden="true"></i>';
	echo '</span>';
	echo '</div>';
}

/**
 * Change the layout after the shop page main content
 */
function hestia_woocommerce_after_main_content() {
	$hestia_page_sidebar_layout = hestia_get_shop_sidebar_layout();
	?>
	</div>
	<?php
	if ( $hestia_page_sidebar_layout === 'sidebar-right' ) {
		hestia_shop_sidebar();
	}
	?>
	</div>
	</article>
	</div>
	</div>
	<?php
}

/**
 * Change the layout before each single product listing
 */
function hestia_woocommerce_before_shop_loop_item() {
	$classes = apply_filters(
		'hestia_shop_product_card_classes',
		array(
			'card',
			'card-product',
			'pop-and-glow',
		)
	);
	$classes = implode( ' ', $classes );
	echo '<div class="' . esc_attr( $classes ) . '">';
}

/**
 * Change the layout after each single product listing
 */
function hestia_woocommerce_after_shop_loop_item() {
	echo '</div>';
}

/**
 * Change the layout of the thumbnail on single product listing
 */
function hestia_woocommerce_template_loop_product_thumbnail() {
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
			<?php do_action( 'hestia_shop_after_product_thumbnail_link' ); ?>
			<div class="ripple-container"></div>
		</div>
		<?php
	}
}

/**
 * Change the main content for single product listing
 */
function hestia_woocommerce_template_loop_product_title() {
	global $post;
	$current_product = wc_get_product( get_the_ID() );

	?>
	<div class="content">
		<?php
		$product_categories = get_the_terms( $post->ID, 'product_cat' );
		$i                  = false;
		if ( ! empty( $product_categories ) && apply_filters( 'hestia_show_category_on_product_card', true ) ) {
			/**
			 * Show only the first $nb_of_cat words. If the value is modified in hestia_shop_category_words filter with
			 * something lower than 0 then it will display all categories.
			 */
			$nb_of_cat = apply_filters( 'hestia_shop_category_words', 2 );
			$nb_of_cat = intval( $nb_of_cat );
			$index     = 0;

			if ( $nb_of_cat !== 0 ) {
				echo '<h6 class="category">';
				foreach ( $product_categories as $product_category ) {
					if ( $index < $nb_of_cat || $nb_of_cat < 0 ) {
						$product_cat_id   = $product_category->term_id;
						$product_cat_name = $product_category->name;
						if ( ! empty( $product_cat_id ) && ! empty( $product_cat_name ) ) {
							if ( $i ) {
								echo ' , ';
							}
							echo '<a href="' . esc_url( get_term_link( $product_cat_id, 'product_cat' ) ) . '">' . esc_html( $product_cat_name ) . '</a>';
							$i = true;
						}
						$index ++;
					}
				}
				echo '</h6>';
			}
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
			$title_limit    = apply_filters( 'hestia_shop_title_words', - 1 );
			$title_limit    = intval( $title_limit );
			$limited_title  = $title_limit > - 1 ? hestia_limit_content( $title_in_words, $title_limit, ' ' ) : $title;
			?>
			<a class="shop-item-title-link" href="<?php the_permalink(); ?>"
					title="<?php the_title_attribute(); ?>"><?php echo esc_html( $limited_title ); ?></a>
		</h4>
		<?php
		if ( $post->post_excerpt ) :
			/**
			 * Explode the excerpt in words by ' ' separator and show only the first 60 words. If the value is modified to -1 or lower in
			 * a function hooked at hestia_shop_excerpt_words, then use the normal behavior from woocommece ( show post excerpt )
			 */
			$excerpt_in_words = explode( ' ', $post->post_excerpt );
			$excerpt_limit    = apply_filters( 'hestia_shop_excerpt_words', 60 );
			$excerpt_limit    = intval( $excerpt_limit );
			$limited_excerpt  = $excerpt_limit > - 1 ? hestia_limit_content( $excerpt_in_words, $excerpt_limit, ' ' ) : $post->post_excerpt;
			?>
			<div class="card-description"><?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $limited_excerpt ) ); ?></div>
		<?php endif; ?>
		<div class="footer">
			<?php
			$product_price = $current_product->get_price_html();
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
	<?php
}

/**
 * Checkout page
 * Move the coupon fild and message info after the order table
 **/
function hestia_coupon_after_order_table_js() {
	wc_enqueue_js( '$( $( ".woocommerce-checkout div.woocommerce-info, .checkout_coupon, .woocommerce-form-login" ).detach() ).appendTo( "#hestia-checkout-coupon" );' );
}

/**
 * Checkout page
 * Add the id hestia-checkout-coupon to be able to Move the coupon fild and message info after the order table
 **/
function hestia_coupon_after_order_table() {
	echo '<div id="hestia-checkout-coupon"></div><div style="clear:both"></div>';
}

/**
 * Get shop sidebar layout.
 *
 * @return string
 */
function hestia_get_shop_sidebar_layout() {
	$hestia_shop_sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_shop_sidebar_layout', Hestia_General_Controls::get_shop_sidebar_layout_default() ) );
	if ( is_shop() ) {
		$pid = get_option( 'woocommerce_shop_page_id' );
		if ( ! empty( $pid ) ) {
			$values = get_post_custom( $pid );
			if ( array_key_exists( 'hestia_layout_select', $values ) ) {
				if ( $values['hestia_layout_select'][0] !== 'default' ) {
					$hestia_shop_sidebar_layout = esc_attr( $values['hestia_layout_select'][0] );
				}
			}
		}
	}
	return $hestia_shop_sidebar_layout;
}

/**
 * Function to display sidebar on shop.
 *
 * @since  1.1.24
 * @access public
 */
function hestia_shop_sidebar() {
	$hestia_page_sidebar_layout = hestia_get_shop_sidebar_layout();
	$bootstrap_classes          = $hestia_page_sidebar_layout === 'off-canvas' ? 'col-xs-12 col-md-4' : 'col-xs-12 col-md-3';

	$class_to_add = '';
	if ( $hestia_page_sidebar_layout === 'sidebar-right' ) {
		$class_to_add = 'hestia-has-sidebar';
	}

	if ( is_active_sidebar( 'sidebar-woocommerce' ) && ! is_singular( 'product' ) ) {
		?>
		<div class="<?php echo esc_attr( $bootstrap_classes ); ?> shop-sidebar-wrapper sidebar-toggle-container">
			<div class="row-sidebar-toggle">
				<span class="hestia-sidebar-close btn btn-border"><i class="fas fa-times" aria-hidden="true"></i></span>
			</div>
			<?php do_action( 'hestia_before_shop_sidebar' ); ?>
			<aside id="secondary" class="shop-sidebar card <?php echo apply_filters( 'hestia_shop_sidebar_card_classes', 'card-raised' ); ?> <?php echo esc_attr( $class_to_add ); ?>"
					role="complementary">
				<?php do_action( 'hestia_before_shop_sidebar_content' ); ?>
				<?php dynamic_sidebar( 'sidebar-woocommerce' ); ?>
				<?php do_action( 'hestia_after_shop_sidebar_content' ); ?>
			</aside><!-- .sidebar .widget-area -->
			<?php do_action( 'hestia_after_shop_sidebar' ); ?>
		</div>
		<?php
	} elseif ( is_customize_preview() && ! is_singular( 'product' ) ) {
		hestia_sidebar_placeholder( $class_to_add, 'sidebar-woocommerce' );
	}
}

/**
 * Remove title on shop main
 *
 * @return bool
 */
function hestia_woocommerce_hide_page_title() {
	return false;
}

if ( ! function_exists( 'hestia_cart_link_after_primary_navigation' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @since  1.0.0
	 */
	function hestia_cart_link_after_primary_navigation() {
		?>
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View cart', 'hestia-pro' ); ?>"
				class="nav-cart-icon">
			<i class="fas fa-shopping-cart"></i><?php echo trim( ( WC()->cart->get_cart_contents_count() > 0 ) ? '<span>' . WC()->cart->get_cart_contents_count() . '</span>' : '' ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'hestia_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param  array $fragments Fragments to refresh via AJAX.
	 *
	 * @return array Fragments to refresh via AJAX.
	 */
	function hestia_cart_link_fragment( $fragments ) {
		global $woocommerce;
		ob_start();
		hestia_cart_link_after_primary_navigation();
		$fragments['.nav-cart-icon'] = ob_get_clean();

		return $fragments;
	}
}

/**
 * Add before cart totals code for card.
 */
function hestia_woocommerce_before_cart_totals() {
	echo '<div class="card card-raised"><div class="content">';
}

/**
 * Add after cart totals code for card.
 */
function hestia_woocommerce_after_cart_totals() {
	echo '</div></div>';
}


/**
 * Add compatibility with WooCommerce Product Images customizer controls.
 *
 * Because there are no filters in WooCommerce to change the default values of those controls,
 * we have to update those controls in order to have the same image size as it was until now.
 * This function runs only once to update those controls.
 *
 * Even if there were filters, woocommerce does update_options in their plugin so if we change
 * the defaults it's equal with 0.
 */
function hestia_woocommerce_product_images_compatibility() {
	$execute = get_option( 'hestia_update_woocommerce_customizer_controls', false );
	if ( $execute !== false ) {
		return;
	}

	update_option( 'woocommerce_thumbnail_cropping', 'custom' );
	update_option( 'woocommerce_thumbnail_cropping_custom_width', '23' );
	update_option( 'woocommerce_thumbnail_cropping_custom_height', '35' );

	if ( class_exists( 'WC_Regenerate_Images', false ) ) {
		$regenerate_obj = new WC_Regenerate_Images();
		$regenerate_obj::init();
		if ( method_exists( $regenerate_obj, 'maybe_regenerate_images' ) ) {
			$regenerate_obj::maybe_regenerate_images();
		} elseif ( method_exists( $regenerate_obj, 'maybe_regenerate_images_option_update' ) ) {
			// Force woocommerce 3.3.1 to regenerate images
			$regenerate_obj::maybe_regenerate_images_option_update( 1, 2, '' );
		}
	}
	update_option( 'hestia_update_woocommerce_customizer_controls', true );
}

add_action( 'after_setup_theme', 'hestia_woocommerce_product_images_compatibility' );

/**
 * Move added to cart/view cart notice inside the product on product page
 */
function hestia_view_cart_notice() {
	if ( function_exists( 'woocommerce_output_all_notices' ) ) {
		remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
		add_action( 'woocommerce_before_single_product_summary', 'woocommerce_output_all_notices', 10 ); /* Move notices position */
	} else {
		remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
		add_action( 'woocommerce_before_single_product_summary', 'wc_print_notices', 10 ); /* Move notices position */
	}
}
add_action( 'after_setup_theme', 'hestia_view_cart_notice', 15 );
