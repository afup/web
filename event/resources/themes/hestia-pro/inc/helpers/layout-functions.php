<?php
/**
 * Layout functions needed in global scope.
 *
 * @package Hestia
 */

if ( ! function_exists( 'hestia_wp_link_pages' ) ) {
	/**
	 * Display a custom wp_link_pages for singular view.
	 *
	 * @param array $args arguments.
	 *
	 * @return string
	 * @since Hestia 1.0
	 */
	function hestia_wp_link_pages( $args = array() ) {
		$defaults = array(
			'before'           => '<ul class="nav pagination pagination-primary">',
			'after'            => '</ul>',
			'link_before'      => '',
			'link_after'       => '',
			'next_or_number'   => 'number',
			'nextpagelink'     => esc_html__( 'Next page', 'hestia-pro' ),
			'previouspagelink' => esc_html__( 'Previous page', 'hestia-pro' ),
			'pagelink'         => '%',
			'echo'             => 1,
		);

		$r = wp_parse_args( $args, $defaults );
		$r = apply_filters( 'wp_link_pages_args', $r );

		global $page, $numpages, $multipage, $more;

		$output = '';
		if ( $multipage ) {
			if ( 'number' === $r['next_or_number'] ) {
				$output .= $r['before'];
				for ( $i = 1; $i < ( $numpages + 1 ); $i = $i + 1 ) {
					$j = str_replace( '%', $i, $r['pagelink'] );

					$output .= ' ';
					$output .= $r['link_before'];
					if ( $i !== (int) $page || ( ( ! $more ) && ( (int) $page === 1 ) ) ) {
						$output .= _wp_link_page( $i );
					} else {
						$output .= '<span class="page-numbers current">';
					}
					$output .= $j;
					if ( $i !== (int) $page || ( ( ! $more ) && ( (int) $page === 1 ) ) ) {
						$output .= '</a>';
					} else {
						$output .= '</span>';
					}
					$output .= $r['link_after'];
				}
				$output .= $r['after'];
			} else {
				if ( $more ) {
					$output .= $r['before'];

					$i = $page - 1;
					if ( $i && $more ) {
						$output .= _wp_link_page( $i );
						$output .= $r['link_before'] . $r['previouspagelink'] . $r['link_after'] . '</a>';
					}
					$i = $page + 1;
					if ( $i <= $numpages && $more ) {
						$output .= _wp_link_page( $i );
						$output .= $r['link_before'] . $r['nextpagelink'] . $r['link_after'] . '</a>';
					}
					$output .= $r['after'];
				}
			}// End if().
		}// End if().

		if ( $r['echo'] ) {
			echo wp_kses(
				$output,
				array(
					'div'  => array(
						'class' => array(),
						'id'    => array(),
					),
					'ul'   => array(
						'class' => array(),
					),
					'a'    => array(
						'href' => array(),
					),
					'li'   => array(),
					'span' => array(
						'class' => array(),
					),
				)
			);
		}

		return $output;
	}
}

if ( ! function_exists( 'hestia_comments_template' ) ) {
	/**
	 * Custom list of comments for the theme.
	 *
	 * @since Hestia 1.0
	 */
	function hestia_comments_template() {
		if ( is_user_logged_in() ) {
			$current_user = get_avatar( wp_get_current_user(), 64 );
		} else {
			$current_user = '<img src="' . get_template_directory_uri() . '/assets/img/placeholder.jpg" height="64" width="64"/>';
		}

		$args = array(
			'class_form'         => 'form media-body',
			'class_submit'       => 'btn btn-primary pull-right',
			'title_reply_before' => '<h3 class="hestia-title text-center">',
			'title_reply_after'  => '</h3> <span class="pull-left author"> <div class="avatar">' . $current_user . '</div> </span>',
			'must_log_in'        => '<p class="must-log-in">' .
									sprintf(
										wp_kses(
											/* translators: %s is Link to login */
											__( 'You must be <a href="%s">logged in</a> to post a comment.', 'hestia-pro' ),
											array(
												'a' => array(
													'href' => array(),
												),
											)
										),
										esc_url( wp_login_url( apply_filters( 'the_permalink', esc_url( get_permalink() ) ) ) )
									) . '</p>',
			'comment_field'      => '<div class="form-group label-floating is-empty"> <label class="control-label">' . esc_html__( 'What\'s on your mind?', 'hestia-pro' ) . '</label><textarea id="comment" name="comment" class="form-control" rows="6" aria-required="true"></textarea><span class="hestia-input"></span> </div>',
		);

		return $args;
	}
}

if ( ! function_exists( 'hestia_comments_list' ) ) {
	/**
	 * Custom list of comments for the theme.
	 *
	 * @param string  $comment comment.
	 * @param array   $args arguments.
	 * @param integer $depth depth.
	 *
	 * @since Hestia 1.0
	 */
	function hestia_comments_list( $comment, $args, $depth ) {
		?>
		<div <?php comment_class( empty( $args['has_children'] ) ? 'media' : 'parent media' ); ?>
				id="comment-<?php comment_ID(); ?>">
			<?php if ( $args['type'] !== 'pings' ) : ?>
				<a class="pull-left" href="<?php echo esc_url( get_comment_author_url( $comment ) ); ?> ">
					<div class="comment-author avatar vcard">
						<?php
						if ( $args['avatar_size'] !== false && $args['avatar_size'] !== 0 ) {
							echo get_avatar( $comment, 64 );
						}
						?>
					</div>
				</a>
			<?php endif; ?>
			<div class="media-body">
				<h4 class="media-heading">
					<?php echo get_comment_author_link(); ?>
					<small>
						<?php
						printf(
							/* translators: %1$s is Date, %2$s is Time */
							esc_html__( '&#183; %1$s at %2$s', 'hestia-pro' ),
							get_comment_date(),
							get_comment_time()
						);
						edit_comment_link( esc_html__( '(Edit)', 'hestia-pro' ), '  ', '' );
						?>
					</small>
				</h4>
				<?php comment_text(); ?>
				<div class="media-footer">
					<?php
					echo get_comment_reply_link(
						array(
							'depth'      => $depth,
							'max_depth'  => $args['max_depth'],
							'reply_text' => sprintf(
								'<svg class="svg-text-color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="12px" height="12px"><path d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z"></path></svg>
 %s',
								esc_html__( 'Reply', 'hestia-pro' )
							),
						),
						$comment->comment_ID,
						$comment->comment_post_ID
					);
					?>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'hestia_single_pagination' ) ) {
	/**
	 * Display pagination on single page and single portfolio.
	 */
	function hestia_single_pagination() {
		?>
		<div class="section section-blog-info">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="row">
						<div class="col-md-12">
							<?php
							hestia_wp_link_pages(
								array(
									'before'      => '<div class="text-center"> <ul class="nav pagination pagination-primary">',
									'after'       => '</ul> </div>',
									'link_before' => '<li>',
									'link_after'  => '</li>',
								)
							);
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'hestia_get_image_sizes' ) ) {
	/**
	 * Output image sizes for attachment single page.
	 *
	 * @since Hestia 1.0
	 */
	function hestia_get_image_sizes() {

		/* If not viewing an image attachment page, return. */
		if ( ! wp_attachment_is_image( get_the_ID() ) ) {
			return '';
		}

		/* Set up an empty array for the links. */
		$links = array();

		/* Get the intermediate image sizes and add the full size to the array. */
		$sizes   = get_intermediate_image_sizes();
		$sizes[] = 'full';

		/* Loop through each of the image sizes. */
		foreach ( $sizes as $size ) {

			/* Get the image source, width, height, and whether it's intermediate. */
			$image = wp_get_attachment_image_src( get_the_ID(), $size );

			/* Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size. */
			if ( ! empty( $image ) && ( ( ! empty( $image[3] ) && true === $image[3] ) || 'full' === $size ) ) {
				$links[] = '<a target="_blank" class="image-size-link" href="' . esc_url( $image[0] ) . '">' . $image[1] . ' &times; ' . $image[2] . '</a>';
			}
		}

		/* Join the links in a string and return. */

		return join( ' <span class="sep">|</span> ', $links );
	}
}

