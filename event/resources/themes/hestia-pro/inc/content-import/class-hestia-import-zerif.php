<?php
/**
 * Class used to import zerif.
 *
 * @package hestia
 * @since   1.1.51
 */

/**
 * Class Hestia_Import_Zerif
 */
class Hestia_Import_Zerif extends Hestia_Import_Utilities {

	/**
	 * Previous theme slug
	 *
	 * @var mixed|string|void
	 */
	private $previous_theme = '';

	/**
	 * Previous theme content
	 *
	 * @var array
	 */
	private $previous_theme_content = array();

	/**
	 * Match customizer options between Zerif and Hestia
	 *
	 * @var array
	 */
	private $match_controls = array(
		'hestia_features_hide'         => 'zerif_ourfocus_show',
		'hestia_features_title'        => 'zerif_ourfocus_title',
		'hestia_features_subtitle'     => 'zerif_ourfocus_subtitle',
		'hestia_team_hide'             => 'zerif_ourteam_show',
		'hestia_team_title'            => 'zerif_ourteam_title',
		'hestia_team_subtitle'         => 'zerif_ourteam_subtitle',
		'hestia_testimonials_hide'     => 'zerif_testimonials_show',
		'hestia_testimonials_title'    => 'zerif_testimonials_title',
		'hestia_testimonials_subtitle' => 'zerif_testimonials_subtitle',
		'hestia_clients_bar_hide'      => 'zerif_aboutus_show',
		'hestia_pricing_title'         => 'zerif_packages_title',
		'hestia_pricing_subtitle'      => 'zerif_packages_subtitle',
		'custom_logo'                  => 'custom_logo',
		'hestia_portfolio_title'       => 'zerif_portofolio_title',
		'hestia_portfolio_subtitle'    => 'zerif_portofolio_subtitle',
		'hestia_portfolio_items'       => 'zerif_portofolio_number',
		'hestia_portfolio_hide'        => 'zerif_portofolio_show',
		'hestia_ribbon_text'           => 'zerif_ribbonright_text',
		'hestia_ribbon_button_text'    => 'zerif_ribbonright_buttonlabel',
		'hestia_ribbon_button_url'     => 'zerif_ribbonright_buttonlink',
		'hestia_contact_title'         => 'zerif_contactus_title',
		'hestia_contact_subtitle'      => 'zerif_contactus_subtitle',
		'hestia_general_credits'       => 'zerif_copyright',
		'hestia_blog_title'            => 'zerif_latestnews_title',
		'hestia_blog_subtitle'         => 'zerif_latestnews_subtitle',
		'hestia_parallax_layer1'       => 'zerif_parallax_img1',
		'hestia_parallax_layer2'       => 'zerif_parallax_img2',
	);

	/**
	 * Collect all content from zerif and add it to this variable.
	 *
	 * @var string
	 */
	private $about_content_markup;

	/**
	 * Hestia_Import_Zerif constructor.
	 *
	 * @access public
	 * @since  1.1.51
	 */
	public function __construct() {

		$this->about_content_markup = '';
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_id = get_theme_mod( 'page_on_front' );
			if ( ! empty( $page_id ) ) {
				$front_page = get_post( $page_id );
				if ( property_exists( $front_page, 'post_content' ) ) {
					$this->about_content_markup = $front_page->post_content;
				}
			}
		}

		// Get the name of the previously active theme.
		$this->previous_theme = strtolower( get_option( 'theme_switched' ) );

