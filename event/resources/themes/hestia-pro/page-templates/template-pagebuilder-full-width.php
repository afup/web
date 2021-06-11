<?php
/**
 * Template Name: Page Builder Full Width
 *
 * The template for the page builder full-width.
 *
 * It contains header, footer and 100% content width.
 *
 * @package Hestia
 * @since Hestia 1.1.24
 * @author Themeisle
 */

get_header(); ?>

<?php do_action( 'hestia_page_builder_full_before_content' ); ?>

<div class="<?php echo hestia_layout(); ?>">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'pagebuilder' );
		endwhile;
	endif;
	?>
</div>

<?php do_action( 'hestia_page_builder_full_after_content' ); ?>

<?php get_footer(); ?>
