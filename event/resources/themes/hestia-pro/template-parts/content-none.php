<?php
/**
 * The default template for displaying content
 *
 * Used for 404 pages.
 *
 * @package Hestia
 * @since Hestia 1.0
 */
?>

<article id="post-0" class="section section-text">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<p>
					<?php
					printf(
						/* translators: %s is Link to new post */
						esc_html__( 'Ready to publish your first post? %s.', 'hestia-pro' ),
						sprintf(
							/* translators: %1$s is Link to new post, %2$s is Get started here */
							'<a href="%1$s">%2$s</a>',
							esc_url( admin_url( 'post-new.php' ) ),
							esc_html__( 'Get started here', 'hestia-pro' )
						)
					);
					?>
				</p>
				<?php
			elseif ( is_search() ) :
				do_action( 'hestia_before_search_content' );
				?>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'hestia-pro' ); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</div>
</article>
