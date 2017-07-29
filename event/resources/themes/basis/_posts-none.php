<?php
/**
 * @package Basis
 */
?>

<article <?php post_class(); ?>>
	<?php
	// Add this post header when there is no sidebar and hide the other one with CSS.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div class="post-header post-header-fullwidth"></div>
	<?php endif; ?>
	<div class="entry">
	<?php if ( is_search() ) : ?>
		<p><?php printf( __( 'Sorry, your search for &#8216;%s&#8217; did not return any results.', 'basis' ), get_search_query() );?></p>
	<?php else : ?>
		<p><?php _e( 'Sorry, there is no content here.', 'basis' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
	</div>
</article>