if ( ! function_exists( 'hestia_sidebar_placeholder' ) ) {
	/**
	 * Display sidebar placeholder.
	 *
	 * @param string $class_to_add Classes to add on container.
	 * @param string $sidebar_id Id of the sidebar used as a class to differentiate hestia-widget-placeholder for blog and shop pages.
	 * @param string $classes Classes to add to placeholder.
	 *
	 * @access public
	 * @since  1.1.24
	 */
	function hestia_sidebar_placeholder( $class_to_add, $sidebar_id, $classes = 'col-md-3 blog-sidebar-wrapper' ) {
		$content = apply_filters( 'hestia_sidebar_placeholder_content', esc_html__( 'This sidebar is active but empty. In order to use this layout, please add widgets in the sidebar', 'hestia-pro' ) );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<aside id="secondary" class="blog-sidebar <?php echo esc_attr( $class_to_add ); ?>" role="complementary">
				<div class="hestia-widget-placeholder
				<?php
				if ( ! empty( $sidebar_id ) ) {
					echo esc_attr( $sidebar_id );
				}
				?>
				">
					<?php
					the_widget( 'WP_Widget_Text', 'text=' . $content );
					?>
				</div>
			</aside><!-- .sidebar .widget-area -->
		</div>
		<?php
	}
}

if ( ! function_exists( 'hestia_display_customizer_shortcut' ) ) {
	/**
	 * This function display a shortcut to a customizer control.
	 *
	 * @param string $class_name The name of control we want to link this shortcut with.
	 * @param bool   $is_section_toggle Tells function to display eye icon if it's true.
	 */
	function hestia_display_customizer_shortcut( $class_name, $is_section_toggle = false, $should_return = false ) {
		if ( ! is_customize_preview() ) {
			return;
		}
		$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
				<path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path>
			</svg>';
		if ( $is_section_toggle ) {
			$icon = '<i class="far fa-eye"></i>';
		}

		$data = '<span class="hestia-hide-section-shortcut customize-partial-edit-shortcut customize-partial-edit-shortcut-' . esc_attr( $class_name ) . '">
		<button class="customize-partial-edit-shortcut-button">
			' . $icon . '
		</button>
	</span>';
		if ( $should_return === true ) {
			return $data;
		}
		echo $data;
	}
}

if ( ! function_exists( 'hestia_no_content_get_header' ) ) {
	/**
	 * Header for page builder blank template
	 *
	 * @since  1.1.24
	 * @access public
	 */
	function hestia_no_content_get_header() {

		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> class="no-js">
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="profile" href="http://gmpg.org/xfn/11">
			<?php wp_head(); ?>
		</head>

		<body <?php body_class(); ?>>
		<?php
		do_action( 'hestia_page_builder_content_body_before' );
	}
}

if ( ! function_exists( 'hestia_no_content_get_footer' ) ) {
	/**
	 * Footer for page builder blank template
	 *
	 * @since  1.1.24
	 * @access public
	 */
	function hestia_no_content_get_footer() {
		do_action( 'hestia_page_builder_content_body_after' );
		wp_footer();
		?>
		</body>
		</html>
		<?php
	}
}

