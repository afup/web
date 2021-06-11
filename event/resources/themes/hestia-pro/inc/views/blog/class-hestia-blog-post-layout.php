<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      26/07/2018
 *
 * @soundtrack      Into the Sun (EP Version) - Sons Of The East
 * @package         Hestia
 */

/**
 * Class Hestia_Blog_Post_Layout
 */
class Hestia_Blog_Post_Layout {

	/**
	 * Initialize the post layout manager.
	 */
	public function init() {
		add_action( 'hestia_blog_post_template_part', array( $this, 'render' ), 2 );
	}

	/**
	 * Main render function.
	 *
	 * @param string $layout post layout.
	 */
	public function render( $layout ) {
		$pid           = get_the_ID();
		$article_class = $this->get_article_class( $layout );
		$wrapper_class = $this->get_wrapper_class( $layout );
		$row_class     = 'row ';
		if ( $layout === 'alt-1' ) {
			$row_class = 'row alternative-blog-row ';
		}

		$settings = array(
			'pid'           => $pid,
			'article_class' => $article_class,
			'wrapper_class' => $wrapper_class,
			'row_class'     => $row_class,
			'layout'        => $layout,
		);

		echo $this->get_article( $settings );
	}

	/**
	 * Get an article.
	 *
	 * @param array $args Article arguments.
	 */
	public function get_article( $args = array() ) {

		$article_template = '';

		$article_template .= '<article 
		id="post-' . esc_attr( $args['pid'] ) . '" 
		class="' . join( ' ', get_post_class( $args['article_class'], $args['pid'] ) ) . '">';
		$article_template .= '<div class="' . esc_attr( $args['row_class'] ) . '">';

		if ( $args['layout'] === 'default' ) {
			$article_template .= $this->render_post_thumbnail();
		}
		if ( $args['layout'] !== 'alt-2' ) {
			$article_template .= '<div class= "' . esc_attr( $args['wrapper_class'] ) . '">';
			$article_template .= $this->render_post_body();
			$article_template .= '</div>';
		}

		if ( $args['layout'] === 'alt-1' ) {
			$article_template .= $this->render_post_thumbnail();
		}
		if ( $args['layout'] === 'alt-2' ) {
			$article_template .= '<div class= "' . esc_attr( $args['wrapper_class'] ) . '">';
			$article_template .= $this->render_post_thumbnail( $args['layout'] );
			$article_template .= $this->render_post_body( $args['layout'] );
			$article_template .= '</div>';
		}
		$article_template .= '</div>';
		$article_template .= '</article>';

		return $article_template;
	}

	/**
	 * Render alternative post thumbnail
	 *
	 * @param string $type layout type [default | alt-2 ].
	 */
	private function render_post_thumbnail( $type = 'default' ) {
		if ( ! $this->is_valid_layout_type( $type ) ) {
			return '';
		}
		if ( ! has_post_thumbnail() ) {
			return '';
		}

		$post_thumbnail_content = '';
		$size                   = 'hestia-blog';
		$wrap_class             = 'col-ms-5 col-sm-5';
		if ( $type === 'alt-2' ) {
			$size       = 'medium_large';
			$wrap_class = 'card-header card-header-image';
		}

		$post_thumbnail_content .= '<div class="' . esc_attr( $wrap_class ) . '">';
		$post_thumbnail_content .= '<div class="card-image">';
		$post_thumbnail_content .= '<a href="' . esc_url( get_the_permalink() ) . '" title="' . the_title_attribute(
			array(
				'echo' => false,
			)
		) . '">';
		$post_thumbnail_content .= get_the_post_thumbnail( null, $size );
		$post_thumbnail_content .= '</a>';
		$post_thumbnail_content .= '</div>';
		$post_thumbnail_content .= '</div>';

		return $post_thumbnail_content;
	}

	/**
	 * Get article class names.
	 *
	 * @return string
	 */
	private function get_article_class( $layout ) {
		$classes = '';
		switch ( $layout ) {
			case 'default':
			case 'alt-1':
				$classes  = 'card card-blog';
				$classes .= ( is_sticky() && is_home() && ! is_paged() ? ' card-raised' : ' card-plain' );
				break;
			case 'alt-2':
				$grid_layout = get_theme_mod( 'hestia_grid_layout', 1 );
				$card_type   = 'card-no-width';
				if ( $grid_layout === 1 ) {
					$card_type = 'card';
				}

				$classes = $card_type . ' card-plain card-blog';

				if ( ! $this->is_full_content() ) {
					$classes .= ' text-center';
				}

				$blog_layout = get_theme_mod( 'hestia_alternative_blog_layout', 'blog_normal_layout' );
				if ( $blog_layout !== 'blog_alternative_layout2' ) {
					return $classes;
				}

				if ( $grid_layout === 1 ) {
					return $classes . ' layout-alternative2 ';
				}

				$classes .= ' layout-alternative2 col-sm-12 col-md-' . ( 12 / $grid_layout );
				break;

		}

		return $classes;
	}

	/**
	 * Wrapper classes for alternative layout
	 */
	private function get_wrapper_class( $layout ) {
		$classes = '';
		switch ( $layout ) {
			case 'default':
			case 'alt-1':
				$classes = has_post_thumbnail() ? 'col-ms-7 col-sm-7' : 'col-sm-12';
				break;
			case 'alt-2':
				$classes = 'col-md-12';
				if ( is_sticky() && is_home() && ! is_paged() ) {
					$classes .= ' card featured-alt-2';
				}
				break;
		}

		return $classes;
	}

