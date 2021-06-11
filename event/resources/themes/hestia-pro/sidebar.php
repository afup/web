<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Hestia
 * @since Hestia 1.0.0
 * @modified 1.1.30
 */

$sidebar_class = apply_filters( 'hestia_filter_blog_sidebar_classes', 'col-md-3 blog-sidebar-wrapper' );
if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
	<div class="<?php echo esc_attr( $sidebar_class ); ?>">
		<aside id="secondary" class="blog-sidebar" role="complementary">
			<?php do_action( 'hestia_before_sidebar_content' ); ?>
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
			<?php do_action( 'hestia_after_sidebar_content' ); ?>
		</aside><!-- .sidebar .widget-area -->
	</div>
	<?php
} elseif ( is_customize_preview() ) {
	hestia_sidebar_placeholder( 'col-md-offset-1', 'sidebar-1' );
} ?>
