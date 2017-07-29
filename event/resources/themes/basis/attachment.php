<?php
/**
 * @package Basis
 */
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); global $post; ?>
	<div class="post-content">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="attachment-header pagination">
				<?php if ( 0 !== $post->post_parent ) : ?>
				<div class="alignleft attachment-return">
					<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>">
						<?php _e( 'Return', 'basis' ); ?>
					</a>
				</div>
				<?php endif; ?>
			</div>
			<div class="attachment-image">
				<a href="<?php echo esc_url( wp_get_attachment_url( get_the_ID() ) ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment">
					<?php
					if ( wp_attachment_is_image ( get_the_ID() ) ) :
						$img_src  = wp_get_attachment_image_src( get_the_ID(), 'large' );
						$alt_text = get_post_meta( get_the_ID(), '_wp_attachment_image_alt', true );
						?>
						<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" />
					<?php
					else :
						echo basename( $post->guid );
					endif;
					?>
				</a>
			</div>
			<div class="entry">
				<h3><?php the_title(); ?></h3>
				<?php the_content(); ?>
			</div>
			<div class="pagination">
				<div class="alignleft"><?php previous_image_link( 0, __( 'Previous', 'basis' ) ); ?></div>
				<div class="alignright"><?php next_image_link( 0, __( 'Next', 'basis' ) ); ?></div>
			</div>
		</article>
	</div>
<?php endwhile; ?>
<?php get_footer(); ?>