	/**
	 * Render post body.
	 *
	 * @param string $type the type of post.
	 */
	private function render_post_body( $type = 'default' ) {
		if ( ! $this->is_valid_layout_type( $type ) ) {
			return '';
		}

		$post_body_content = '';
		if ( $type === 'alt-2' ) {
			$post_body_content .= '<div class="card-body">';
		}
		$post_body_content .= '<h6 class="category text-info">';
		$post_body_content .= hestia_category();
		$post_body_content .= '</h6>';

		$post_body_content .= the_title(
			sprintf(
				'<h2 class="card-title entry-title"><a href="%s" title="%s" rel="bookmark">',
				esc_url( get_permalink() ),
				the_title_attribute(
					array(
						'echo' => false,
					)
				)
			),
			'</a></h2>',
			false
		);

		$excerpt_class = $this->is_full_content() ? 'entry-content' : 'entry-summary';

		$post_body_content .= '<div class="card-description ' . $excerpt_class . ' ">';
		$post_body_content .= $this->get_theme_excerpt( $type );
		$post_body_content .= '</div>';

		if ( $type === 'default' ) {
			$post_body_content .= $this->render_post_meta();
		}

		if ( $type === 'alt-2' ) {
			$post_body_content .= $this->render_read_more_button();
			$post_body_content .= '</div>';
		}

		return $post_body_content;
	}

	/**
	 * Get post excerpt.
	 *
	 * @param string $type Blog post layout type.
	 *
	 * @return string
	 */
	private function get_theme_excerpt( $type ) {
		global $post;

		if ( is_post_type_archive() ) {
			return get_the_excerpt();
		}

		$content = $this->get_post_content();

		/**
		 * Return full content if the option is set in customizer
		 */
		if ( $this->is_full_content() ) {
			return $content;
		}

		/**
		 * Check for excerpt
		 */
		if ( has_excerpt( $post->ID ) ) {
			return apply_filters( 'the_excerpt', get_the_excerpt() );
		}

		/**
		 * Check for more tag
		 */
		$hestia_more = strpos( $post->post_content, '<!--more' );
		if ( ! empty( $hestia_more ) ) {
			return ( $type === 'alt-2' ? preg_replace( '/<a (.*?)class="more-link"(.*?)>(.*?)<\/a>/i', '...', $content ) : $content );
		}

		return apply_filters( 'the_excerpt', get_the_excerpt() );
	}

	/**
	 * Get post content.
	 */
	private function get_post_content() {
		$content = get_the_content();
		$content = apply_filters( 'the_content', $content );
		$content = strip_shortcodes( $content );

		return $content;
	}

	/**
	 * Render post meta.
	 */
	private function render_post_meta() {
		$post_meta_content  = '';
		$post_meta_content .= '<div class="posted-by vcard author">';
		$post_meta_content .= apply_filters(
			'hestia_blog_post_meta',
			sprintf(
				/* translators: %1$s is Author name wrapped, %2$s is Time */
				esc_html__( 'By %1$s, %2$s', 'hestia-pro' ),
				sprintf(
					/* translators: %1$s is Author name, %2$s is author link */
					'<a href="%2$s" title="%1$s" class="url"><b class="author-name fn">%1$s</b></a>',
					esc_html( get_the_author() ),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) )
				),
				sprintf(
					/* translators: %1$s is Time since post, %2$s is author Close tag */
					esc_html__( '%1$s ago %2$s', 'hestia-pro' ),
					sprintf(
						/* translators: %1$s is Time since, %2$s is Link to post */
						'<a href="%2$s">%1$s',
						$this->get_time_tags(),
						esc_url( get_permalink() )
					),
					'</a>'
				)
			)
		);
		$post_meta_content .= '</div>';

		return $post_meta_content;
	}

	/**
	 * Get <time> tags.
	 *
	 * @return string
	 */
	private function get_time_tags() {
		$time = '';

		$time .= '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" content="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">';
		$time .= esc_html( human_time_diff( get_the_time( 'U' ), time() ) );
		$time .= '</time>';
		if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) {
			return $time;
		}
		$time .= '<time class="updated hestia-hidden" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">';
		$time .= esc_html( human_time_diff( get_the_modified_date( 'U' ), time() ) );
		$time .= '</time>';

		return $time;
	}

	/**
	 * Render Read More button.
	 */
	private function render_read_more_button() {
		if ( $this->is_full_content() ) {
			return '';
		}
		$read_more_button  = '';
		$read_more_button .= '<div class="text-center">';
		$read_more_button .= '<a href="' . esc_url( get_the_permalink() ) . '" class="btn colored-button">';
		$read_more_button .= apply_filters( 'hestia_blog_posts_button_text', esc_html__( 'Read more', 'hestia-pro' ) );
		$read_more_button .= '</a>';
		$read_more_button .= '</div>';

		return $read_more_button;
	}

	/**
	 * Check if full content is displayed.
	 *
	 * @return bool
	 */
	private function is_full_content() {
		$content_type = get_theme_mod( 'hestia_blog_post_content_type', 'excerpt' );
		if ( $content_type === 'content' ) {
			return true;
		}

		return false;
	}

	/**
	 * Utility to check if layout is allowed.
	 *
	 * @param string $type the type of layout to check.
	 *
	 * @return bool
	 */
	private function is_valid_layout_type( $type ) {
		$allowed_layouts = array(
			'default',
			'alt-2',
		);
		if ( in_array( $type, $allowed_layouts, true ) ) {
			return true;
		}

		return false;
	}
}
