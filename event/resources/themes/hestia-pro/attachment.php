<?php
/**
 * The template for displaying attachments.
 *
 * @package Hestia
 * @since   Hestia 1.0
 */

get_header();

do_action( 'hestia_before_attachment_wrapper' )
?>
<div class="<?php echo hestia_layout(); ?>">
	<div class="blog-post blog-post-wrapper">
		<div class="container">

			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>

					<div class="entry-attachment section section-text">
						<?php
						if ( wp_attachment_is_image( $post->ID ) ) :
							$att_image = wp_get_attachment_image_src( $post->id, 'full' );
							?>
							<a href="<?php echo esc_url( wp_get_attachment_url( $post->id ) ); ?>"
									title="<?php the_title_attribute(); ?>" rel="attachment">
								<img src="<?php echo esc_url( $att_image[0] ); ?>"
										width="<?php echo esc_attr( $att_image[1] ); ?>"
										height="<?php echo esc_attr( $att_image[2] ); ?>" class="attachment-medium"
										alt="<?php esc_attr( $post->post_excerpt ); ?>"/>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( wp_get_attachment_url( $post->ID ) ); ?>"
									title="<?php the_title_attribute(); ?>" rel="attachment">
								<?php echo basename( $post->guid ); ?>
							</a>
							<?php
						endif;

						echo '<p class="sizes">';if ( wp_attachment_is_image( get_the_ID() ) ) {
							echo '<div class="image-meta">';
							echo '<i class="fas fa-camera"></i> ';
							/* translators: %s is Image sizes for attachment single page. */
							printf( esc_html__( 'Size: %s', 'hestia-pro' ), hestia_get_image_sizes() );

							echo '</div>';
						}
						echo '</p>';
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						?>
					</div>

				<?php endwhile; ?>

			<?php endif; ?>

		</div>
	</div>
</div>
<?php get_footer(); ?>