if ( ! function_exists( 'hestia_is_external_url' ) ) {
	/**
	 * Utility to check if URL is external
	 *
	 * @param string $url Url to check.
	 *
	 * @return string
	 */
	function hestia_is_external_url( $url ) {
		$link_url = parse_url( $url );
		$home_url = parse_url( home_url() );

		if ( ! empty( $link_url['host'] ) ) {
			if ( $link_url['host'] !== $home_url['host'] ) {
				return ' target="_blank"';
			}
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'hestia_hex_rgb' ) ) {
	/**
	 * HEX colors conversion to RGB.
	 *
	 * @param string $input Color in hex format.
	 *
	 * @return array|string RGB string.
	 * @since Hestia 1.0
	 */
	function hestia_hex_rgb( $input ) {

		$default = 'rgb(0,0,0)';

		// Return default if no color provided
		if ( empty( $input ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $input[0] === '#' ) {
			$input = substr( $input, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( strlen( $input ) === 6 ) {
			$hex = array( $input[0] . $input[1], $input[2] . $input[3], $input[4] . $input[5] );
		} elseif ( strlen( $input ) === 3 ) {
			$hex = array( $input[0] . $input[0], $input[1] . $input[1], $input[2] . $input[2] );
		} else {
			return $default;
		}

		// Convert hexadeciomal color to rgb(a)
		$rgb = array_map( 'hexdec', $hex );

		return $rgb;
	}
}

if ( ! function_exists( 'hestia_rgb_to_rgba' ) ) {
	/**
	 * Add opacity to rgb.
	 *
	 * @param array $rgb RGB color.
	 * @param int   $opacity Opacity value.
	 *
	 * @return string
	 */
	function hestia_rgb_to_rgba( $rgb, $opacity ) {

		if ( ! is_array( $rgb ) ) {
			return '';
		}
		// Check for opacity
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ',', $rgb ) . ')';
		}

		return esc_html( $output );
	}
}

if ( ! function_exists( 'hestia_hex_rgba' ) ) {
	/**
	 * HEX colors conversion to RGBA.
	 *
	 * @param array|string $input RGB color.
	 * @param int          $opacity Opacity value.
	 *
	 * @return string
	 */
	function hestia_hex_rgba( $input, $opacity = 1 ) {
		$rgb = hestia_hex_rgb( $input );

		return hestia_rgb_to_rgba( $rgb, $opacity );
	}
}

if ( ! function_exists( 'hestia_generate_gradient_color' ) ) {
	/**
	 * Generate gradient second color based on Header Gradient color
	 *
	 * @param string $input the color from which to generate the gradient color.
	 * @param string $opacity the opacity for the generated color.
	 *
	 * @return string RGBA string.
	 * @since Hestia 1.1.53
	 */
	function hestia_generate_gradient_color( $input, $opacity = '' ) {

		$rgb = hestia_hex_rgb( $input );

		$rgb[0] = $rgb[0] + 66;
		$rgb[1] = $rgb[1] + 28;
		$rgb[2] = $rgb[2] - 21;

		if ( $rgb[0] >= 255 ) {
			$rgb[0] = 255;
		}

		if ( $rgb[1] >= 255 ) {
			$rgb[1] = 255;
		}

		if ( $rgb[2] <= 0 ) {
			$rgb[2] = 0;
		}

		return hestia_rgb_to_rgba( $rgb, $opacity );
	}
}

if ( ! function_exists( 'hestia_adjust_brightness' ) ) {
	/**
	 * Generate a new color, darker or lighter.
	 *
	 * @param string $hex Color in hex.
	 * @param int    $steps Steps should be between -255 and 255. Negative = darker, positive = lighter.
	 *
	 * @return string
	 */
	function hestia_adjust_brightness( $hex, $steps ) {
		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max( - 255, min( 255, $steps ) );
		// Normalize into a six character long hex string
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) === 3 ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
		}
		// Split into three parts: R, G and B
		$color_parts = str_split( $hex, 2 );
		$return      = '#';
		foreach ( $color_parts as $color ) {
			$color = hexdec( $color ); // Convert to decimal
			$color = max( 0, min( 255, $color + $steps ) ); // Adjust color

			$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code
		}

		return $return;
	}
}

if ( ! function_exists( 'hestia_add_animationation' ) ) {
	/**
	 * Add animation attribute for animate-on-scroll.
	 *
	 * @param string $animation_type the type of animation.
	 *
	 * @return string
	 */
	function hestia_add_animationation( $animation_type ) {
		if ( ! defined( 'HESTIA_PRO_FLAG' ) ) {
			return '';
		}
		$enable_animations = apply_filters( 'hestia_enable_animations', true );
		$output            = '';
		if ( $enable_animations && ! empty( $animation_type ) ) {
			$output .= ' data-aos="';
			$output .= $animation_type;
			$output .= '" ';
		}

		return $output;
	}
}

if ( ! function_exists( 'hestia_layout' ) ) {
	/**
	 * Returns class names used for the main page/post content div
	 * Based on the Boxed Layout and Header Layout customizer options
	 *
	 * @since    Hestia 1.0
	 * @modified 1.1.64
	 */
	function hestia_layout() {

		/**
		 * For the Page Builder Full Width template don't add any extra classes (except main)
		 */
		if ( is_page_template( 'page-templates/template-pagebuilder-full-width.php' ) ) {
			return 'main';
		}

		$layout_class = 'main ';

		$hestia_general_layout = get_theme_mod( 'hestia_general_layout', apply_filters( 'hestia_boxed_layout_default', 1 ) );

		/**
		 * Add main-raised class when the Boxed Layout option is enabled
		 */
		if ( isset( $hestia_general_layout ) && (bool) $hestia_general_layout === true ) {
			$layout_class .= apply_filters( 'hestia_boxed_layout', ' main-raised ' );
		}

		/**
		 * For WooCommerce pages don't add any extra classes (except main or main-raised)
		 */
		if ( class_exists( 'WooCommerce', false ) && is_product() ) {
			return $layout_class;
		}

		return $layout_class;
	}
}

if ( ! function_exists( 'hestia_limit_content' ) ) {
	/**
	 * Function that limits a text to $limit words, words that are separated by $separator
	 *
	 * @param array  $input Content to limit.
	 * @param int    $limit Max size.
	 * @param string $separator Separator.
	 * @param bool   $show_more Flag to decide if '...' should be added at the end of result.
	 *
	 * @return string
	 */
	function hestia_limit_content( $input, $limit, $separator = ',', $show_more = true ) {
		if ( $limit === 0 ) {
			return '';
		}
		$length = sizeof( $input );
		$more   = $length > $limit ? apply_filters( 'hestia_text_more', ' ...' ) : '';
		$result = '';
		$index  = 0;
		foreach ( $input as $word ) {
			if ( $index < $limit || $limit < 0 ) {
				$result .= $word;
				if ( $length > 1 && $index !== $length - 1 && $index !== $limit - 1 ) {
					$result .= $separator;
					if ( $separator === ',' ) {
						$result .= ' ';
					}
				}
			}
			$index ++;
		}
		if ( $show_more === true ) {
			$result .= $more;
		}

		return $result;
	}
}

