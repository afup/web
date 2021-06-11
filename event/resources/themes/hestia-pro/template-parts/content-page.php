<?php
/**
 * The default template for displaying content
 *
 * Used for pages.
 *
 * @package Hestia
 * @since Hestia 1.0
 */
?>

<?php
$sidebar_layout = apply_filters( 'hestia_sidebar_layout', get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' ) );
$wrap_class     = apply_filters( 'hestia_filter_page_content_classes', 'col-md-8 page-content-wrap ' );
?>

	<article id="post-<?php the_ID(); ?>" class="section section-text">
		<div class="row">
			<?php
			if ( $sidebar_layout === 'sidebar-left' ) {
				do_action( 'hestia_page_sidebar' );
			}
			?>
			<div class="<?php echo esc_attr( $wrap_class ); ?>">
				<?php
				do_action( 'hestia_before_page_content' );

				the_content();

				echo apply_filters( 'hestia_filter_blog_social_icons', '' );

				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
			</div>
			<?php
			if ( $sidebar_layout === 'sidebar-right' ) {
				do_action( 'hestia_page_sidebar' );
			}
			?>
		</div>
	</article>
<?php
if ( is_paged() ) {
	hestia_single_pagination();
}
