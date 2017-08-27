
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



<div id="article">
<h1><?php the_title(); ?></h1>
<div class="date"></div>

  
	
	<?php the_content(); ?>
</div><div class="clear">
	
	
	<?php if ( comments_open() ) : ?><div id="comments"><?php comments_template('', true); ?> </div>
</div><?php endif; ?>

  

<?php endwhile; ?> 


<?php endif; ?>


<?php get_footer(); ?>