if ( ! function_exists( 'hestia_edited_with_pagebuilder' ) ) {
	/**
	 * This function returns whether the theme use or not one of the following page builders:
	 * SiteOrigin, WP Bakery, Elementor, Divi Builder or Beaver Builder.
	 *
	 * @return bool
	 * @since 1.1.63
	 */
	function hestia_edited_with_pagebuilder( $pid = '' ) {
		$frontpage_id = get_option( 'page_on_front' );
		if ( ! empty( $pid ) ) {
			$frontpage_id = $pid;
		}
		/**
		 * Exit with false if there is no page set as frontpage.
		 */
		if ( intval( $frontpage_id ) === 0 ) {
			return false;
		}
		/**
		 * Elementor, Beaver Builder, Divi and Siteorigin mark if the page was edited with its editors in post meta
		 * so we'll have to check if plugins exists and the page was edited with page builder.
		 */
		$post_meta            = ! empty( $frontpage_id ) ? get_post_meta( $frontpage_id ) : '';
		$page_builders_values = array(
			'elementor'  => ! empty( $post_meta['_elementor_edit_mode'] ) && $post_meta['_elementor_edit_mode'][0] === 'builder' && class_exists( 'Elementor\Plugin', false ),
			'beaver'     => ! empty( $post_meta['_fl_builder_enabled'] ) && $post_meta['_fl_builder_enabled'][0] === '1' && class_exists( 'FLBuilder', false ),
			'siteorigin' => ! empty( $post_meta['panels_data'] ) && class_exists( 'SiteOrigin_Panels', false ),
			'divi'       => ! empty( $post_meta['_et_pb_use_builder'] ) && $post_meta['_et_pb_use_builder'][0] === 'on' && class_exists( 'ET_Builder_Plugin', false ),
		);
		/**
		 * WP Bakery (former Visual Composer) doesn't store a flag in meta data to say whether or not the page
		 * is edited with it so we have to check post content if it contains shortcodes from plugin.
		 */
		$post_content = get_post_field( 'post_content', $frontpage_id );
		if ( ! empty( $post_content ) ) {
			$page_builders_values['wpbakery'] = class_exists( 'Vc_Manager', false ) && strpos( $post_content, '[vc_' ) !== false;
		}
		/**
		 * Check if at least one page builder returns true and return true if it does.
		 */
		foreach ( $page_builders_values as $page_builder ) {
			if ( $page_builder === true ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'hestia_category' ) ) {
	/**
	 * Displays blog categories
	 *
	 * @param boolean $rel_tag should have rel='tag'.
	 *
	 * @return string
	 * @since Hestia 1.0
	 */
	function hestia_category( $rel_tag = true ) {

		$hestia_disable_categories = get_theme_mod( 'hestia_disable_categories', 'one' );

		if ( ! $hestia_disable_categories || $hestia_disable_categories === 'none' ) {
			return '';
		}

		$filtered_categories = '';

		$categories = get_the_category();

		if ( ! empty( $categories ) ) {

			foreach ( $categories as $category ) {
				/* translators: %s is Category name */
				$filtered_categories .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'hestia-pro' ), $category->name ) ) . '" ' . ( $rel_tag === true ? ' rel="tag"' : '' ) . '>' . esc_html( $category->name ) . '</a> ';
				if ( $hestia_disable_categories === 'one' ) {
					break;
				}
			}
		}

		return $filtered_categories;
	}
}

if ( ! function_exists( 'hestia_get_excerpt_default' ) ) {

	/**
	 * Get default values excerpt value.
	 *
	 * @access public
	 */
	function hestia_get_excerpt_default() {
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			return 40;
		}

		return 75;
	}
}

if ( ! function_exists( 'hestia_contact_form_placeholder' ) ) {

	/**
	 * Render the contact form placeholder for the contact section.
	 *
	 * @since    1.1.31
	 * @modified 1.1.86
	 */
	function hestia_contact_form_placeholder() {
		return '
		<div class="col-md-5 col-md-offset-2 pirate-forms-placeholder hestia-contact-form-col">
			<div class="card card-contact">
				<div class="header header-raised header-primary text-center">
				' . hestia_display_customizer_shortcut( 'hestia_contact_info', false, true ) . '
					<h4 class="hestia-title">' . esc_html__( 'Contact Us', 'hestia-pro' ) . '</h4>
				</div>
				<div class="pirate-forms-placeholder-overlay">	
					<div class="pirate-forms-placeholder-align">		
						<h4 class="placeholder-text"> ' .
			sprintf(
				/* translators: %1$s is Plugin name */
				esc_html__( 'In order to add a contact form to this section, you need to install the %s plugin.', 'hestia-pro' ),
				esc_html( 'WPForms Lite' )
			) . ' </h4>	
					</div>	
				</div>			
				<div class="content">
					<div class="pirate_forms_wrap">
						<form class="pirate_forms ">
							<div class="pirate_forms_three_inputs_wrap">
								<div class="col-sm-4 col-lg-4 form_field_wrap contact_name_wrap pirate_forms_three_inputs  ">
									<label for="pirate-forms-contact-name"></label>
									<input id="pirate-forms-contact-name" class="form-control" type="text" value="" placeholder="Your Name">
								</div>
								<div class="col-sm-4 col-lg-4 form_field_wrap contact_email_wrap pirate_forms_three_inputs">
									<label for="pirate-forms-contact-email"></label>
									<input id="pirate-forms-contact-email" class="form-control" type="email" value="" placeholder="Your Email">
								</div>
								<div class="col-sm-4 col-lg-4 form_field_wrap contact_subject_wrap pirate_forms_three_inputs">
									<label for="pirate-forms-contact-subject"></label>
									<input id="pirate-forms-contact-subject" class="form-control" type="text" value="" placeholder="Subject">
								</div>
							</div>
						</form>
						<div class="col-sm-12 col-lg-12 form_field_wrap contact_message_wrap">
								<textarea id="pirate-forms-contact-message" required="" class="form-control" placeholder="Your message"></textarea>
							</div>
						<div class="col-xs-12 form_field_wrap contact_submit_wrap">
								<button id="pirate-forms-contact-submit" class="pirate-forms-submit-button" type="submit">Send Message</button>
							</div>
						<div class="pirate_forms_clearfix"></div>
					</div>
				</div>
			</div>
		</div>';
	}
}

/**
 * Check if WooCommerce exists.
 *
 * @return bool
 */
function hestia_check_woocommerce() {
	return class_exists( 'WooCommerce', false ) && ( is_woocommerce() || is_cart() || is_checkout() );
}

/**
 * This function returns the page id of a WooCommerce page.
 *
 * @return bool|mixed
 */
function hestia_get_woo_page_id() {
	if ( ! hestia_check_woocommerce() ) {
		return false;
	}
	if ( is_shop() ) {
		return get_option( 'woocommerce_shop_page_id' );
	}
	if ( is_cart() ) {
		return get_option( 'woocommerce_cart_page_id' );
	}
	if ( is_checkout() ) {
		return get_option( 'woocommerce_checkout_page_id' );
	}

	return false;
}

/**
 * This function returns the current page id.
 *
 * @return bool|false|int|mixed
 */
