<?php
/**
 * @package Basis
 */
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<?php
	// If the featured image is large enough, show it as a full-width background
	if ( $background_image = basis_is_thumbnail_wide_enough( get_post_thumbnail_id(), 800, 'basis-featured-single' ) ) : ?>
	<div class="post-background" style='background-image: url("<?php echo addcslashes( esc_url_raw( $background_image[0] ), '"' ); ?>");'></div>
	<?php endif; ?>
	<div class="post-content main">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-header">
				<p class="post-date basis-secondary-text"><?php echo get_the_date( basis_get_date_format() ); ?></p>
				<p class="post-author"><?php the_author_posts_link(); ?></p>
				<?php get_template_part( '_post', 'commentlink' ); ?>
			</div>
			<div class="entry basis-list">
				<?php get_template_part( '_post', 'title' ); ?>
				<div class="mobile-post-header">
					<?php get_template_part( '_post', 'commentlink' ); ?>
					<p class="post-date basis-secondary-text"><?php echo get_the_date( basis_get_date_format() ); ?></p>
					<p class="post-author"><?php the_author_posts_link(); ?></p>
				</div>
				<?php get_template_part( '_post', 'content' ); ?>
				<?php get_template_part( '_pagination', 'single' ); ?>

				<?php if ( has_tag() || has_category() ) : ?>
					<div class="post-footer">
						<?php get_template_part( '_post', 'category' ); ?>
						<?php get_template_part( '_post', 'tag' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	</div>
	<?php if ( ( get_adjacent_post( false, '', true ) || get_adjacent_post( false, '', false ) ) ) : ?>
		<div class="post-navigation">
			<div class="post-content">
				<nav class="pagination">
					<div class="alignleft" title="<?php esc_attr_e( 'Read next post', 'basis' ); ?>"><?php next_post_link( '%link', __( 'Next post', 'basis' ) ); ?></div>
					<div class="alignright" title="<?php esc_attr_e( 'Read previous post', 'basis' ); ?>"><?php previous_post_link( '%link', __( 'Previous post', 'basis' ) ); ?></div>
				</nav>
			</div>
		</div>
	<?php endif; ?>
	<?php comments_template(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
