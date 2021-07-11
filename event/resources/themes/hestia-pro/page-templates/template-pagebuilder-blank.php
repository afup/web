<?php
/**
 * Template Name: Page Builder Blank
 *
 * The template for the page builder blank.
 *
 * @package Hestia
 * @since Hestia 1.1.24
 * @author Themeisle
 */ ?>

<?php
hestia_no_content_get_header();

do_action( 'hestia_page_builder_blank_before_content' );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'pagebuilder' );
	endwhile;
endif;

do_action( 'hestia_page_builder_blank_after_content' );

hestia_no_content_get_footer();