function hestia_get_current_page_id() {
	if ( is_home() ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			return get_option( 'page_for_posts' );
		}

		return false;
	}
	if ( class_exists( 'WooCommerce', false ) && is_shop() ) {
		return get_option( 'woocommerce_shop_page_id' );
	}
	if ( is_search() ) {
		return false;
	}
	if ( is_archive() ) {
		return false;
	}

	return hestia_get_woo_page_id() !== false ? hestia_get_woo_page_id() : get_the_ID();
}

/**
 * Determine if featured posts are enabled
 *
 * @return bool
 */
function hestia_featured_posts_enabled() {
	$featured_posts_category = get_theme_mod( 'hestia_featured_posts_category', apply_filters( 'hestia_featured_posts_category_default', 0 ) );

	if ( empty( $featured_posts_category ) ) {
		return false;
	}

	if ( count( $featured_posts_category ) === 1 && empty( $featured_posts_category[0] ) ) {
		return false;
	}

	return $featured_posts_category;

}

if ( ! function_exists( 'hestia_get_blog_layout_default' ) ) {

	/**
	 * Get default option for sidebar layout
	 *
	 * @return string
	 */
	function hestia_get_blog_layout_default() {
		$sidebar_on_single_post = get_theme_mod( 'hestia_sidebar_on_single_post', false );
		$sidebar_on_index       = get_theme_mod( 'hestia_sidebar_on_index', false );

		return $sidebar_on_single_post && $sidebar_on_index ? 'full-width' : 'sidebar-right';
	}
}

if ( ! function_exists( 'hestia_display_fa_icon' ) ) {
	/**
	 * Properly display old fontawesome icon values by adding the prefix class.
	 *
	 * @param string $value icon value.
	 *
	 * @return string
	 */
	function hestia_display_fa_icon( $value ) {
		hestia_load_fa();
		if ( strpos( $value, 'fa-' ) !== 0 ) {
			return $value;
		}
		return 'fa ' . $value;
	}
}

/**
 * List of All Google fonts
 *
 * @since 1.1.38
 */
