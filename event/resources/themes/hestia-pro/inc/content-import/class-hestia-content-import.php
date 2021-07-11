<?php
/**
 * Hestia import handler.
 *
 * @package Hestia
 * @since   1.1.49
 */

/**
 * Class Hestia_Content_Import
 *
 * Handles content import from Azera, Llorix and Parallax One.
 */
final class Hestia_Content_Import {

	/**
	 * Previous theme slug
	 *
	 * @var mixed|string|void
	 */
	private $previous_theme = '';

	/**
	 * Simple theme mods
	 *
	 * @var array
	 */
	private $simple_theme_mods = array(

		// Big title
		'hestia_big_title_title'       => 'header_title',
		'hestia_big_title_text'        => 'header_subtitle',
		'hestia_big_title_button_text' => 'header_button_text',
		'hestia_big_title_button_link' => 'header_button_link',

		// Logos section
		'hestia_clients_bar_hide'      => 'logos_show',
		'hestia_clients_bar_content'   => 'logos_content',

		// Ribbon section
		'hestia_ribbon_hide'           => 'ribbon_show',
		'hestia_ribbon_text'           => 'ribbon_title',
		'hestia_ribbon_button_text'    => 'button_text',
		'hestia_ribbon_button_url'     => 'button_link',

		// Contact subtitle
		'hestia_contact_subtitle'      => 'copyright',

		// Features section
		'hestia_features_hide'         => 'our_services_show',
		'hestia_features_title'        => 'our_services_title',
		'hestia_features_subtitle'     => 'our_services_subtitle',

		// About section
		'hestia_about_hide'            => 'our_story_show',

		// Team section
		'hestia_team_hide'             => 'our_team_show',
		'hestia_team_title'            => 'our_team_title',
		'hestia_team_subtitle'         => 'our_team_subtitle',
		'hestia_team_content'          => 'team_content',

		// Testimonials
		'hestia_testimonials_hide'     => 'happy_customers_show',
		'hestia_testimonials_title'    => 'happy_customers_title',
		'hestia_testimonials_subtitle' => 'happy_customers_subtitle',
		'hestia_testimonials_content'  => 'testimonials_content',

		// Portfolio
		'hestia_portfolio_title'       => 'plus_portfolio_section_title',
		'hestia_portfolio_subtitle'    => 'plus_portfolio_section_subtitle',
		'hestia_portfolio_items'       => 'plus_number_of_portfolio_posts',

		// Shop
		'hestia_shop_hide'             => 'shop_section_show',

		// Copyright
		'hestia_general_credits'       => 'pwd',
	);

	/**
	 * Previous theme content
	 *
	 * @var array
	 */
	private $previous_theme_content = array();

	/**
	 * Hestia_Content_Import constructor.
	 *
	 * @access public
	 * @since  1.1.49
	 */
	public function __construct() {

		// Get the name of the previously active theme.
		$this->previous_theme = strtolower( get_option( 'theme_switched' ) );

		// Get the theme mods from the previous theme.
		$this->previous_theme_content = get_option( 'theme_mods_' . $this->previous_theme );

	}

