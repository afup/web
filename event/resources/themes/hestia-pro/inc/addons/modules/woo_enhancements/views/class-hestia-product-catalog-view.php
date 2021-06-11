<?php
/**
 * Product catalog view
 *
 * @package Inc/Addons/Modules/Woo_Enhancements/Views
 */

/**
 * Class Hestia_Woo_Product_View
 */
class Hestia_Product_Catalog_View extends Hestia_Abstract_Main {

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
		add_filter( 'hestia_shop_product_card_classes', array( $this, 'card_style' ), 10, 1 );
		add_filter( 'hestia_shop_product_card_classes', array( $this, 'product_image_style' ), 10, 1 );
		add_action( 'hestia_shop_after_product_thumbnail', array( $this, 'shop_add_secondary_thumbnail' ) );
		add_filter( 'body_class', array( $this, 'add_off_canvas' ) );
		$this->manage_sale_tag();
	}

	/**
	 * Add off canvas option to shop sidebar.
	 */
	public function add_off_canvas( $classes ) {
		$shop_sidebar = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_shop_sidebar_layout', Hestia_General_Controls::get_shop_sidebar_layout_default() ) );
		if ( $shop_sidebar === 'off-canvas' ) {
			$classes[] = 'off-canvas';
		}
		return $classes;
	}

	/**
	 * Manage sale tag.
	 */
	private function manage_sale_tag() {
		$hestia_product_style = get_theme_mod( 'hestia_product_style', 'boxed' );
		if ( $hestia_product_style === 'plain' ) {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 20 );
			add_action( 'hestia_shop_after_product_thumbnail_link', 'woocommerce_show_product_loop_sale_flash', 20 );
		}
	}

	/**
	 * Add card style class
	 *
	 * @param array $classes Card style.
	 *
	 * @return array
	 */
	public function card_style( $classes ) {

		$hestia_product_style = get_theme_mod( 'hestia_product_style', 'boxed' );

		if ( in_array( $hestia_product_style, array( 'plain', 'boxed' ), true ) ) {
			$classes[] = ' card-' . $hestia_product_style;
		}

		return $classes;
	}

	/**
	 * Product image style ( zoom/swipe ).
	 *
	 * @param array $classes Product classes.
	 *
	 * @return array
	 */
	public function product_image_style( $classes ) {

		$hestia_product_hover_style = get_theme_mod( 'hestia_product_hover_style', 'pop-and-glow' );

		if ( $hestia_product_hover_style === 'pop-and-glow' ) {
			return $classes;
		}

		$index = array_search( 'pop-and-glow', $classes, true );
		if ( $index !== false ) {
			unset( $classes[ $index ] );
		}

		if ( $hestia_product_hover_style === 'none' ) {
			return $classes;
		}

		if ( $hestia_product_hover_style === 'swap-images' ) {
			global $product;
			if ( ! method_exists( $product, 'get_gallery_image_ids' ) ) {
				return $classes;
			}

			$gallery_attachment_ids = $product->get_gallery_image_ids();
			if ( ! empty( $gallery_attachment_ids[0] ) && ! empty( wp_get_attachment_url( $gallery_attachment_ids[0] ) ) ) {
				$classes[] = $hestia_product_hover_style;
				return $classes;
			}
			$hestia_product_hover_style = '';
		}

		$classes[] = $hestia_product_hover_style;

		return $classes;
	}

	/**
	 * Get the second image from product gallery if image style is swipe.
	 *
	 * @return bool
	 */
	public function shop_add_secondary_thumbnail() {

		$hestia_product_hover_style = get_theme_mod( 'hestia_product_hover_style', 'pop-and-glow' );
		if ( 'swap-images' !== $hestia_product_hover_style ) {
			return false;
		}

		global $product;
		if ( ! method_exists( $product, 'get_gallery_image_ids' ) ) {
			return false;
		}

		$shop_isle_gallery_attachment_ids = $product->get_gallery_image_ids();
		$image_size                       = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
		if ( empty( $shop_isle_gallery_attachment_ids[0] ) ) {
			return false;
		}

		echo wp_get_attachment_image( $shop_isle_gallery_attachment_ids[0], $image_size, '', 'data-secondary' );

		return true;

	}
}
