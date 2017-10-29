<?php
/**
 * @package Basis
 */
?>

<?php get_header(); ?>

<div class="post-content">
	<div id="posts-container" class="post-wrapper">
		<header class="archive-header">
			<h3 class="archive-title"><?php basis_archives_title(); ?></h3>
			<?php get_search_form(); ?>
		</header>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( '_posts' ); ?>
			<?php endwhile; ?>
			<?php get_template_part( '_pagination', 'index' ); ?>
		<?php
		// No posts.
		else : ?>
			<?php get_template_part( '_posts', 'none' ); ?>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>