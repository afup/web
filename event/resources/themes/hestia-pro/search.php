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

$default                    = hestia_get_blog_layout_default();
$hestia_blog_sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_blog_sidebar_layout', $default ) );
$wrap_class                 = apply_filters( 'hestia_filter_index_search_content_classes', 'col-md-8 blog-posts-wrap' );
get_header();

do_action( 'hestia_before_search_wrapper' );
?>

<div class="<?php echo hestia_layout(); ?>">
	<div class="hestia-blogs" data-layout="<?php echo esc_attr( $hestia_blog_sidebar_layout ); ?>">
		<div class="container">
			<div class="row">
				<?php
				do_action( 'hestia_before_search_content' );

				if ( $hestia_blog_sidebar_layout === 'sidebar-left' ) {
					get_sidebar();
				}
				?>
				<div class="<?php echo esc_attr( $wrap_class ); ?>">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content' );
						endwhile;
						the_posts_pagination();
						else :
							get_template_part( 'template-parts/content', 'none' );
					endif;
						?>
				</div>
				<?php
				if ( $hestia_blog_sidebar_layout === 'sidebar-right' ) {
					get_sidebar();
				}
				?>
			</div>
		</div>
	</div>
	<?php get_footer(); ?>
