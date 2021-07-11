<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package Hestia
 * @since Hestia 1.0
 * @modified 1.1.30
 */

get_header();

$default                 = hestia_get_blog_layout_default();
$sidebar_layout          = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );
$wrap_class              = apply_filters( 'hestia_filter_index_search_content_classes', 'col-md-8 blog-posts-wrap' );
$alternative_blog_layout = get_theme_mod( 'hestia_alternative_blog_layout', 'blog_normal_layout' );
$wrap_posts              = 'flex-row';
if ( Hestia_Public::should_enqueue_masonry() === true ) {
	$wrap_posts .= ' post-grid-display';
}

do_action( 'hestia_before_index_wrapper' ); ?>

<div class="<?php echo hestia_layout(); ?>">
	<div class="hestia-blogs" data-layout="<?php echo esc_attr( $sidebar_layout ); ?>">
		<div class="container">
			<?php

			do_action( 'hestia_before_index_posts_loop' );
			$paged         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			$posts_to_skip = ( $paged === 1 ) ? apply_filters( 'hestia_filter_skipped_posts_in_main_loop', array() ) : array();

			?>
			<div class="row">
				<?php
				if ( $sidebar_layout === 'sidebar-left' ) {
					get_sidebar();
				}
				?>
				<div class="<?php echo esc_attr( $wrap_class ); ?>">
					<?php
					do_action( 'hestia_before_index_content' );

					$counter = 0;
					if ( have_posts() ) {
						echo '<div class="' . esc_attr( $wrap_posts ) . '">';
						while ( have_posts() ) {
							the_post();
							$counter ++;
							$pid = get_the_ID();
							if ( ! empty( $posts_to_skip ) && in_array( $pid, $posts_to_skip, true ) ) {
								$counter ++;
								continue;
							}
							if ( $alternative_blog_layout === 'blog_alternative_layout2' ) {
								get_template_part( 'template-parts/content', 'alternative-2' );
							} elseif ( ( $alternative_blog_layout === 'blog_alternative_layout' ) && ( $counter % 2 === 0 ) ) {
								get_template_part( 'template-parts/content', 'alternative' );
							} else {
								get_template_part( 'template-parts/content' );
							}
						}
						echo '</div>';
						$hestia_pagination_type = get_theme_mod( 'hestia_pagination_type', 'number' );
						if ( $hestia_pagination_type === 'number' ) {
							do_action( 'hestia_before_pagination' );
							the_posts_pagination();
							do_action( 'hestia_after_pagination' );
						}
					} else {
						get_template_part( 'template-parts/content', 'none' );
					}
					?>
				</div>
				<?php
				if ( $sidebar_layout === 'sidebar-right' ) {
					get_sidebar();
				}
				?>
			</div>
		</div>
	</div>
	<?php do_action( 'hestia_after_archive_content' ); ?>

	<?php get_footer(); ?>
