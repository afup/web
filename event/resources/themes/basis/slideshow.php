<?php
/**
 * Template Name: Slideshow Template
 *
 * @package Basis
 */
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<div class="cycle-slideshow"<?php echo basis_get_slideshow_options( get_the_ID() ); ?>>
		<?php remove_filter( 'the_content', 'wpautop' ); ?>
		<?php the_content(); ?>
		<?php add_filter( 'the_content', 'wpautop' ); ?>
		<div class="cycle-pager"></div>
		<div class="cycle-prev"></div>
		<div class="cycle-next"></div>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>