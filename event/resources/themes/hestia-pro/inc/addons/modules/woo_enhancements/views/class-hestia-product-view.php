<?php
/**
 * Product page features view
 *
 * @package Inc/Addons/Modules/Woo_Enhancements/Views
 */

/**
 * Class Hestia_Product_View
 */
class Hestia_Product_View extends Hestia_Abstract_Main {

	/**
	 * Check if submodule should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {

		if ( ! is_product() ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the view.
	 */
	public function init() {
		add_action( 'wp', array( $this, 'run' ) );
	}

	/**
	 * Run product view.
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return;
		}
		$this->related_products();
		add_action( 'woocommerce_after_single_product', array( $this, 'render_exclusive_products' ), 100 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );

		$is_seamless_add_to_cart = get_theme_mod( 'hestia_enable_seamless_add_to_cart', false );
		if ( $is_seamless_add_to_cart === true ) {
			add_filter( 'body_class', array( $this, 'seamless_add_to_cart' ) );
		}
	}

	/**
	 * Add the seamless add to cart class on body.
	 *
	 * @param array $classes Body classes.
	 *
	 * @return array
	 */
	public function seamless_add_to_cart( $classes ) {

		if ( ! is_product() ) {
			return $classes;
		}

		$product_id = get_the_ID();
		$product    = wc_get_product( $product_id );

		if ( $product->is_type( 'external' ) ) {
			return $classes;
		}

		if ( $product->is_type( 'grouped' ) ) {
			return $classes;
		}

		$classes[] = 'seamless-add-to-cart';

		return $classes;
	}

	/**
	 * Enqueue style.
	 */
	public function enqueue_style() {
		$css                     = '';
		$show_exclusive_products = get_theme_mod( 'hestia_enable_exclusive_products', false );
		if ( $show_exclusive_products === true ) {
			$css .= '
			.woocommerce.single-product .product + section {
			    margin-top: 100px;
			}
			.woocommerce .exclusive-products h2 {
			    margin: 0 0 50px;
			    font-family: "Roboto Slab","Times New Roman",serif;
			    text-align: center;
		        font-weight: 700;
			}
			@media (max-width: 768px) {
			    .woocommerce .exclusive-products h2{
					margin-top: 50px
				}
			}
			';
		}

		$is_seamless_add_to_cart = get_theme_mod( 'hestia_enable_seamless_add_to_cart', false );
		if ( $is_seamless_add_to_cart === true ) {
			$css .= '
			.woocommerce div.product form.cart .button.loading:after {
				top: initial;
				font-size: 12px;
				font-family: WooCommerce;
                content: "\e01c";
			}
			.single-product .added_to_cart.wc-forward {
				font-size:12px;
				margin-left: 5px;
				line-height: 32px;
			}
			';
		}

		if ( empty( $css ) ) {
			return false;
		}

		wp_add_inline_style( 'hestia_style', $css );
	}

	/**
	 * Related products function.
	 */
	public function related_products() {
		$hide_related_products = get_theme_mod( 'hestia_hide_related_products', false );
		if ( $hide_related_products === true ) {
			remove_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
		}

		add_filter(
			'woocommerce_product_related_products_heading',
			function() {
				return get_theme_mod( 'hestia_related_products_title', __( 'Related products', 'hestia-pro' ) );
			}
		);

		add_filter(
			'woocommerce_output_related_products_args',
			function( $args ) {
				$args['posts_per_page'] = get_theme_mod( 'hestia_related_products_number', 4 );
				return $args;
			}
		);
	}

	/**
	 * Exclusive products function.
	 */
	public static function render_exclusive_products() {
		$show_exclusive_products = get_theme_mod( 'hestia_enable_exclusive_products', false );
		if ( $show_exclusive_products === false ) {
			return false;
		}

		$settings                 = array(
			'posts_per_page' => 4,
			'columns'        => 4,
			'orderby'        => 'rand',
			'post_type'      => 'product',
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',
				),
			),
		);
		$exclusive_products_title = get_theme_mod( 'hestia_exclusive_products_title', __( 'Exclusive products', 'hestia-pro' ) );
		$exclusive_categories     = get_theme_mod( 'hestia_exclusive_products_categories' );
		if ( ! empty( $exclusive_categories ) ) {
			$settings['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $exclusive_categories,
			);
		}

		$loop = new WP_Query( $settings );
		if ( ! $loop->have_posts() ) {
			return false;
		}

		echo '<section class="exclusive-products">';

		if ( ! empty( $exclusive_products_title ) ) {
			echo  '<h2>' . wp_kses_post( $exclusive_products_title ) . '</h2>';
		}

		woocommerce_product_loop_start();
		while ( $loop->have_posts() ) {
			$loop->the_post();

			wc_get_template_part( 'content', 'product' );
		}
		woocommerce_product_loop_end();

		wp_reset_postdata();
		echo '</section>';

		return true;
	}

}