	/**
	 * Main import handler function.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	public final function import() {

		if ( ! in_array( $this->previous_theme, array( 'azera-shop', 'parallax-one', 'llorix-one-lite' ), true ) ) {
			return;
		}
		// Prefix the theme mods with the previously active theme slug.
		$this->prefix_theme_mods( $this->simple_theme_mods );

		// Add exceptions.
		$this->add_exceptions();

		// Set all mods in the $simple_theme_mods array.
		$this->set_simple_mods( $this->simple_theme_mods );

		// Import content.
		$this->import_content();

	}

	/**
	 * Prefix theme mods.
	 *
	 * @param theme -mods $mods theme mods array.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private final function prefix_theme_mods( $mods ) {
		$prefix = str_replace( '-', '_', $this->previous_theme ) . '_';
		if ( ! empty( $mods ) ) {
			foreach ( $mods as $hestia_mod => $previous_mod_unprefixed ) {
				$this->simple_theme_mods[ $hestia_mod ] = $prefix . $previous_mod_unprefixed;
			}
		}

	}

	/**
	 * Add exceptions || remove unused settings.
	 *
	 * Add exceptions and bail if the previous theme was not Azera, Parallax or Llorix,
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private final function add_exceptions() {
		// Add exceptions and bail if there's another theme than these three.
		switch ( $this->previous_theme ) {
			case 'azera-shop':
				$this->azera_specific_changes();

				$theme_exceptions        = array(
					'hestia_big_title_background' => 'header_image',
					'hestia_ribbon_background'    => 'azera_shop_ribbon_background',
					'hestia_shop_title'           => 'azera_shop_shop_section_title',
					'hestia_shop_subtitle'        => 'azera_shop_shop_section_subtitle',
					'hestia_shop_items'           => 'azera_shop_number_of_products',
				);
				$this->simple_theme_mods = array_merge( $this->simple_theme_mods, $theme_exceptions );
				break;
			case 'parallax-one':
				$this->parallax_specific_changes();
				$theme_exceptions        = array(
					'hestia_big_title_background' => 'header_image',
					'hestia_ribbon_background'    => 'paralax_one_ribbon_background',
					'hestia_shop_title'           => 'parallax_one_plus_shop_section_title',
					'hestia_shop_subtitle'        => 'parallax_one_plus_shop_section_subtitle',
					'hestia_shop_items'           => 'parallax_one_plus_number_of_products',
					'hestia_blog_title'           => 'parallax_one_latest_news_title',
				);
				$this->simple_theme_mods = array_merge( $this->simple_theme_mods, $theme_exceptions );
				break;
			case 'llorix-one-lite':
				$this->llorix_specific_changes();
				$theme_exceptions        = array(
					'hestia_big_title_background' => 'header_image',
					'hestia_ribbon_background'    => 'llorix_one_lite_ribbon_background',
					'hestia_shop_title'           => 'llorix_one_plus_shop_section_title',
					'hestia_shop_subtitle'        => 'llorix_one_plus_shop_section_subtitle',
					'hestia_shop_items'           => 'llorix_one_plus_number_of_products',
					'hestia_blog_title'           => 'llorix_one_lite_latest_news_title',
				);
				$this->simple_theme_mods = array_merge( $this->simple_theme_mods, $theme_exceptions );
				break;
		}
	}


	/**
	 * Import content from previous theme.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private function import_content() {

		require_once( HESTIA_PHP_INCLUDE . 'content-import/class-hestia-import-utilities.php' );
		$utilities = new Hestia_Import_Utilities();

		$prefix = str_replace( '-', '_', $this->previous_theme ) . '_';

		/**
		 * Import logo.
		 */
		$logo_id = $prefix === 'parallax_one_' ? 'paralax_one_logo' : $prefix . 'logo';
		if ( ! empty( $this->previous_theme_content[ $logo_id ] ) ) {
			$utilities->update_logo( $this->previous_theme_content[ $logo_id ] );
		}

		/**
		 * Enable Clients bar section if user have content in it in previous theme.
		 */
		$clients_hide = get_theme_mod( 'hestia_clients_bar_hide' );
		if ( ! empty( $this->previous_theme_content[ $prefix . 'logos_content' ] ) && empty( $clients_hide ) ) {
			set_theme_mod( 'hestia_clients_bar_hide', false );
		}

		/**
		 * Enable ribbon section if user have content in it in previous theme.
		 */
		$ribbon_hide = get_theme_mod( 'hestia_ribbon_hide' );
		if ( ( ! empty( $this->previous_theme_content[ $prefix . 'ribbon_title' ] ) || ! empty( $this->previous_theme_content[ $prefix . 'button_text' ] ) ) && empty( $ribbon_hide ) ) {
			set_theme_mod( 'hestia_ribbon_hide', false );
		}

		/**
		 * Features content. Check if it contains stamp icons, replace them and update content in hestia.
		 */
		if ( ! empty( $this->previous_theme_content[ $prefix . 'services_content' ] ) ) {
			$json = $utilities->update_icons( $this->previous_theme_content[ $prefix . 'services_content' ] );
			if ( ! empty( $json ) ) {
				set_theme_mod( 'hestia_features_content', $json );
			}
		}

		/**
		 * Shop section.
		 */
		$woo_cat_control_id = $prefix . 'woocomerce_categories';
		switch ( $prefix ) {
			case 'llorix_one_lite_':
				$woo_cat_control_id = 'llorix_one_plus_woocomerce_categories';
				break;
			case 'parallax_one_':
				$woo_cat_control_id = 'parallax_one_plus_woocomerce_categories';
				break;
		}
		if ( ! empty( $this->previous_theme_content[ $woo_cat_control_id ] ) ) {
			$utilities->update_shop_category( $this->previous_theme_content[ $woo_cat_control_id ] );
		}

		/**
		 * Shortcodes section.
		 */
		if ( ( empty( $this->previous_theme_content[ $prefix . 'shortcodes_section_show' ] ) || (bool) $this->previous_theme_content[ $prefix . 'shortcodes_section_show' ] !== true ) && ! empty( $this->previous_theme_content[ $prefix . 'shortcodes_settings' ] ) ) {
			$utilities->shortcodes_section_to_html( $this->previous_theme_content[ $prefix . 'shortcodes_settings' ] );
		}

