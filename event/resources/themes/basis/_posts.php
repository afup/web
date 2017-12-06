<?php
/**
 * @package Basis
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> xmlns="http://www.w3.org/1999/html">
	<?php
	// Add this post header when there is no sidebar and hide the other one with CSS.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) : ?>
	<div class="post-header post-header-fullwidth">
		<?php get_template_part( '_post', 'thumbnail' ); ?>
		<?php get_template_part( '_post', 'sticky' ); ?>
		<p class="post-date basis-secondary-text">
			<a class="basis-secondary-text" href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Read the full post', 'basis' ); ?>" rel="bookmark">
				<?php echo get_the_date( basis_get_date_format() ); ?>
			</a>
		</p>
		<p class="post-author"><?php the_author_posts_link(); ?></p>
		<?php get_template_part( '_post', 'commentlink' ); ?>
	</div>
	<?php endif; ?>
	<div class="entry basis-list">
		<div class="post-header post-header-sidebar">
			<?php get_template_part( '_post', 'thumbnail' ); ?>
		</div>
		<?php get_template_part( '_post', 'title' ); ?>
		<?php // The post header when the sidebar is active, and when in narrow view. ?>
		<div class="post-header post-header-sidebar">
			<?php get_template_part( '_post', 'sticky' ); ?>
			<?php get_template_part( '_post', 'commentlink' ); ?>
			<span class="post-date basis-secondary-text">
				<a class="basis-secondary-text" href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Read the full post', 'basis' ); ?>" rel="bookmark">
					<?php echo get_the_date( basis_get_date_format() ); ?>
				</a>
			</span>
			<span class="post-author"><?php the_author_posts_link(); ?></span>
		</div>
		<?php get_template_part( '_post', 'content' ); ?>
	</div>
</article>