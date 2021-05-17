<?php
/**
 * Template Name: Fullwidth Template
 *
 * The template for the full-width page.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

get_header();

/**
 * Don't display page header if header layout is set as classic blog.
 */
do_action( 'hestia_before_single_page_wrapper' ); ?>

<div class="<?php echo hestia_layout(); ?>">
	<?php
	$class_to_add = '';
	if ( class_exists( 'WooCommerce', false ) && ! is_cart() ) {
		$class_to_add = 'blog-post-wrapper';
	}
	?>
	<div class="blog-post <?php echo esc_attr( $class_to_add ); ?>">
		<div class="container">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content-fullwidth', 'page' );
				endwhile;
				else :
					get_template_part( 'template-parts/content', 'none' );
			endif;
				?>
		</div>
	</div>

	<?php get_footer(); ?>