		/**
		 * Contact section.
		 */
		if ( ( empty( $this->previous_theme_content[ $prefix . 'contact_info_show' ] ) || (bool) $this->previous_theme_content[ $prefix . 'contact_info_show' ] !== true ) && ! empty( $this->previous_theme_content[ $prefix . 'contact_info_content' ] ) ) {
			$utilities->contact_to_html( $this->previous_theme_content[ $prefix . 'contact_info_content' ] );
		}

		/**
		 * About section.
		 */
		$settings = array();
		if ( ! empty( $this->previous_theme_content[ $prefix . 'our_story_title' ] ) ) {
			$settings['title'] = $this->previous_theme_content[ $prefix . 'our_story_title' ];
		};

		if ( ! empty( $this->previous_theme_content[ $prefix . 'our_story_text' ] ) ) {
			$settings['text'] = $this->previous_theme_content[ $prefix . 'our_story_text' ];
		};

		if ( ! empty( $this->previous_theme_content[ $prefix . 'our_story_image' ] ) ) {
			$settings['image'] = $this->previous_theme_content[ $prefix . 'our_story_image' ];
		};
		$layout_control = '';
		switch ( $prefix ) {
			case 'llorix_one_lite_':
				$layout_control = 'llorix_one_plus_about_layout';
				break;
			case 'parallax_one_':
				$layout_control = 'parallax_one_plus_about_layout';
				break;
			case 'azera_shop_':
				$layout_control = 'azera_shop_plus_about_layout';
				break;
		}
		if ( ! empty( $layout_control ) && ! empty( $this->previous_theme_content[ $layout_control ] ) ) {
			$settings['layout'] = $this->previous_theme_content[ $layout_control ];
		};
		$utilities->about_to_html( $settings );

		/**
		 * Move portfolio from cpt to jetpack's cpt.
		 */
		$utilities->update_portfolio( 'portfolio' );
		/**
		 * Sidebars.
		 */
		$utilities->update_sidebars();

		/**
		 * Features ribbon section.
		 */
		if ( ! empty( $this->previous_theme_content[ $prefix . 'features_ribbon_content' ] ) ) {
			$utilities->features_ribbon_to_html( $this->previous_theme_content[ $prefix . 'features_ribbon_content' ] );
		}

		/**
		 * Create Json for slider control in hestia
		 */
		$settings = array(
			'title'     => $prefix . 'header_title',
			'subtitle'  => $prefix . 'header_subtitle',
			'text'      => $prefix . 'header_button_text',
			'link'      => $prefix . 'header_button_link',
			'image_url' => 'header_image',
		);
		$utilities->update_big_title( $this->previous_theme_content, $settings );

		/**
		 * Full width layout
		 */
		if ( ! empty( $this->previous_theme_content[ $prefix . 'full_width_template' ] ) ) {
			$utilities->update_layout( $this->previous_theme_content[ $prefix . 'full_width_template' ] );
		}

		/**
		 * Update sections order
		 */
		$section_match  = array(
			'hestia_clients_bar'  => $prefix . 'logos_settings_section',
			'hestia_features'     => $prefix . 'services_section',
			'hestia_about'        => $prefix . 'about_section',
			'hestia_shop'         => $prefix . 'shop_section',
			'hestia_team'         => $prefix . 'team_section',
			'hestia_portfolio'    => $prefix . 'portfolio_section',
			'hestia_testimonials' => $prefix . 'testimonials_section',
			'hestia_ribbon'       => $prefix . 'ribbon_section',
			'hestia_blog'         => $prefix . 'latest_news_section',
			'hestia_contact'      => $prefix . 'contact_section',
		);
		$sections_order = ( ! empty( $this->previous_theme_content['sections_order'] ) ? $this->previous_theme_content['sections_order'] : '' );
		$utilities->update_sections_order( $sections_order, $section_match );

		/**
		 * Header layout
		 */
		$layout = $prefix === 'azera_shop_' ? 'layout2' : '';
		if ( ! empty( $this->previous_theme_content[ $prefix . 'header_layout' ] ) ) {
			$utilities->update_header_layout( $this->previous_theme_content[ $prefix . 'header_layout' ] );
		} elseif ( ! empty( $layout ) ) {
			$utilities->update_header_layout( $layout );
		}

