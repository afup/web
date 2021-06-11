<?php
/**
 * Utilities methods used to import all three themes, Azera, Llorix, Parallax
 *
 * @package hestia
 * @since 1.1.49
 */

/**
 * Class Import_Utilities
 *
 * @access public
 * @since 1.1.49
 */
class Hestia_Import_Utilities {

	/**
	 * This function updates logo in hestia.
	 * In hestia logo control is returning attachment id while in A, P, L the logo is an url
	 *
	 * Note: A, P, L is short version of Azera, Parallax, Llorix.
	 *
	 * @param string $previous_theme_content Previous content.
	 *
	 * @access public
	 * @since 1.1.49
	 */
	public function update_logo( $previous_theme_content ) {
		// Don't set any logo if it's already set in hestia
		$current_logo = get_theme_mod( 'custom_logo' );
		if ( ! empty( $current_logo ) ) {
			return;
		}

		// Exit if there is no logo in A / P / L
		$logo_value = $previous_theme_content;
		if ( empty( $logo_value ) ) {
			return;
		}
		$logo_attachement_id = $this->get_attachment_id( $logo_value );
		if ( ! empty( $logo_attachement_id ) ) {
			set_theme_mod( 'custom_logo', $logo_attachement_id );
		}
	}

	/**
	 * Returns attachement id from an url.
	 *
	 * @param string $url Attachement url.
	 * @access public
	 * @since 1.1.49
	 *
	 * @return int|mixed
	 */
	private function get_attachment_id( $url ) {
		$attachment_id = 0;
		$dir           = wp_upload_dir();
		if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
			$file       = basename( $url );
			$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'value'   => $file,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				),
			);
			$query      = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post_id ) {
					$meta                = wp_get_attachment_metadata( $post_id );
					$original_file       = basename( $meta['file'] );
					$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
					if ( $original_file === $file || in_array( $file, $cropped_image_files, true ) ) {
						$attachment_id = $post_id;
						break;
					}
				}
				wp_reset_postdata();
			}
		}
		return $attachment_id;
	}

	/**
	 * Update sections order.
	 *
	 * @param string $previous_theme_content All settings from previous theme.
	 * @param array  $section_match Matching sections from previous theme and hestia.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_sections_order( $previous_theme_content, $section_match ) {
		$result_order = array(
			'hestia_clients_bar'                => 10,
			'hestia_features'                   => 15,
			'hestia_about'                      => 20,
			'hestia_shop'                       => 25,
			'hestia_team'                       => 30,
			'hestia_testimonials'               => 35,
			'hestia_ribbon'                     => 40,
			'hestia_blog'                       => 45,
			'hestia_contact'                    => 50,
			'hestia_pricing'                    => 55,
			'sidebar-widgets-subscribe-widgets' => 60,
		);

		if ( empty( $previous_theme_content ) ) {
			set_theme_mod( 'sections_order', json_encode( $result_order ) );
			return;
		}

		// This means that it's a pro version of the theme we want to import
		$prev_oreder = json_decode( $previous_theme_content );
		if ( ! empty( $prev_oreder ) ) {
			foreach ( $section_match as $hestia_section => $imported_sction ) {
				$result_order[ $hestia_section ] = $prev_oreder->$imported_sction;
			}
		}
		set_theme_mod( 'sections_order', json_encode( $result_order ) );
	}


	/**
	 * Create Json for slider control in hestia.
	 *
	 * @param array $previous_theme_content All settings from previous theme.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_big_title( $previous_theme_content, $settings ) {
		$hestia_slider_content = get_theme_mod( 'hestia_slider_content' );
		if ( ! empty( $hestia_slider_content ) ) {
			return;
		}

		$result = array();
		foreach ( $settings as $item => $name ) {
			if ( ! empty( $previous_theme_content[ $name ] ) ) {
				$result[ $item ] = $previous_theme_content[ $name ];
			}
		}

		if ( ! empty( $result ) ) {
			set_theme_mod( 'hestia_slider_content', '[' . json_encode( $result ) . ']' );
		}

	}

	/**
	 * Create html from about section add add it to about section content in hestia.
	 *
	 * @param array $about_content About content.
	 * @access public
	 * @since 1.1.49
	 */
	public function about_to_html( $about_content ) {

		$title  = ! empty( $about_content['title'] ) ? $about_content['title'] : '';
		$text   = ! empty( $about_content['text'] ) ? $about_content['text'] : '';
		$image  = ! empty( $about_content['image'] ) ? $about_content['image'] : '';
		$layout = ! empty( $about_content['layout'] ) ? $about_content['layout'] : '';

		$page_editor = get_theme_mod( 'hestia_page_editor' );
		$about_html  = ( ! empty( $page_editor ) ? $page_editor : '' );
		if ( ! empty( $title ) ) {
			$about_html .= '<h2>' . wp_kses_post( $title ) . '</h2>';
		}
		if ( ! empty( $text ) || ! empty( $image ) ) {

			$class_to_add = ( empty( $image ) ? 'col-md-12' : 'col-md-8' );
			$about_html  .= '<div class="row">';
			if ( $layout === 'about_layout2' ) {
				if ( ! empty( $image ) ) {
					$about_html .= '<div class="col-md-4 col-xs-12"><img src="' . esc_url( $image ) . '"/></div>';
				}
			}

			$about_html .= '<div class="' . esc_attr( $class_to_add ) . ' col-xs-12">';
			if ( ! empty( $text ) ) {
				$about_html .= wp_kses_post( $text );
			}
			$about_html .= '</div>';

			if ( $layout !== 'about_layout2' ) {
				if ( ! empty( $image ) ) {
					$about_html .= '<div class="col-md-4 col-xs-12"><img src="' . esc_url( $image ) . '"/></div>';
				}
			}

			$about_html .= '</div>';
		}

		if ( ! empty( $about_html ) ) {
			set_theme_mod( 'hestia_page_editor', $about_html );
			$this->sync_content_from_control( $about_html );
		}
	}

	/**
	 * Create html from contact section add add it to about section content in hestia.
	 *
	 * @param string $contact_content Section content.
	 * @access public
	 * @since 1.1.49
	 */
	public function contact_to_html( $contact_content ) {
		if ( empty( $contact_content ) ) {
			return;
		}
		$content = $this->update_icons( $contact_content );
		if ( ! empty( $content ) ) {
			$data = json_decode( $content, true );
			if ( ! empty( $data ) ) {
				$contact_html = '';
				foreach ( $data as $content_block ) {
					$contact_html .= '<div class="info info-horizontal">';
					if ( ! empty( $content_block['icon_value'] ) ) {
						$contact_html .= '<div class="icon icon-primary"><i class="fas ' . $content_block['icon_value'] . '"></i></div>';
					}
					if ( ! empty( $content_block['text'] ) ) {
						$contact_html .= '<div class="description">';
						if ( ! empty( $content_block['link'] ) ) {
							$contact_html .= '<a href="' . esc_url( $content_block['link'] ) . '">';
						}
						$contact_html .= '<h4 class="info-title">' . wp_kses_post( $content_block['text'] ) . '</h4>';
						if ( ! empty( $content_block['link'] ) ) {
							$contact_html .= '</a>';
						}
						$contact_html .= '</div>';
					}
					$contact_html .= '</div>';
				}

				if ( ! empty( $contact_html ) ) {
					set_theme_mod( 'hestia_contact_content_new', $contact_html );
				}
			}
		}

	}

	/**
	 * Parallax theme has stamp icons and font awesome while hestia has only font awesome. If a stamp icon is used,
	 * replace it with an icon form font awesome
	 *
	 * @param string $json Repeater content in json format.
	 * @access public
	 * @since 1.1.49
	 *
	 * @return string
	 */
	public function update_icons( $json ) {
		if ( empty( $json ) ) {
			return '';
		}

		$data = json_decode( $json, true );
		if ( ! empty( $data ) ) {
			foreach ( $data as $item => $values ) {
				if ( ! empty( $values['icon_value'] ) && strpos( $values['icon_value'], 'icon-' ) !== false ) {
					$data[ $item ]['icon_value'] = 'fa-circle-o';
				}
				if ( ! empty( $values['choice'] ) ) {
					if ( strpos( $values['choice'], '_icon' ) !== false ) {
						$data[ $item ]['choice'] = 'customizer_repeater_icon';
					}
					if ( strpos( $values['choice'], '_image' ) !== false ) {
						$data[ $item ]['choice'] = 'customizer_repeater_image';
					}
				}
				$color = get_theme_mod( 'accent_color' );
				if ( ! empty( $color ) ) {
					$data[ $item ]['color'] = $color;
				}
			}
		}
		return json_encode( $data );
	}

	/**
	 * Update Shop category control.
	 * In A, P, L shop categories are given by name while in Hestia we need its id
	 *
	 * @param string $shop_cat Shop category name.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_shop_category( $shop_cat ) {
		if ( ! empty( $shop_cat ) ) {
			$category = get_term_by( 'slug', $shop_cat, 'product_cat' );
			if ( ! empty( $category ) && ! empty( $category->term_id ) ) {
				$cat_id = $category->term_id;
				if ( ! empty( $cat_id ) ) {
					set_theme_mod( 'hestia_shop_categories', array( $cat_id ) );
				}
			}
		}
	}

	/**
	 * Add content form features ribbon to html and add it to about section
	 *
	 * @param string $features_ribbon_content Section's content.
	 * @access public
	 * @since 1.1.49
	 */
	public function features_ribbon_to_html( $features_ribbon_content ) {

		if ( empty( $features_ribbon_content ) ) {
			return;
		}
		$page_editor = get_theme_mod( 'hestia_page_editor' );
		$ribbon_html = ( ! empty( $page_editor ) ? $page_editor : '' );

		$section_content = json_decode( $features_ribbon_content );
		if ( ! empty( $section_content ) ) {
			$i            = 1;
			$ribbon_html .= '<div class="row text-center" style="padding: 75px 0 55px;">';
			foreach ( $section_content as $ribbon_item ) {
				$choice       = ! empty( $ribbon_item->choice ) ? $ribbon_item->choice : 'parallax_icon';
				$icon         = ! empty( $ribbon_item->icon_value ) ? $ribbon_item->icon_value : '';
				$image        = ! empty( $ribbon_item->image_url ) ? $ribbon_item->image_url : '';
				$title        = ! empty( $ribbon_item->title ) ? $ribbon_item->title : '';
				$link         = ! empty( $ribbon_item->link ) ? $ribbon_item->link : '';
				$subtitle     = ! empty( $ribbon_item->subtitle ) ? $ribbon_item->subtitle : '';
				$ribbon_html .= '<div class="col-md-4"><div class="info hestia-info">';
				if ( ! empty( $link ) ) {
					$ribbon_html .= '<a href="' . esc_url( $link ) . '">';
				}

				if ( strpos( $choice, '_icon' ) !== false && ! empty( $icon ) ) {
					$ribbon_html .= '<div class="icon" style="color: #008ed6"><i class="' . esc_attr( hestia_display_fa_icon( $icon ) ) . '"></i></div>';
				}
				if ( strpos( $choice, '_image' ) !== false && ! empty( $image ) ) {
					$ribbon_html .= '<div class="card card-plain" style="max-width: 100px;"><img src="' . esc_url( $image ) . '"/></div>';
				}

				if ( ! empty( $title ) ) {
					$ribbon_html .= '<h4 class="info-title">' . esc_html( $title ) . '</h4>';
				}

				if ( ! empty( $link ) ) {
					$ribbon_html .= '</a>';
				}

				if ( ! empty( $subtitle ) ) {
					$ribbon_html .= '<p>' . wp_kses_post( html_entity_decode( $subtitle ) ) . '</p>';
				}

				$ribbon_html .= '</div></div>';
				if ( $i % 3 === 0 ) {
					$ribbon_html .= '</div>';
					$ribbon_html .= '<div class="row">';
				}
				$i++;
			}
			$ribbon_html .= '</div>';
		}

		if ( ! empty( $ribbon_html ) ) {
			set_theme_mod( 'hestia_page_editor', $ribbon_html );
			$this->sync_content_from_control( $ribbon_html );
		}
	}

	/**
	 * Add content form shortcodes section to html and add it to about section
	 *
	 * @param string $shortcodes_content Section's content.
	 * @access public
	 * @since 1.1.49
	 */
	public function shortcodes_section_to_html( $shortcodes_content ) {

		$execute = get_option( 'should_import_zerif_shortcodes' );
		if ( $execute !== false ) {
			return;
		}

		if ( empty( $shortcodes_content ) ) {
			return;
		}
		$page_editor     = get_theme_mod( 'hestia_page_editor' );
		$shortcode_html  = ( ! empty( $page_editor ) ? $page_editor : '' );
		$section_content = json_decode( $shortcodes_content );
		if ( ! empty( $section_content ) && is_array( $section_content ) ) {
			foreach ( $section_content as $shortcode_section ) {
				$title     = ( ! empty( $shortcode_section->title ) ? $shortcode_section->title : '' );
				$subtitle  = ( ! empty( $shortcode_section->subtitle ) ? $shortcode_section->subtitle : '' );
				$shortcode = ( ! empty( $shortcode_section->shortcode ) ? $shortcode_section->shortcode : '' );

				$shortcode_html .= '<section class="shortcode">';

				if ( ! empty( $title ) || ! empty( $subtitle ) ) {
					$shortcode_html .= '<div class="row"><div class="col-md-8 col-md-offset-2 text-center">';
					if ( ! empty( $title ) ) {
						$shortcode_html .= '<h2 class="hestia-title">' . wp_kses_post( $title ) . '</h2>';
					}

					if ( ! empty( $subtitle ) ) {
						$shortcode_html .= '<h5 class="description">' . wp_kses_post( $subtitle ) . '</h5>';
					}
					$shortcode_html .= '</div></div>';
				}

				if ( ! empty( $shortcode ) ) {
					$shortcode_html .= '<div class="shortcode-content"><div class="row"><div class="col-md-12">' . $shortcode . '</div></div></div>';
				}

				$shortcode_html .= '</section>';

				if ( ! empty( $shortcode_html ) ) {
					set_theme_mod( 'hestia_page_editor', $shortcode_html );
					$this->sync_content_from_control( $shortcode_html );
				}
			}
		}

		update_option( 'should_import_zerif_shortcodes', true );

	}

	/**
	 * Remove sidebars if full width is checked in imported theme.
	 *
	 * @param bool $full_width_option Full width option.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_layout( $full_width_option ) {
		if ( (bool) $full_width_option === true ) {
			set_theme_mod( 'hestia_page_sidebar_layout', 'full-width' );
			set_theme_mod( 'hestia_blog_sidebar_layout', 'full-width' );
		}
	}

	/**
	 * Update nav menus.
	 *
	 * @param string $footer_socials_content Footer socials.
	 * @param string $nav_locations Old nav locations.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_menus( $footer_socials_content, $nav_locations ) {
		$theme_navs = get_theme_mod( 'nav_menu_locations' );
		if ( empty( $theme_navs['primary'] ) && ! empty( $nav_locations['primary'] ) ) {
			$theme_navs['primary'] = $nav_locations['primary'];
		}

		if ( empty( $theme_navs['footer'] ) && ! empty( $nav_locations['parallax_footer_menu'] ) ) {
			$theme_navs['footer'] = $nav_locations['parallax_footer_menu'];
		}

		if ( empty( $theme_navs['top-bar-menu'] ) && ! empty( $footer_socials_content ) ) {

			$menu_name   = __( 'Header socials menu', 'hestia-pro' );
			$menu_exists = wp_get_nav_menu_object( $menu_name );
			if ( ! $menu_exists ) {
				$menu_id     = wp_create_nav_menu( $menu_name );
				$icons_array = json_decode( $footer_socials_content );
				if ( ! empty( $icons_array ) && is_array( $icons_array ) ) {
					foreach ( $icons_array as $social ) {
						if ( ! empty( $social->link ) ) {
							wp_update_nav_menu_item(
								$menu_id,
								0,
								array(
									'menu-item-title'  => __( 'Custom Page', 'hestia-pro' ),
									'menu-item-url'    => $social->link,
									'menu-item-status' => 'publish',
								)
							);
						}
					}
				}
				$theme_navs['top-bar-menu'] = $menu_id;
				set_theme_mod( 'hestia_top_bar_hide', false );
			}
		}
		set_theme_mod( 'nav_menu_locations', $theme_navs );
	}

	/**
	 * Move widgets from old sidebars to hestia's sidebars
	 *
	 * @access public
	 * @since 1.1.49
	 */
	public function update_sidebars() {
		$widgets_from_old_theme = wp_get_sidebars_widgets();
		$new_widget_array       = array();

		if ( ! empty( $widgets_from_old_theme['sidebar-1'] ) ) {
			$new_widget_array['sidebar-1'] = $widgets_from_old_theme['sidebar-1'];
		}

		if ( ! empty( $widgets_from_old_theme['footer-area'] ) ) {
			$new_widget_array['footer-one-widgets'] = $widgets_from_old_theme['footer-area'];
		}

		if ( ! empty( $widgets_from_old_theme['footer-area-2'] ) ) {
			$new_widget_array['footer-two-widgets'] = $widgets_from_old_theme['footer-area-2'];
		}

		if ( ! empty( $widgets_from_old_theme['footer-area-3'] ) || ! empty( $widgets_from_old_theme['footer-area-4'] ) ) {
			$footer_3_content = array();
			if ( ! empty( $widgets_from_old_theme['footer-area-3'] ) ) {
				$footer_3_content = array_merge( $footer_3_content, $widgets_from_old_theme['footer-area-3'] );
			}
			if ( ! empty( $widgets_from_old_theme['footer-area-4'] ) ) {
				$footer_3_content = array_merge( $footer_3_content, $widgets_from_old_theme['footer-area-4'] );
			}
			$new_widget_array['footer-three-widgets'] = $footer_3_content;
		}

		if ( ! isset( $new_widget_array['wp_inactive_widgets'] ) ) {
			$new_widget_array['wp_inactive_widgets'] = array();
		}

		update_option( 'sidebars_widgets', $new_widget_array );
	}

	/**
	 * Update header layout.
	 *
	 * @param string $header_layout Header layout.
	 * @access public
	 * @since 1.1.49
	 */
	public function update_header_layout( $header_layout ) {
		if ( $header_layout === 'layout2' ) {
			set_theme_mod( 'hestia_slider_alignment', 'left' );
		}
	}

	/**
	 * Moves portfolios posts from Parallax cpt portfolio to Jetpack portfolio cpt.
	 *
	 * @param string $post_type Name of the cpt.
	 * @access public
	 * @since 1.1.51
	 */
	public function update_portfolio( $post_type ) {
		if ( ! class_exists( 'Jetpack', false ) || ! ( Jetpack::is_module_active( 'custom-content-types' ) ) ) {
			return;
		}

		$post = new WP_Query(
			array(
				'post_type' => $post_type,
			)
		);
		if ( $post->have_posts() ) {
			while ( $post->have_posts() ) {
				$post->the_post();

				$pid = get_the_ID();

				/* Create post */
				$title   = get_the_title();
				$content = get_the_content();
				$post_id = wp_insert_post(
					array(
						'post_type'    => 'jetpack-portfolio',
						'post_title'   => $title,
						'post_content' => $content,
						'post_status'  => 'publish',
					)
				);

				/* Update post thumbnail */
				$post_thumbnail_id = get_post_thumbnail_id( $pid );
				if ( ! empty( $post_id ) && ! empty( $post_thumbnail_id ) ) {
					update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
				}
			}
			wp_reset_postdata();
		}
	}

	/**
	 * Sync frontpage content with customizer control
	 *
	 * @param string $value New value.
	 */
	protected function sync_content_from_control( $value ) {
		$frontpage_id = get_option( 'page_on_front' );
		if ( ! empty( $frontpage_id ) && ! empty( $value ) ) {
			if ( ! wp_is_post_revision( $frontpage_id ) ) {
				// update the post, which calls save_post again
				$post = array(
					'ID'           => $frontpage_id,
					'post_content' => wp_kses_post( $value ),
				);
				wp_update_post( $post );
			}
		}
	}
}
