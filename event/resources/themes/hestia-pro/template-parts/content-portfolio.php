<?php
/**
 * The default template for displaying content
 *
 * Used for single portfolio posts.
 *
 * @package Hestia
 * @since Hestia 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" class="section section-text">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<?php
				do_action( 'hestia_before_single_post_content' );
				the_content();
				?>
			</div>
		</div>
	</article>
<?php
if ( is_paged() ) {
	hestia_single_pagination();
}

