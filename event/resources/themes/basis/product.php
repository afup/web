<?php
/**
 * Template Name: Product Template
 *
 * @package Basis
 */
?>

<?php get_header(); ?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>
	<div class="product-content-wrapper">
		<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php remove_filter( 'the_content', 'wpautop' ); ?>
			<?php the_content(); ?>
			<?php add_filter( 'the_content', 'wpautop' ); ?>
		</div>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>