		/* Menus */
		if ( ! empty( $this->previous_theme_content['nav_menu_locations'] ) ) {
			if ( $prefix === 'llorix_one_lite_' ) {
				$social_content = ! empty( $this->previous_theme_content['llorix_one_lite_very_top_social_icons'] ) ? $this->previous_theme_content['llorix_one_lite_very_top_social_icons'] : '';
			} else {
				$social_content = ! empty( $this->previous_theme_content[ $prefix . 'social_icons' ] ) ? $this->previous_theme_content[ $prefix . 'social_icons' ] : '';
			}
			$utilities->update_menus( $social_content, $this->previous_theme_content['nav_menu_locations'] );
		}

	}

	/**
	 * Sets all the simple theme mods provided in the parameter array.
	 *
	 * @param theme -mods $mods theme mods array.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private function set_simple_mods( $mods ) {
		// Prefix the theme mods with the previous active theme name and set them in Hestia.
		if ( ! empty( $mods ) ) {
			foreach ( $mods as $hestia_mod => $imported_mod ) {
				$this->set_hestia_mod( $hestia_mod, $imported_mod );
			}
		}
	}

	/**
	 * Utility method to set theme mod from import.
	 *
	 * @param  hestia-mod-id   $hestia_mod_id the hestia mod to set.
	 * @param  imported-mod-id $imported_mod_id the imported theme mod id.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private final function set_hestia_mod( $hestia_mod_id, $imported_mod_id ) {
		$hestia_mod = get_theme_mod( $hestia_mod_id );
		if ( ! empty( $this->previous_theme_content[ $imported_mod_id ] ) ) {
			$imported_mod = $this->previous_theme_content[ $imported_mod_id ];
			if ( ! empty( $imported_mod ) && empty( $hestia_mod ) ) {
				set_theme_mod( $hestia_mod_id, $imported_mod );
			}
		}
	}

	/**
	 * Do specific actions for Llorix theme.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private function llorix_specific_changes() {

		/* Set fonts */
		set_theme_mod( 'hestia_headings_font', 'Cabin' );
		set_theme_mod( 'hestia_body_font', 'Cabin' );

		/* Set default color */
		set_theme_mod( 'accent_color', '#be5000' );
		set_theme_mod( 'secondary_color', '#0d3c55' );
		if ( ! empty( $previous_theme_content['llorix_one_lite_title_color'] ) ) {
			set_theme_mod( 'body_color', $previous_theme_content['llorix_one_lite_title_color'] );
		}
		if ( ! empty( $previous_theme_content['llorix_one_lite_text_color'] ) ) {
			set_theme_mod( 'secondary_color', $previous_theme_content['llorix_one_lite_text_color'] );
		}

	}


	/**
	 * Do specific actions for Azera theme.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private function azera_specific_changes() {

		/* Set fonts */
		set_theme_mod( 'hestia_headings_font', 'Cabin' );
		set_theme_mod( 'hestia_body_font', 'Cabin' );

		/* Set default color */
		set_theme_mod( 'accent_color', '#FFA200' );

		/* Static front page settings */
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$pid = $this->create_frontpage();
			update_option( 'page_on_front', $pid );
		}
	}

	/**
	 * Do modifications for parallax that needs some logic.
	 *
	 * @access private
	 * @since  1.1.49
	 */
	private function parallax_specific_changes() {
		/* Set fonts */
		set_theme_mod( 'hestia_headings_font', 'Cabin' );
		set_theme_mod( 'hestia_body_font', 'Cabin' );

		/* Set default color */
		set_theme_mod( 'accent_color', '#008ed6' );

		/* Shop section visibility */
		if ( ! empty( $previous_theme_content['parallax_one_shop_section_show'] ) ) {
			set_theme_mod( 'hestia_shop_hide', (bool) $previous_theme_content['parallax_one_shop_section_show'] );
		} else {
			set_theme_mod( 'hestia_shop_hide', true );
		}

		/* Static front page settings */
		if ( 'posts' === get_option( 'show_on_front' ) ) {
			$pid = $this->create_frontpage();
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $pid );
		}
	}

	/**
	 * Create the frontpage from previous themes ( azera/llorix/paralax) and returns its id.
	 */
	private function create_frontpage() {
		$about_content = get_theme_mod( 'hestia_page_editor' );
		$page_content  = ! empty( $about_content ) ? $about_content : '';
		$page          = array(
			'post_type'    => 'page',
			'post_title'   => __( 'Front page', 'hestia-pro' ),
			'post_content' => wp_kses_post( $page_content ),
			'post_status'  => 'publish',
			'post_author'  => 1,
		);
		$pid           = wp_insert_post( $page );

		return $pid;
	}
}