		// Get the theme mods from the previous theme.
		$this->previous_theme_content = get_option( 'theme_mods_' . $this->previous_theme );
	}

	/**
	 * Main import handler function.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	public final function import() {
		if ( ! in_array(
			$this->previous_theme,
			array(
				'zerif-pro',
				'zerif-lite',
				'zblackbeard',
				'responsiveboat',
			),
			true
		) ) {
			return;
		}

		/* Set fonts */
		set_theme_mod( 'hestia_headings_font', 'Montserrat' );
		set_theme_mod( 'hestia_body_font', 'Montserrat' );
		set_theme_mod( 'hestia_general_layout', false );

		/* Set default color */
		set_theme_mod( 'accent_color', '#e96656' );

		// Import about section.
		$this->import_zerif_about();

		// Import bottom button ribon to about
		$this->import_zerif_bottom_button_ribbon();

		if ( $this->previous_theme === 'zerif-pro' ) {
			/* Static front page settings */
			if ( 'posts' === get_option( 'show_on_front' ) ) {
				$about_content = $this->about_content_markup;
				$page_content  = ! empty( $about_content ) ? $about_content : '';
				$page          = array(
					'post_type'    => 'page',
					'post_title'   => 'Front page',
					'post_content' => wp_kses_post( $page_content ),
					'post_status'  => 'publish',
					'post_author'  => 1,
				);
				$pid           = wp_insert_post( $page );
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $pid );
			}
			$this->import_section_order();
		} else {
			$this->sync_content_from_control( $this->about_content_markup );
		}

		if ( $this->previous_theme === 'zerif-lite' ) {
			$this->match_controls['hestia_features_title'] = 'zerif_ourfocus_title_2';
		}

		// Set all mods in the $simple_theme_mods array.
		$this->set_simple_mods();

		$this->import_zerif_parallax();

		$this->import_zerif_header();

		$this->widgets_to_theme_mods();

		$this->import_zerif_packages();

		// Import portfolios
		require_once( HESTIA_PHP_INCLUDE . 'content-import/class-hestia-import-utilities.php' );
		$utilities = new Hestia_Import_Utilities();

		if ( empty( $this->previous_theme_content['zerif_portofolio_show'] ) || (bool) $this->previous_theme_content['zerif_portofolio_show'] === false ) {
			set_theme_mod( 'hestia_portfolio_hide', false );
		}
		$utilities->update_portfolio( 'portofolio' );

		// Show ribbon section if it's visible in zerif
		if ( ! empty( $this->previous_theme_content['zerif_ribbonright_text'] ) || ! empty( $this->previous_theme_content['zerif_ribbonright_buttonlabel'] ) ) {
			set_theme_mod( 'hestia_ribbon_hide', false );
		}

		// Import shortcode section
		if ( isset( $this->previous_theme_content['zerif_shortcodes_settings'] ) ) {
			$utilities->shortcodes_section_to_html( $this->previous_theme_content['zerif_shortcodes_settings'] );
		}
		// Import footer to contact section
		$this->import_zerif_footer();

		// Import footer social in footer menu
		$this->import_zerif_footer_socials();

		// Update sidebars
		$this->import_sidebars();

		// Import menus
		$this->import_menus();

	}

	/**
	 * Function to import About section.
	 *
	 * @since  1.1.51
	 * @access private
	 */
	private function import_zerif_about() {
		$execute = get_option( 'should_import_zerif_about' );
		if ( $execute !== false ) {
			return;
		}

		$css_to_add = '';

		/* Title and subtitle */
		$title    = array_key_exists( 'zerif_aboutus_title', $this->previous_theme_content ) && ! empty( $this->previous_theme_content['zerif_aboutus_title'] ) ? $this->previous_theme_content['zerif_aboutus_title'] : '';
		$subtitle = array_key_exists( 'zerif_aboutus_subtitle', $this->previous_theme_content ) && ! empty( $this->previous_theme_content['zerif_aboutus_subtitle'] ) ? $this->previous_theme_content['zerif_aboutus_subtitle'] : '';
		if ( ! empty( $title ) || ! empty( $subtitle ) ) {
			$this->about_content_markup .= '<div class="row"><div class="col-md-8 col-md-offset-2 text-center">';
			if ( ! empty( $title ) ) {
				$this->about_content_markup .= '<h2 class="hestia-title">' . wp_kses_post( $title ) . '</h2>';
			}
			if ( ! empty( $subtitle ) ) {
				$this->about_content_markup .= '<h5 class="description">' . wp_kses_post( $subtitle ) . '</h5>';
			}
			$this->about_content_markup .= '</div></div>';
		}

		/* Left content */
		$left_content = array_key_exists( 'zerif_aboutus_biglefttitle', $this->previous_theme_content ) && ! empty( $this->previous_theme_content['zerif_aboutus_biglefttitle'] ) ? $this->previous_theme_content['zerif_aboutus_biglefttitle'] : '';

		/* Center content */
		$center_content = array_key_exists( 'zerif_aboutus_text', $this->previous_theme_content ) && ! empty( $this->previous_theme_content['zerif_aboutus_text'] ) ? $this->previous_theme_content['zerif_aboutus_text'] : '';

		/* Right content */
		$right_content = '';
		for ( $i = 1; $i <= 4; $i ++ ) {
			$knob_title      = ! empty( $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_title' ] ) ? $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_title' ] : '';
			$knob_text       = ! empty( $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_text' ] ) ? $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_text' ] : '';
			$knob_percentage = ! empty( $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_nr' ] ) ? $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_nr' ] : '';
			$knob_color      = ! empty( $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_color' ] ) ? $this->previous_theme_content[ 'zerif_aboutus_feature' . $i . '_color' ] : '';
			if ( ! empty( $knob_percentage ) ) {
				if ( $knob_percentage <= 50 ) {
					$rotation    = (int) $knob_percentage * 3.6;
					$css_to_add .= '.progress' . $i . ' .progress-right .progress-bar { margin-right: 10px;border-color: ' . $knob_color . ';-webkit-transform: rotate(' . $rotation . 'deg); transform: rotate(' . $rotation . 'deg);}';
					$css_to_add .= '.progress' . $i . ' .progress-left .progress-bar { margin-right: 10px;border-color: ' . $knob_color . ';-webkit-transform: rotate(0deg); transform: rotate(0deg); }';
				} else {
					$rotation    = ( (int) $knob_percentage - 50 ) * 3.6;
					$css_to_add .= '.progress' . $i . ' .progress-left .progress-bar { margin-right: 10px;border-color: ' . $knob_color . ';-webkit-transform: rotate(' . $rotation . 'deg); transform: rotate(' . $rotation . 'deg);}';
					$css_to_add .= '.progress' . $i . ' .progress-right .progress-bar { margin-right: 10px;border-color: ' . $knob_color . ';-webkit-transform: rotate(180deg); transform: rotate(180deg);}';
				}
				$right_content .= '
					<div class="row">
						<div class="progress progress' . esc_attr( $i ) . '">
		                <span class="progress-left">
		                    <span class="progress-bar"></span>
		                </span>
						<span class="progress-right">
		                    <span class="progress-bar"></span>
		                </span>
						<div class="progress-value">' . wp_kses_post( $knob_percentage ) . '%</div>
						</div>
						<h6 class="category">' . wp_kses_post( $knob_title ) . '</h6>
						<p>' . wp_kses_post( $knob_text ) . '</p>
					</div>';
			}
		}

		/* About section in zerif have 3 columns. If one column is empty, divide the section in two*/
		$content          = array( $left_content, $center_content, $right_content );
		$not_empty_colums = count( array_filter( $content ) );
		if ( ! function_exists( 'wp_update_custom_css_post' ) ) {
			$not_empty_colums --;
		}
		if ( $not_empty_colums <= 0 ) {
			return;
		}

		/* Get bootstrap class name for columns */
		$nb    = 12 / $not_empty_colums;
		$class = 'col-md-' . $nb;

		/* Add section content */
		$this->about_content_markup .= '<div class="row">';
		if ( ! empty( $left_content ) ) {
			$this->about_content_markup .= '<div class="' . esc_attr( $class ) . ' text-right">';
			$this->about_content_markup .= '<h3>' . wp_kses_post( $left_content ) . '</h3>';
			$this->about_content_markup .= '</div>';
		}
		if ( ! empty( $center_content ) ) {
			$this->about_content_markup .= '<div class="' . esc_attr( $class ) . '">';
			$this->about_content_markup .= '<p>' . wp_kses_post( $center_content ) . '</p>';
			$this->about_content_markup .= '</div>';
		}
		if ( ! empty( $right_content ) && function_exists( 'wp_update_custom_css_post' ) ) {
			/* This is the css for knobs */
			$css_to_add .= '
			.progress{
				width: 70px;
				height: 70px;
				line-height: 70px;
				margin: 0 auto;
				position: relative;
				display: inline-block;
				float: left;
				margin-right: 15px;
				margin-top: 15px;
			}
			.progress > span{
				width: 50%;
				height: 100%;
				overflow: hidden;
				position: absolute;
				top: 0;
				z-index: 1;
			}
			.progress .progress-left{
				left: 0;
			}
			.progress .progress-bar{
				width: 100%;
				height: 100%;
				background: none;
				border-width: 8px;
				border-style: solid;
				position: absolute;
				top: 0;
			}
			.progress .progress-left .progress-bar{
				left: 100%;
				border-top-right-radius: 80px;
				border-bottom-right-radius: 80px;
				border-left: 0;
				-webkit-transform-origin: center left;
				transform-origin: center left;
			}
			.progress .progress-right{
				right: 0;
			}
			.progress .progress-right .progress-bar{
				left: -100%;
				border-top-left-radius: 80px;
				border-bottom-left-radius: 80px;
				border-right: 0;
				-webkit-transform-origin: center right;
				transform-origin: center right;
			}
			.progress .progress-value{
				width: 54px;
				height: 54px;
				border-radius: 50%;
				background: #44484b;
				font-size: 13px;
				color: #fff;
				line-height: 54px;
				text-align: center;
				position: absolute;
				top: 8px;
				left: 8px;
			}
			.progress.blue .progress-bar{
				border-color: #049dff;
			}';
			wp_update_custom_css_post( $css_to_add );
			$this->about_content_markup .= '<div class="' . esc_attr( $class ) . '">';
			$this->about_content_markup .= wp_kses_post( $right_content );
			$this->about_content_markup .= '</div>';
		}
		$this->about_content_markup .= '</div>';

		update_option( 'should_import_zerif_about', true );
	}

	/**
	 * Import Bottom Button ribbon to Hestia's about section.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_zerif_bottom_button_ribbon() {

		$execute = get_option( 'should_import_zerif_ribbon' );
		if ( $execute !== false ) {
			return;
		}

		$title        = ! empty( $this->previous_theme_content['zerif_bottomribbon_text'] ) ? $this->previous_theme_content['zerif_bottomribbon_text'] : '';
		$button_label = ! empty( $this->previous_theme_content['zerif_bottomribbon_buttonlabel'] ) ? $this->previous_theme_content['zerif_bottomribbon_buttonlabel'] : '';
		$button_link  = ! empty( $this->previous_theme_content['zerif_bottomribbon_buttonlink'] ) ? $this->previous_theme_content['zerif_bottomribbon_buttonlink'] : '';
		if ( empty( $title ) && empty( $button_label ) ) {
			return;
		}

		$section_content = '<div class="row"><div class="col-md-12 text-center">';
		if ( ! empty( $title ) ) {
			$section_content .= '<h2 class="hestia-title">' . wp_kses_post( $title ) . '</h2>';
		}
		if ( ! empty( $button_label ) && ! empty( $button_link ) ) {
			$section_content .= '<a href="' . esc_url( $button_link ) . '" class="btn btn-primary btn-lg">' . wp_kses_post( $button_label ) . '</a>';
		}
		$section_content .= '</div></div>';

		$this->about_content_markup .= $section_content;

		update_option( 'should_import_zerif_ribbon', true );
	}

	/**
	 * Import sections order in hestia.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_section_order() {
		$result_order    = array(
			'hestia_features'                   => 10,
			'hestia_ribbon'                     => 15,
			'hestia_portfolio'                  => 20,
			'hestia_about'                      => 25,
			'hestia_clients_bar'                => 30,
			'hestia_team'                       => 35,
			'hestia_testimonials'               => 40,
			'hestia_contact'                    => 45,
			'hestia_pricing'                    => 50,
			'sidebar-widgets-subscribe-widgets' => 55,
			'hestia_shop'                       => 60,
			'hestia_blog'                       => 65,
		);
		$section_mapping = array(
			'our_focus'    => 'hestia_features',
			'portofolio'   => 'hestia_portfolio',
			'about_us'     => 'hestia_about',
			'our_team'     => 'hestia_team',
			'testimonials' => 'hestia_testimonials',
			'right_ribbon' => 'hestia_ribbon',
			'contact_us'   => 'hestia_contact',
			'packages'     => 'hestia_pricing',
			'subscribe'    => 'sidebar-widgets-subscribe-widgets',
			'latest_news'  => 'hestia_blog',
		);
		for ( $i = 1; $i <= 13; $i ++ ) {
			if ( ! empty( $this->previous_theme_content[ 'section' . $i ] ) ) {
				if ( array_key_exists( $this->previous_theme_content[ 'section' . $i ], $section_mapping ) ) {
					$hestia_section                  = $section_mapping[ $this->previous_theme_content[ 'section' . $i ] ];
					$result_order[ $hestia_section ] = ( $i * 5 ) + 5;
				}
			}
		}

		if ( empty( $sections_order ) ) {
			set_theme_mod( 'sections_order', json_encode( $result_order ) );
		}
	}

	/**
	 * Sets all the simple theme mods provided in the parameter array.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function set_simple_mods() {
		$mods = $this->match_controls;
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
	 * @since  1.1.51
	 */
	private final function set_hestia_mod( $hestia_mod_id, $imported_mod_id ) {
		$hestia_mod = get_theme_mod( $hestia_mod_id );
		if ( array_key_exists( $imported_mod_id, $this->previous_theme_content ) ) {
			$imported_mod = $this->previous_theme_content[ $imported_mod_id ];
			if ( ! empty( $imported_mod ) && empty( $hestia_mod ) ) {
				set_theme_mod( $hestia_mod_id, $imported_mod );
			}
		}
	}

	/**
	 * Import parallax from zerif to hestia.
	 */
	private function import_zerif_parallax() {
		if ( ! array_key_exists( 'zerif_parallax_show', $this->previous_theme_content ) ) {
			return;
		}
		$zerif_parallax_use = $this->previous_theme_content['zerif_parallax_show'];
		if ( ! empty( $zerif_parallax_use ) && ( (bool) $zerif_parallax_use === true ) ) {
			set_theme_mod( 'hestia_slider_type', 'parallax' );
		}

	}

	/**
	 * Create Json for slider control in hestia
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_zerif_header() {
		// This is the main structure of a slide. In zerif all slides have same content but different background.
		$main_slide = array();
		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_title'] ) ) {
			$main_slide['title'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_title'] );
		} elseif ( ! empty( $this->previous_theme_content['zerif_bigtitle_title_2'] ) ) {
			$main_slide['title'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_title_2'] );
		}

		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_redbutton_label'] ) ) {
			$main_slide['text'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_redbutton_label'] );
		} elseif ( ! empty( $this->previous_theme_content['zerif_bigtitle_redbutton_label_2'] ) ) {
			$main_slide['text'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_redbutton_label_2'] );
		}

		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_redbutton_url'] ) ) {
			$main_slide['link'] = esc_url( $this->previous_theme_content['zerif_bigtitle_redbutton_url'] );
		}
		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_greenbutton_label'] ) ) {
			$main_slide['text2'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_greenbutton_label'] );
		}
		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_greenbutton_url'] ) ) {
			$main_slide['link2'] = esc_url( $this->previous_theme_content['zerif_bigtitle_greenbutton_url'] );
		}
		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_1button_background_color'] ) ) {
			$main_slide['color'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_1button_background_color'] );
		} else {
			$main_slide['color'] = '#e96656';
		}
		if ( ! empty( $this->previous_theme_content['zerif_bigtitle_2button_background_color'] ) ) {
			$main_slide['color2'] = wp_kses_post( $this->previous_theme_content['zerif_bigtitle_2button_background_color'] );
		} else {
			$main_slide['color2'] = '#1e9e6b';
		}

		$background_settings = ! empty( $this->previous_theme_content['zerif_background_settings'] ) ? $this->previous_theme_content['zerif_background_settings'] : '';

		if ( ! empty( $background_settings ) && $background_settings === 'zerif-background-slider' ) {
			$settings = array();
			for ( $i = 1; $i <= 3; $i ++ ) {
				if ( array_key_exists( 'zerif_bgslider_' . $i, $this->previous_theme_content ) ) {
					$bg = $this->previous_theme_content[ 'zerif_bgslider_' . $i ];
					if ( ! empty( $bg ) ) {
						$slide              = $main_slide;
						$slide['image_url'] = esc_url( $bg );
						array_push( $settings, $slide );
					}
				}
				set_theme_mod( 'hestia_slider_type', 'image' );
			}
			$section_is_empty = empty( $main_slide['title'] ) && empty( $main_slide['text'] ) && empty( $main_slide['text2'] ) && empty( $background_settings );
			if ( ! $section_is_empty ) {
				set_theme_mod( 'hestia_slider_content', json_encode( $settings ) );
			}
		} elseif ( $background_settings !== 'zerif-background-video' ) {
			if ( ! empty( $this->previous_theme_content['background_image'] ) ) {
				$main_slide['image_url'] = esc_url( $this->previous_theme_content['background_image'] );
			}
			$section_is_empty = empty( $main_slide['title'] ) && empty( $main_slide['text'] ) && empty( $main_slide['text2'] ) && empty( $main_slide['image_url'] );
			if ( ! $section_is_empty ) {
				set_theme_mod( 'hestia_slider_content', '[' . json_encode( $main_slide ) . ']' );
			}

			if ( ! array_key_exists( 'zerif_parallax_show', $this->previous_theme_content ) ) {
				return;
			}
			$zerif_parallax_use = $this->previous_theme_content['zerif_parallax_show'];
			if ( ! empty( $zerif_parallax_use ) && ( (bool) $zerif_parallax_use === true ) ) {
				set_theme_mod( 'hestia_slider_type', 'parallax' );
			} else {
				set_theme_mod( 'hestia_slider_type', 'image' );
			}
		}
	}

	/**
	 * Transfer widgets from Our focus, Testimonials, Team and About us to theme mods in hestia
	 *
	 * @since  1.1.51
	 * @access private
	 */
	private function widgets_to_theme_mods() {

		$sidebars = array(
			'hestia_features_content'     => array( 'sidebar-ourfocus', 'ctup-ads' ),
			'hestia_testimonials_content' => array( 'sidebar-testimonials', 'zerif_testim' ),
			'hestia_clients_bar_content'  => array( 'sidebar-aboutus', 'zerif_clients' ),
			'hestia_team_content'         => array( 'sidebar-ourteam', 'zerif_team' ),
		);

		foreach ( $sidebars as $hestia_corespondent => $sidebar_settings ) {
			$hestia_content         = get_theme_mod( $hestia_corespondent );
			$hestia_content_decoded = json_decode( $hestia_content );
			if ( empty( $hestia_content_decoded ) ) {
				$content = $this->get_sidebar_content( $sidebar_settings[0], $sidebar_settings[1] );
				if ( ! empty( $content ) ) {
					set_theme_mod( $hestia_corespondent, $content );
				}
			}
		}
	}

	/**
	 * Returns the content from Our focus, Testimonials, Team and About in json format
	 *
	 * @param string $sidebar Sidebar name.
	 * @param string $prefix  Prefix of widgets in that sidebar.
	 *
	 * @since  1.1.51
	 * @access private
	 * @return array|string
	 */
	private function get_sidebar_content( $sidebar, $prefix ) {
		$sidebars              = get_option( 'sidebars_widgets' );
		$data_in_hestia_format = array();
		if ( array_key_exists( $sidebar, $sidebars ) ) {
			$widget_ids = $sidebars[ $sidebar ];
			if ( empty( $widget_ids ) ) {
				return '';
			}
			$ids_to_grab = array();
			foreach ( $widget_ids as $widget_id ) {
				if ( strpos( $widget_id, $prefix ) !== false ) {
					$short_id_transient = explode( '-', $widget_id );
					$short_id           = end( $short_id_transient );
					array_push( $ids_to_grab, $short_id );
				}
			}
			$all_widgets = get_option( 'widget_' . $prefix . '-widget' );
			foreach ( $ids_to_grab as $key ) {
				$widget_data = array();
				if ( array_key_exists( $key, $all_widgets ) ) {
					$current_widget = $all_widgets[ $key ];
					if ( ! empty( $current_widget ) ) {
						$social_repeater = array();
						foreach ( $current_widget as $key => $value ) {
							$repeater_key = $this->get_key( $key );
							if ( ! empty( $value ) && ! empty( $repeater_key ) ) {
								if ( $repeater_key === 'social_repeater' ) {
									$social = $this->get_repeater_social( $key, $value );
									array_push( $social_repeater, $social );
								} else {
									$widget_data[ $repeater_key ] = $value;
								}
							}
						}
						$widget_data['social_repeater'] = json_encode( $social_repeater );
						$widget_data['choice']          = 'customizer_repeater_image';
					}
				}
				if ( ! empty( $widget_data ) ) {
					array_push( $data_in_hestia_format, $widget_data );
				}
			}
		}

		return json_encode( $data_in_hestia_format );
	}

	/**
	 * Map widgets inputs names to repeater inputs
	 *
	 * @param string $key Name of the inputs.
	 *
	 * @since  1.1.51
	 * @access private
	 * @return bool|string
	 */
	private function get_key( $key ) {
		$repeater_map = array(
			'image_url'       => array( 'image_url', 'image_uri' ),
			'title'           => array( 'title', 'name' ),
			'subtitle'        => array( 'subtitle', 'position', 'details' ),
			'text'            => array( 'text', 'description' ),
			'link'            => array( 'link' ),
			'social_repeater' => array(
				'fb_link',
				'tw_link',
				'bh_link',
				'db_link',
				'ln_link',
				'gp_link',
				'pinterest_link',
				'tumblr_link',
				'reddit_link',
				'youtube_link',
				'instagram_link',
				'website_link',
				'email_link',
				'phone_link',
				'profile_link',
			),
		);
		foreach ( $repeater_map as $k => $v ) {
			if ( in_array( $key, $v, true ) ) {
				return $k;
			}
		}

		return false;
	}

	/**
	 * Return content to add to social repeater. Used for team members.
	 *
	 * @param string $social_name Name of social link.
	 * @param string $value       Link of social.
	 *
	 * @since  1.1.51
	 * @access private
	 * @return array
	 */
	private function get_repeater_social( $social_name, $value ) {
		$result = array(
			'icon' => '',
			'link' => $value,
		);
		switch ( $social_name ) {
			case 'fb_link':
				$result['icon'] = 'fa-facebook';
				break;
			case 'tw_link':
				$result['icon'] = 'fa-twitter';
				break;
			case 'bh_link':
				$result['icon'] = 'fa-behance';
				break;
			case 'db_link':
				$result['icon'] = 'fa-dribbble';
				break;
			case 'ln_link':
				$result['icon'] = 'fa-linkedin';
				break;
			case 'gp_link':
				$result['icon'] = 'fa-google-plus';
				break;
			case 'pinterest_link':
				$result['icon'] = 'fa-pinterest-p';
				break;
			case 'tumblr_link':
				$result['icon'] = 'fa-tumblr';
				break;
			case 'reddit_link':
				$result['icon'] = 'fa-reddit-alien';
				break;
			case 'youtube_link':
				$result['icon'] = 'fa-youtube';
				break;
			case 'instagram_link':
				$result['icon'] = 'fa-instagram';
				break;
			case 'website_link':
				$result['icon'] = 'fa-globe';
				break;
			case 'email_link':
				$result['icon'] = 'fa-envelope';
				break;
			case 'phone_link':
				$result['icon'] = 'fa-phone';
				break;
			case 'profile_link':
				$result['icon'] = 'fa-user';
				break;
		}

		return $result;
	}

	/**
	 * Function to import Packages section.
	 * Because in hestia are only two tables, we only import two widgets from packages in this section.
	 *
	 * @since  1.1.51
	 * @access private
	 */
	private function import_zerif_packages() {

		if ( ! isset( $this->previous_theme_content['zerif_packages_show'] ) ) {
			$display_packages = false;
		} else {
			$display_packages = get_theme_mod( $this->previous_theme_content['zerif_packages_show'] );
		}

		$hestia_pricing_hide = get_theme_mod( 'hestia_pricing_hide' );
		if ( ( ! empty( $display_packages ) && $display_packages === false ) || $hestia_pricing_hide === true ) {
			return;
		}

		$sidebars = get_option( 'sidebars_widgets' );
		if ( ! array_key_exists( 'sidebar-packages', $sidebars ) ) {
			return;
		}

		set_theme_mod( 'hestia_pricing_hide', false );

		/* Get two widgets ids from this section */
		$widget_ids = $sidebars['sidebar-packages'];
		if ( empty( $widget_ids ) ) {
			return;
		}
		$ids_to_grab = array();
		$items       = 2;
		foreach ( $widget_ids as $widget_id ) {
			if ( strpos( $widget_id, 'color-picker' ) !== false && $items > 0 ) {
				$short_id_transient = explode( '-', $widget_id );
				$short_id           = end( $short_id_transient );
				array_push( $ids_to_grab, $short_id );
				$items --;
			}
		}

		/* Get all widgets from packages section and import just the ones that have one of those ids that we've selected earlier */
		$all_widgets = get_option( 'widget_color-picker' );

		if ( ! empty( $ids_to_grab[0] ) && array_key_exists( $ids_to_grab[0], $all_widgets ) ) {
			$current_widget = $all_widgets[ $ids_to_grab[0] ];
			$this->import_package( $current_widget, 'one' );
		}

		if ( ! empty( $ids_to_grab[1] ) && array_key_exists( $ids_to_grab[1], $all_widgets ) ) {
			$current_widget = $all_widgets[ $ids_to_grab[1] ];
			$this->import_package( $current_widget, 'two' );
		}
	}

	/**
	 * Update theme mods from hestia based on content from zerif package widget.
	 *
	 * @param array  $content Content from zerif's widget.
	 * @param string $table   Destination table for the widget.
	 *
	 * @since  1.1.51
	 * @access private
	 */
	private function import_package( $content, $table ) {

		if ( ! in_array( $table, array( 'one', 'two' ), true ) || empty( $content ) ) {
			return;
		}

		$pricing_table_title    = get_theme_mod( 'hestia_pricing_table_' . $table . '_title' );
		$pricing_table_price    = get_theme_mod( 'hestia_pricing_table_' . $table . '_price' );
		$pricing_table_features = get_theme_mod( 'hestia_pricing_table_' . $table . '_features' );
		$pricing_table_link     = get_theme_mod( 'hestia_pricing_table_' . $table . '_link' );
		$pricing_table_text     = get_theme_mod( 'hestia_pricing_table_' . $table . '_text' );
		$table_is_empty         = empty( $pricing_table_title ) && empty( $pricing_table_price ) && empty( $pricing_table_features ) && empty( $pricing_table_link ) && empty( $pricing_table_text ) && empty( $table_is_empty );

		if ( ! $table_is_empty ) {
			return;
		}

		if ( ! empty( $content['title'] ) ) {
			set_theme_mod( 'hestia_pricing_table_' . $table . '_title', $content['title'] );
		}

		$price = '';
		if ( ! empty( $content['price'] ) ) {
			if ( ! empty( $content['currency'] ) ) {
				$price .= '<small>' . $content['currency'] . '</small>';
			}
			$price .= $content['price'];
			if ( ! empty( $content['price_meta'] ) ) {
				$price .= '<small>' . $content['price_meta'] . '</small>';
			}
		}
		if ( ! empty( $price ) ) {
			set_theme_mod( 'hestia_pricing_table_' . $table . '_price', $price );
		}

		if ( ! empty( $content['button_link'] ) ) {
			set_theme_mod( 'hestia_pricing_table_' . $table . '_link', $content['button_link'] );
		}

		if ( ! empty( $content['button_label'] ) ) {
			set_theme_mod( 'hestia_pricing_table_' . $table . '_text', $content['button_label'] );
		}

		/**
		 * Zerif's package widget have 10 possible items in a package. If an item isn't empty we need to concatenate
		 * it and to add \n character to tell the control in hestia that it's a new item.
		 */
		$features = '';
		for ( $i = 1; $i <= 10; $i ++ ) {
			if ( ! empty( $content[ 'item' . $i ] ) ) {
				$features .= $content[ 'item' . $i ] . '\n';
			}
		}
		if ( ! empty( $features ) ) {
			set_theme_mod( 'hestia_pricing_table_' . $table . '_features', $features );
		}
	}

	/**
	 * Import content from footer to contact section.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_zerif_footer() {
		$icon1           = ! empty( $this->previous_theme_content['zerif_email_icon'] ) ? $this->previous_theme_content['zerif_email_icon'] : '';
		$text1           = ! empty( $this->previous_theme_content['zerif_email'] ) ? $this->previous_theme_content['zerif_email'] : '';
		$icon2           = ! empty( $this->previous_theme_content['zerif_phone_icon'] ) ? $this->previous_theme_content['zerif_phone_icon'] : '';
		$text2           = ! empty( $this->previous_theme_content['zerif_phone'] ) ? $this->previous_theme_content['zerif_phone'] : '';
		$icon3           = ! empty( $this->previous_theme_content['zerif_address_icon'] ) ? $this->previous_theme_content['zerif_address_icon'] : '';
		$text3           = ! empty( $this->previous_theme_content['zerif_address'] ) ? $this->previous_theme_content['zerif_address'] : '';
		$section_content = array(
			$icon1 => $text1,
			$icon2 => $text2,
			$icon3 => $text3,
		);
		if ( ! empty( $section_content ) ) {
			$contact_html = '';
			foreach ( $section_content as $icon => $text ) {
				$contact_html .= '<div class="info info-horizontal">';
				if ( ! empty( $icon ) ) {
					$contact_html .= '<div class="icon icon-primary"><img src="' . esc_url( $icon ) . '"></div>';
				}
				if ( ! empty( $text ) ) {
					$contact_html .= '<h4 class="info-title">' . wp_kses_post( $text ) . '</h4>';
				}
				$contact_html .= '</div>';
			}
			$contact_content = get_theme_mod( 'hestia_contact_content_new' );
			if ( empty( $contact_content ) ) {
				set_theme_mod( 'hestia_contact_content_new', $contact_html );
			}
		}
	}

	/**
	 * Import footer socials into hestia footer menu.
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_zerif_footer_socials() {
		$zerif_socials = array(
			'zerif_socials_facebook',
			'zerif_socials_twitter',
			'zerif_socials_linkedin',
			'zerif_socials_behance',
			'zerif_socials_dribbble',
			'zerif_socials_googleplus',
			'zerif_socials_pinterest',
			'zerif_socials_tumblr',
			'zerif_socials_reddit',
			'zerif_socials_youtube',
			'zerif_socials_instagram',
		);

		$theme_navs = get_theme_mod( 'nav_menu_locations' );
		if ( empty( $theme_navs['footer'] ) ) {

			$menu_name   = __( 'Footer socials menu', 'hestia-pro' );
			$menu_exists = wp_get_nav_menu_object( $menu_name );
			if ( ! $menu_exists ) {
				$menu_id = wp_create_nav_menu( $menu_name );

				foreach ( $zerif_socials as $social ) {
					if ( ! empty( $this->previous_theme_content[ $social ] ) ) {
						wp_update_nav_menu_item(
							$menu_id,
							0,
							array(
								'menu-item-title'  => __( 'Custom Page', 'hestia-pro' ),
								'menu-item-url'    => $this->previous_theme_content[ $social ],
								'menu-item-status' => 'publish',
							)
						);
					}
				}

				$theme_navs['footer'] = $menu_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $theme_navs );
	}

	/**
	 * Move widgets from old sidebars to hestia's sidebars
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_sidebars() {
		$widgets_from_old_theme = wp_get_sidebars_widgets();
		$new_widget_array       = array();

		if ( ! empty( $widgets_from_old_theme['sidebar-1'] ) ) {
			$new_widget_array['sidebar-1'] = $widgets_from_old_theme['sidebar-1'];
		}

		if ( ! empty( $widgets_from_old_theme['zerif-sidebar-footer'] ) ) {
			$new_widget_array['footer-one-widgets'] = $widgets_from_old_theme['zerif-sidebar-footer'];
		}

		if ( ! empty( $widgets_from_old_theme['zerif-sidebar-footer-2'] ) ) {
			$new_widget_array['footer-two-widgets'] = $widgets_from_old_theme['zerif-sidebar-footer-2'];
		}

		if ( ! empty( $widgets_from_old_theme['zerif-sidebar-footer-3'] ) ) {
			$new_widget_array['footer-three-widgets'] = $widgets_from_old_theme['zerif-sidebar-footer-3'];
		}
		if ( ! isset( $new_widget_array['wp_inactive_widgets'] ) ) {
			$new_widget_array['wp_inactive_widgets'] = array();
		}

		update_option( 'sidebars_widgets', $new_widget_array );
	}

	/**
	 * Import menus from zerif
	 *
	 * @access private
	 * @since  1.1.51
	 */
	private function import_menus() {
		$theme_navs = get_theme_mod( 'nav_menu_locations' );
		if ( empty( $theme_navs['primary'] ) && ! empty( $nav_locations['primary'] ) ) {
			$theme_navs['primary'] = $nav_locations['primary'];
		}
		set_theme_mod( 'nav_menu_locations', $theme_navs );
	}

}
