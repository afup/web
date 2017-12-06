<?php
/**
 * @package Basis
 */
?>
<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<div class="post-content">
		<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			// Show the featured image and its caption and description if they are available.
			if ( ! post_password_required() && '' !== $featured_image_id = get_post_thumbnail_id() ) :
				$attachment = get_post( $featured_image_id );
				?>
			<div class="page-header">
				<?php echo wp_get_attachment_image( $attachment->ID, 'basis-featured-page' ); ?>
				<?php if ( $attachment->post_content ) : ?>
				<div class="page-header-description">
					<?php echo wpautop( basis_allowed_tags( $attachment->post_content ) ); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="entry basis-list">
				<?php get_template_part( '_post', 'title' ); ?>
				<?php the_content(); ?>
				<?php get_template_part( '_pagination', 'single' ); ?>
			</div>
		</article>
		<?php get_sidebar( 'page' ); ?>
	</div>

	<?php comments_template(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
