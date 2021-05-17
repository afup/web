<?php
/**
 * The template for displaying all single portfolio posts.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

get_header();

do_action( 'hestia_before_single_post_wrapper' ); ?>

<div class="<?php echo hestia_layout(); ?>">
	<div class="blog-post blog-post-wrapper">
		<div class="container">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'portfolio' );
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				endwhile;
				else :
					get_template_part( 'template-parts/content', 'none' );
			endif;
				?>
		</div>
	</div>
</div>
<?php get_footer(); ?>