function hestia_get_google_fonts() {
	return apply_filters(
		'hestia_google_fonts_array',
		// Updated on 17/07/19
		array(
			'ABeeZee',
			'Abel',
			'Abhaya Libre',
			'Abril Fatface',
			'Aclonica',
			'Acme',
			'Actor',
			'Adamina',
			'Advent Pro',
			'Aguafina Script',
			'Akronim',
			'Aladin',
			'Aldrich',
			'Alef',
			'Alegreya',
			'Alegreya SC',
			'Alegreya Sans',
			'Alegreya Sans SC',
			'Aleo',
			'Alex Brush',
			'Alfa Slab One',
			'Alice',
			'Alike',
			'Alike Angular',
			'Allan',
			'Allerta',
			'Allerta Stencil',
			'Allura',
			'Almendra',
			'Almendra Display',
			'Almendra SC',
			'Amarante',
			'Amaranth',
			'Amatic SC',
			'Amethysta',
			'Amiko',
			'Amiri',
			'Amita',
			'Anaheim',
			'Andada',
			'Andika',
			'Angkor',
			'Annie Use Your Telescope',
			'Anonymous Pro',
			'Antic',
			'Antic Didone',
			'Antic Slab',
			'Anton',
			'Arapey',
			'Arbutus',
			'Arbutus Slab',
			'Architects Daughter',
			'Archivo',
			'Archivo Black',
			'Archivo Narrow',
			'Aref Ruqaa',
			'Arima Madurai',
			'Arimo',
			'Arizonia',
			'Armata',
			'Arsenal',
			'Artifika',
			'Arvo',
			'Arya',
			'Asap',
			'Asap Condensed',
			'Asar',
			'Asset',
			'Assistant',
			'Astloch',
			'Asul',
			'Athiti',
			'Atma',
			'Atomic Age',
			'Aubrey',
			'Audiowide',
			'Autour One',
			'Average',
			'Average Sans',
			'Averia Gruesa Libre',
			'Averia Libre',
			'Averia Sans Libre',
			'Averia Serif Libre',
			'B612',
			'B612 Mono',
			'Bad Script',
			'Bahiana',
			'Bahianita',
			'Bai Jamjuree',
			'Baloo',
			'Baloo Bhai',
			'Baloo Bhaijaan',
			'Baloo Bhaina',
			'Baloo Chettan',
			'Baloo Da',
			'Baloo Paaji',
			'Baloo Tamma',
			'Baloo Tammudu',
			'Baloo Thambi',
			'Balthazar',
			'Bangers',
			'Barlow',
			'Barlow Condensed',
			'Barlow Semi Condensed',
			'Barriecito',
			'Barrio',
			'Basic',
			'Battambang',
			'Baumans',
			'Bayon',
			'Belgrano',
			'Bellefair',
			'Belleza',
			'BenchNine',
			'Bentham',
			'Berkshire Swash',
			'Bevan',
			'Bigelow Rules',
			'Bigshot One',
			'Bilbo',
			'Bilbo Swash Caps',
			'BioRhyme',
			'BioRhyme Expanded',
			'Biryani',
			'Bitter',
			'Black And White Picture',
			'Black Han Sans',
			'Black Ops One',
			'Bokor',
			'Bonbon',
			'Boogaloo',
			'Bowlby One',
			'Bowlby One SC',
			'Brawler',
			'Bree Serif',
			'Bubblegum Sans',
			'Bubbler One',
			'Buda',
			'Buenard',
			'Bungee',
			'Bungee Hairline',
			'Bungee Inline',
			'Bungee Outline',
			'Bungee Shade',
			'Butcherman',
			'Butterfly Kids',
			'Cabin',
			'Cabin Condensed',
			'Cabin Sketch',
			'Caesar Dressing',
			'Cagliostro',
			'Cairo',
			'Calligraffitti',
			'Cambay',
			'Cambo',
			'Candal',
			'Cantarell',
			'Cantata One',
			'Cantora One',
			'Capriola',
			'Cardo',
			'Carme',
			'Carrois Gothic',
			'Carrois Gothic SC',
			'Carter One',
			'Catamaran',
			'Caudex',
			'Caveat',
			'Caveat Brush',
			'Cedarville Cursive',
			'Ceviche One',
			'Chakra Petch',
			'Changa',
			'Changa One',
			'Chango',
			'Charm',
			'Charmonman',
			'Chathura',
			'Chau Philomene One',
			'Chela One',
			'Chelsea Market',
			'Chenla',
			'Cherry Cream Soda',
			'Cherry Swash',
			'Chewy',
			'Chicle',
			'Chivo',
			'Chonburi',
			'Cinzel',
			'Cinzel Decorative',
			'Clicker Script',
			'Coda',
			'Coda Caption',
			'Codystar',
			'Coiny',
			'Combo',
			'Comfortaa',
			'Coming Soon',
			'Concert One',
			'Condiment',
			'Content',
			'Contrail One',
			'Convergence',
			'Cookie',
			'Copse',
			'Corben',
			'Cormorant',
			'Cormorant Garamond',
			'Cormorant Infant',
			'Cormorant SC',
			'Cormorant Unicase',
			'Cormorant Upright',
			'Courgette',
			'Cousine',
			'Coustard',
			'Covered By Your Grace',
			'Crafty Girls',
			'Creepster',
			'Crete Round',
			'Crimson Text',
			'Croissant One',
			'Crushed',
			'Cuprum',
			'Cute Font',
			'Cutive',
			'Cutive Mono',
			'DM Sans',
			'DM Serif Display',
			'DM Serif Text',
			'Damion',
			'Dancing Script',
			'Dangrek',
			'Darker Grotesque',
			'David Libre',
			'Dawning of a New Day',
			'Days One',
			'Dekko',
			'Delius',
			'Delius Swash Caps',
			'Delius Unicase',
			'Della Respira',
			'Denk One',
			'Devonshire',
			'Dhurjati',
			'Didact Gothic',
			'Diplomata',
			'Diplomata SC',
			'Do Hyeon',
			'Dokdo',
			'Domine',
			'Donegal One',
			'Doppio One',
			'Dorsa',
			'Dosis',
			'Dr Sugiyama',
			'Duru Sans',
			'Dynalight',
			'EB Garamond',
			'Eagle Lake',
			'East Sea Dokdo',
			'Eater',
			'Economica',
			'Eczar',
			'El Messiri',
			'Electrolize',
			'Elsie',
			'Elsie Swash Caps',
			'Emblema One',
			'Emilys Candy',
			'Encode Sans',
			'Encode Sans Condensed',
			'Encode Sans Expanded',
			'Encode Sans Semi Condensed',
			'Encode Sans Semi Expanded',
			'Engagement',
			'Englebert',
			'Enriqueta',
			'Erica One',
			'Esteban',
			'Euphoria Script',
			'Ewert',
			'Exo',
			'Exo 2',
			'Expletus Sans',
			'Fahkwang',
			'Fanwood Text',
			'Farsan',
			'Fascinate',
			'Fascinate Inline',
			'Faster One',
			'Fasthand',
			'Fauna One',
			'Faustina',
			'Federant',
			'Federo',
			'Felipa',
			'Fenix',
			'Finger Paint',
			'Fira Mono',
			'Fira Sans',
			'Fira Sans Condensed',
			'Fira Sans Extra Condensed',
			'Fjalla One',
			'Fjord One',
			'Flamenco',
			'Flavors',
			'Fondamento',
			'Fontdiner Swanky',
			'Forum',
			'Francois One',
			'Frank Ruhl Libre',
			'Freckle Face',
			'Fredericka the Great',
			'Fredoka One',
			'Freehand',
			'Fresca',
			'Frijole',
			'Fruktur',
			'Fugaz One',
			'GFS Didot',
			'GFS Neohellenic',
			'Gabriela',
			'Gaegu',
			'Gafata',
			'Galada',
			'Galdeano',
			'Galindo',
			'Gamja Flower',
			'Gentium Basic',
			'Gentium Book Basic',
			'Geo',
			'Geostar',
			'Geostar Fill',
			'Germania One',
			'Gidugu',
			'Gilda Display',
			'Give You Glory',
			'Glass Antiqua',
			'Glegoo',
			'Gloria Hallelujah',
			'Goblin One',
			'Gochi Hand',
			'Gorditas',
			'Gothic A1',
			'Goudy Bookletter 1911',
			'Graduate',
			'Grand Hotel',
			'Gravitas One',
			'Great Vibes',
			'Griffy',
			'Gruppo',
			'Gudea',
			'Gugi',
			'Gurajada',
			'Habibi',
			'Halant',
			'Hammersmith One',
			'Hanalei',
			'Hanalei Fill',
			'Handlee',
			'Hanuman',
			'Happy Monkey',
			'Harmattan',
			'Headland One',
			'Heebo',
			'Henny Penny',
			'Herr Von Muellerhoff',
			'Hi Melody',
			'Hind',
			'Hind Guntur',
			'Hind Madurai',
			'Hind Siliguri',
			'Hind Vadodara',
			'Holtwood One SC',
			'Homemade Apple',
			'Homenaje',
			'IBM Plex Mono',
			'IBM Plex Sans',
			'IBM Plex Sans Condensed',
			'IBM Plex Serif',
			'IM Fell DW Pica',
			'IM Fell DW Pica SC',
			'IM Fell Double Pica',
			'IM Fell Double Pica SC',
			'IM Fell English',
			'IM Fell English SC',
			'IM Fell French Canon',
			'IM Fell French Canon SC',
			'IM Fell Great Primer',
			'IM Fell Great Primer SC',
			'Iceberg',
			'Iceland',
			'Imprima',
			'Inconsolata',
			'Inder',
			'Indie Flower',
			'Inika',
			'Inknut Antiqua',
			'Irish Grover',
			'Istok Web',
			'Italiana',
			'Italianno',
			'Itim',
			'Jacques Francois',
			'Jacques Francois Shadow',
			'Jaldi',
			'Jim Nightshade',
			'Jockey One',
			'Jolly Lodger',
			'Jomhuria',
			'Josefin Sans',
			'Josefin Slab',
			'Joti One',
			'Jua',
			'Judson',
			'Julee',
			'Julius Sans One',
			'Junge',
			'Jura',
			'Just Another Hand',
			'Just Me Again Down Here',
			'K2D',
			'Kadwa',
			'Kalam',
			'Kameron',
			'Kanit',
			'Kantumruy',
			'Karla',
			'Karma',
			'Katibeh',
			'Kaushan Script',
			'Kavivanar',
			'Kavoon',
			'Kdam Thmor',
			'Keania One',
			'Kelly Slab',
			'Kenia',
			'Khand',
			'Khmer',
			'Khula',
			'Kirang Haerang',
			'Kite One',
			'Knewave',
			'KoHo',
			'Kodchasan',
			'Kosugi',
			'Kosugi Maru',
			'Kotta One',
			'Koulen',
			'Kranky',
			'Kreon',
			'Kristi',
			'Krona One',
			'Krub',
			'Kumar One',
			'Kumar One Outline',
			'Kurale',
			'La Belle Aurore',
			'Laila',
			'Lakki Reddy',
			'Lalezar',
			'Lancelot',
			'Lateef',
			'Lato',
			'League Script',
			'Leckerli One',
			'Ledger',
			'Lekton',
			'Lemon',
			'Lemonada',
			'Libre Barcode 128',
			'Libre Barcode 128 Text',
			'Libre Barcode 39',
			'Libre Barcode 39 Extended',
			'Libre Barcode 39 Extended Text',
			'Libre Barcode 39 Text',
			'Libre Baskerville',
			'Libre Franklin',
			'Life Savers',
			'Lilita One',
			'Lily Script One',
			'Limelight',
			'Linden Hill',
			'Literata',
			'Lobster',
			'Lobster Two',
			'Londrina Outline',
			'Londrina Shadow',
			'Londrina Sketch',
			'Londrina Solid',
			'Lora',
			'Love Ya Like A Sister',
			'Loved by the King',
			'Lovers Quarrel',
			'Luckiest Guy',
			'Lusitana',
			'Lustria',
			'M PLUS 1p',
			'M PLUS Rounded 1c',
			'Macondo',
			'Macondo Swash Caps',
			'Mada',
			'Magra',
			'Maiden Orange',
			'Maitree',
			'Major Mono Display',
			'Mako',
			'Mali',
			'Mallanna',
			'Mandali',
			'Manuale',
			'Marcellus',
			'Marcellus SC',
			'Marck Script',
			'Margarine',
			'Markazi Text',
			'Marko One',
			'Marmelad',
			'Martel',
			'Martel Sans',
			'Marvel',
			'Mate',
			'Mate SC',
			'Maven Pro',
			'McLaren',
			'Meddon',
			'MedievalSharp',
			'Medula One',
			'Meera Inimai',
			'Megrim',
			'Meie Script',
			'Merienda',
			'Merienda One',
			'Merriweather',
			'Merriweather Sans',
			'Metal',
			'Metal Mania',
			'Metamorphous',
			'Metrophobic',
			'Michroma',
			'Milonga',
			'Miltonian',
			'Miltonian Tattoo',
			'Mina',
			'Miniver',
			'Miriam Libre',
			'Mirza',
			'Miss Fajardose',
			'Mitr',
			'Modak',
			'Modern Antiqua',
			'Mogra',
			'Molengo',
			'Molle',
			'Monda',
			'Monofett',
			'Monoton',
			'Monsieur La Doulaise',
			'Montaga',
			'Montez',
			'Montserrat',
			'Montserrat Alternates',
			'Montserrat Subrayada',
			'Moul',
			'Moulpali',
			'Mountains of Christmas',
			'Mouse Memoirs',
			'Mr Bedfort',
			'Mr Dafoe',
			'Mr De Haviland',
			'Mrs Saint Delafield',
			'Mrs Sheppards',
			'Mukta',
			'Mukta Mahee',
			'Mukta Malar',
			'Mukta Vaani',
			'Muli',
			'Mystery Quest',
			'NTR',
			'Nanum Brush Script',
			'Nanum Gothic',
			'Nanum Gothic Coding',
			'Nanum Myeongjo',
			'Nanum Pen Script',
			'Neucha',
			'Neuton',
			'New Rocker',
			'News Cycle',
			'Niconne',
			'Niramit',
			'Nixie One',
			'Nobile',
			'Nokora',
			'Norican',
			'Nosifer',
			'Notable',
			'Nothing You Could Do',
			'Noticia Text',
			'Noto Sans',
			'Noto Sans HK',
			'Noto Sans JP',
			'Noto Sans KR',
			'Noto Sans SC',
			'Noto Sans TC',
			'Noto Serif',
			'Noto Serif JP',
			'Noto Serif KR',
			'Noto Serif SC',
			'Noto Serif TC',
			'Nova Cut',
			'Nova Flat',
			'Nova Mono',
			'Nova Oval',
			'Nova Round',
			'Nova Script',
			'Nova Slim',
			'Nova Square',
			'Numans',
			'Nunito',
			'Nunito Sans',
			'Odor Mean Chey',
			'Offside',
			'Old Standard TT',
			'Oldenburg',
			'Oleo Script',
			'Oleo Script Swash Caps',
			'Open Sans',
			'Open Sans Condensed',
			'Oranienbaum',
			'Orbitron',
			'Oregano',
			'Orienta',
			'Original Surfer',
			'Oswald',
			'Over the Rainbow',
			'Overlock',
			'Overlock SC',
			'Overpass',
			'Overpass Mono',
			'Ovo',
			'Oxygen',
			'Oxygen Mono',
			'PT Mono',
			'PT Sans',
			'PT Sans Caption',
			'PT Sans Narrow',
			'PT Serif',
			'PT Serif Caption',
			'Pacifico',
			'Padauk',
			'Palanquin',
			'Palanquin Dark',
			'Pangolin',
			'Paprika',
			'Parisienne',
			'Passero One',
			'Passion One',
			'Pathway Gothic One',
			'Patrick Hand',
			'Patrick Hand SC',
			'Pattaya',
			'Patua One',
			'Pavanam',
			'Paytone One',
			'Peddana',
			'Peralta',
			'Permanent Marker',
			'Petit Formal Script',
			'Petrona',
			'Philosopher',
			'Piedra',
			'Pinyon Script',
			'Pirata One',
			'Plaster',
			'Play',
			'Playball',
			'Playfair Display',
			'Playfair Display SC',
			'Podkova',
			'Poiret One',
			'Poller One',
			'Poly',
			'Pompiere',
			'Pontano Sans',
			'Poor Story',
			'Poppins',
			'Port Lligat Sans',
			'Port Lligat Slab',
			'Pragati Narrow',
			'Prata',
			'Preahvihear',
			'Press Start 2P',
			'Pridi',
			'Princess Sofia',
			'Prociono',
			'Prompt',
			'Prosto One',
			'Proza Libre',
			'Puritan',
			'Purple Purse',
			'Quando',
			'Quantico',
			'Quattrocento',
			'Quattrocento Sans',
			'Questrial',
			'Quicksand',
			'Quintessential',
			'Qwigley',
			'Racing Sans One',
			'Radley',
			'Rajdhani',
			'Rakkas',
			'Raleway',
			'Raleway Dots',
			'Ramabhadra',
			'Ramaraja',
			'Rambla',
			'Rammetto One',
			'Ranchers',
			'Rancho',
			'Ranga',
			'Rasa',
			'Rationale',
			'Ravi Prakash',
			'Redressed',
			'Reem Kufi',
			'Reenie Beanie',
			'Revalia',
			'Rhodium Libre',
			'Ribeye',
			'Ribeye Marrow',
			'Righteous',
			'Risque',
			'Roboto',
			'Roboto Condensed',
			'Roboto Mono',
			'Roboto Slab',
			'Rochester',
			'Rock Salt',
			'Rokkitt',
			'Romanesco',
			'Ropa Sans',
			'Rosario',
			'Rosarivo',
			'Rouge Script',
			'Rozha One',
			'Rubik',
			'Rubik Mono One',
			'Ruda',
			'Rufina',
			'Ruge Boogie',
			'Ruluko',
			'Rum Raisin',
			'Ruslan Display',
			'Russo One',
			'Ruthie',
			'Rye',
			'Sacramento',
			'Sahitya',
			'Sail',
			'Saira',
			'Saira Condensed',
			'Saira Extra Condensed',
			'Saira Semi Condensed',
			'Salsa',
			'Sanchez',
			'Sancreek',
			'Sansita',
			'Sarabun',
			'Sarala',
			'Sarina',
			'Sarpanch',
			'Satisfy',
			'Sawarabi Gothic',
			'Sawarabi Mincho',
			'Scada',
			'Scheherazade',
			'Schoolbell',
			'Scope One',
			'Seaweed Script',
			'Secular One',
			'Sedgwick Ave',
			'Sedgwick Ave Display',
			'Sevillana',
			'Seymour One',
			'Shadows Into Light',
			'Shadows Into Light Two',
			'Shanti',
			'Share',
			'Share Tech',
			'Share Tech Mono',
			'Shojumaru',
			'Short Stack',
			'Shrikhand',
			'Siemreap',
			'Sigmar One',
			'Signika',
			'Signika Negative',
			'Simonetta',
			'Sintony',
			'Sirin Stencil',
			'Six Caps',
			'Skranji',
			'Slabo 13px',
			'Slabo 27px',
			'Slackey',
			'Smokum',
			'Smythe',
			'Sniglet',
			'Snippet',
			'Snowburst One',
			'Sofadi One',
			'Sofia',
			'Song Myung',
			'Sonsie One',
			'Sorts Mill Goudy',
			'Source Code Pro',
			'Source Sans Pro',
			'Source Serif Pro',
			'Space Mono',
			'Special Elite',
			'Spectral',
			'Spectral SC',
			'Spicy Rice',
			'Spinnaker',
			'Spirax',
			'Squada One',
			'Sree Krushnadevaraya',
			'Sriracha',
			'Srisakdi',
			'Staatliches',
			'Stalemate',
			'Stalinist One',
			'Stardos Stencil',
			'Stint Ultra Condensed',
			'Stint Ultra Expanded',
			'Stoke',
			'Strait',
			'Stylish',
			'Sue Ellen Francisco',
			'Suez One',
			'Sumana',
			'Sunflower',
			'Sunshiney',
			'Supermercado One',
			'Sura',
			'Suranna',
			'Suravaram',
			'Suwannaphum',
			'Swanky and Moo Moo',
			'Syncopate',
			'Tajawal',
			'Tangerine',
			'Taprom',
			'Tauri',
			'Taviraj',
			'Teko',
			'Telex',
			'Tenali Ramakrishna',
			'Tenor Sans',
			'Text Me One',
			'Thasadith',
			'The Girl Next Door',
			'Tienne',
			'Tillana',
			'Timmana',
			'Tinos',
			'Titan One',
			'Titillium Web',
			'Trade Winds',
			'Trirong',
			'Trocchi',
			'Trochut',
			'Trykker',
			'Tulpen One',
			'Ubuntu',
			'Ubuntu Condensed',
			'Ubuntu Mono',
			'Ultra',
			'Uncial Antiqua',
			'Underdog',
			'Unica One',
			'UnifrakturCook',
			'UnifrakturMaguntia',
			'Unkempt',
			'Unlock',
			'Unna',
			'VT323',
			'Vampiro One',
			'Varela',
			'Varela Round',
			'Vast Shadow',
			'Vesper Libre',
			'Vibur',
			'Vidaloka',
			'Viga',
			'Voces',
			'Volkhov',
			'Vollkorn',
			'Vollkorn SC',
			'Voltaire',
			'Waiting for the Sunrise',
			'Wallpoet',
			'Walter Turncoat',
			'Warnes',
			'Wellfleet',
			'Wendy One',
			'Wire One',
			'Work Sans',
			'Yanone Kaffeesatz',
			'Yantramanav',
			'Yatra One',
			'Yellowtail',
			'Yeon Sung',
			'Yeseva One',
			'Yesteryear',
			'Yrsa',
			'ZCOOL KuaiLe',
			'ZCOOL QingKe HuangYou',
			'ZCOOL XiaoWei',
			'Zeyada',
			'Zilla Slab',
			'Zilla Slab Highlight',
		)
	);
}

/**
 * Font Awesome loading trigger.
 *
 * @return bool
 */
function hestia_load_fa() {
	global $hestia_load_fa;
	$hestia_load_fa = true;
	return $hestia_load_fa;
}

/**
 * Trigger fa loading if strings in that array contains font awesome code.
 *
 * @param array | string $strings Array of strings.
 *
 * @return bool
 */
function maybe_trigger_fa_loading( $strings ) {
	global $hestia_load_fa;
	if ( $hestia_load_fa ) {
		return $hestia_load_fa;
	}

	if ( empty( $strings ) ) {
		return false;
	}

	if ( is_array( $strings ) ) {
		foreach ( $strings as $string ) {
			if ( strpos( $string, 'fa-' ) !== false ) {
				return hestia_load_fa();
			}
		}
	} else {
		if ( strpos( $strings, 'fa-' ) !== false ) {
			return hestia_load_fa();
		}
	}

	return false;
}
