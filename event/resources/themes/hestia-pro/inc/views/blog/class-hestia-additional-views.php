<?php
/**
 * Hestia Additional Views.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Layout_Manager
 */
class Hestia_Additional_Views extends Hestia_Abstract_Main {
	/**
	 * Init layout manager.
	 */
	public function init() {
		add_action( 'hestia_after_single_post_article', array( $this, 'post_after_article' ) );

		add_action( 'hestia_blog_social_icons', array( $this, 'social_icons' ) );

		add_action( 'wp_footer', array( $this, 'scroll_to_top' ) );

		add_action( 'hestia_blog_related_posts', array( $this, 'related_posts' ) );

		add_action( 'hestia_before_header_hook', array( $this, 'hidden_sidebars' ) );
	}

	/**
	 * Social sharing icons for single view.
	 *
	 * @since Hestia 1.0
	 */
	public function social_icons() {
		$enabled_socials = get_theme_mod( 'hestia_enable_sharing_icons', true );
		if ( (bool) $enabled_socials !== true ) {
			return;
		}

		$post_link  = esc_url( get_the_permalink() );
		$post_title = get_the_title();

		$facebook_url = add_query_arg(
			array(
				'u' => $post_link,
			),
			'https://www.facebook.com/sharer.php'
		);

		$twitter_url = add_query_arg(
			array(
				'url'  => $post_link,
				'text' => rawurlencode( html_entity_decode( wp_strip_all_tags( $post_title ), ENT_COMPAT, 'UTF-8' ) ),
			),
			'http://twitter.com/share'
		);

		$email_title = str_replace( '&', '%26', $post_title );

		$email_url = add_query_arg(
			array(
				'subject' => wp_strip_all_tags( $email_title ),
				'body'    => $post_link,
			),
			'mailto:'
		);

		$social_links = '
        <div class="col-md-6">
            <div class="entry-social">
                <a target="_blank" rel="tooltip"
                   data-original-title="' . esc_attr__( 'Share on Facebook', 'hestia-pro' ) . '"
                   class="btn btn-just-icon btn-round btn-facebook"
                   href="' . esc_url( $facebook_url ) . '">
                   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="20" height="17"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path></svg>
                </a>
                
                <a target="_blank" rel="tooltip"
                   data-original-title="' . esc_attr__( 'Share on Twitter', 'hestia-pro' ) . '"
                   class="btn btn-just-icon btn-round btn-twitter"
                   href="' . esc_url( $twitter_url ) . '">
                   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="17"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>
                </a>
                
                <a rel="tooltip"
                   data-original-title=" ' . esc_attr__( 'Share on Email', 'hestia-pro' ) . '"
                   class="btn btn-just-icon btn-round"
                   href="' . esc_url( $email_url ) . '">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="17"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
               </a>
            </div>
		</div>';
		echo apply_filters( 'hestia_filter_blog_social_icons', $social_links );
	}

	/**
	 * Single post after article.
	 */
	public function post_after_article() {
		global $post;
		$categories = get_the_category( $post->ID );
		?>

		<div class="section section-blog-info">
			<div class="row">
				<div class="col-md-6">
					<div class="entry-categories"><?php esc_html_e( 'Categories:', 'hestia-pro' ); ?>
						<?php
						foreach ( $categories as $category ) {
							echo '<span class="label label-primary"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></span>';
						}
						?>
					</div>
					<?php the_tags( '<div class="entry-tags">' . esc_html__( 'Tags: ', 'hestia-pro' ) . '<span class="entry-tag">', '</span><span class="entry-tag">', '</span></div>' ); ?>
				</div>
				<?php do_action( 'hestia_blog_social_icons' ); ?>
			</div>
			<hr>
			<?php
			$this->maybe_render_author_box();
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>
		</div>
		<?php
	}


	/**
	 * Render the author box.
	 */
	private function maybe_render_author_box() {
		$author_description = get_the_author_meta( 'description' );
		if ( empty( $author_description ) ) {
			return;
		}
		?>
		<div class="card card-profile card-plain">
			<div class="row">
				<div class="col-md-2">
					<div class="card-avatar">
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"
								title="<?php echo esc_attr( get_the_author() ); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></a>
					</div>
				</div>
				<div class="col-md-10">
					<h4 class="card-title"><?php the_author(); ?></h4>
					<p class="description"><?php the_author_meta( 'description' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display scroll to top button.
	 *
	 * @since 1.1.54
	 */
	public function scroll_to_top() {
		$hestia_enable_scroll_to_top = get_theme_mod( 'hestia_enable_scroll_to_top', apply_filters( 'hestia_scroll_to_top_default', 0 ) );
		if ( (bool) $hestia_enable_scroll_to_top === false ) {
			return;
		}
		?>

		<button class="hestia-scroll-to-top">
			<svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="12.5px" height="20px"><path d="M177 255.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 351.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 425.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1zm-34-192L7 199.7c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l96.4-96.4 96.4 96.4c9.4 9.4 24.6 9.4 33.9 0l22.6-22.6c9.4-9.4 9.4-24.6 0-33.9l-136-136c-9.2-9.4-24.4-9.4-33.8 0z"></path></svg>
		</button>
		<?php
	}

	/**
	 * Related posts for single view.
	 *
	 * @since Hestia 1.0
	 */
	public function related_posts() {
		global $post;
		$cats         = wp_get_object_terms(
			$post->ID,
			'category',
			array(
				'fields' => 'ids',
			)
		);
		$args         = array(
			'posts_per_page'      => 3,
			'cat'                 => $cats,
			'orderby'             => 'date',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array( $post->ID ),
		);
		$allowed_html = array(
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'i'      => array(
				'class' => array(),
			),
			'span'   => array(),
		);

		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) :
			?>
			<div class="section related-posts">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<h2 class="hestia-title text-center"><?php echo apply_filters( 'hestia_related_posts_title', esc_html__( 'Related Posts', 'hestia-pro' ) ); ?></h2>
							<div class="row">
								<?php
								while ( $loop->have_posts() ) :
									$loop->the_post();
									?>
									<div class="col-md-4">
										<div class="card card-blog">
											<?php if ( has_post_thumbnail() ) : ?>
												<div class="card-image">
													<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
														<?php the_post_thumbnail( 'hestia-blog' ); ?>
													</a>
												</div>
											<?php endif; ?>
											<div class="content">
												<h6 class="category text-info"><?php echo hestia_category( false ); ?></h6>
												<h4 class="card-title">
													<a class="blog-item-title-link" href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
														<?php echo wp_kses( force_balance_tags( get_the_title() ), $allowed_html ); ?>
													</a>
												</h4>
												<p class="card-description"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
											</div>
										</div>
									</div>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		endif;
	}

	/**
	 * Display the hidden sidebars to enable the customizer panels.
	 */
	public function hidden_sidebars() {
		echo '<div style="display: none">';
		if ( is_customize_preview() ) {
			dynamic_sidebar( 'sidebar-top-bar' );
			dynamic_sidebar( 'header-sidebar' );
			dynamic_sidebar( 'subscribe-widgets' );
			dynamic_sidebar( 'sidebar-big-title' );
		}
		echo '</div>';
	}

}
