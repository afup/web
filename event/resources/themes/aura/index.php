
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



<?php    


if (has_post_format('aside')) {
         ?>
         
         
         <div id="article" <?php the_ID(); ?> <?php post_class(); ?>>
<div class="date"><?php the_date(); ?></div>
<h1></h1>
	
	<div class="aside"><?php the_content(''); ?></div>
</div><div class="clear"></div>

         
         
<?php } elseif (has_post_format('quote')) { ?>
         
  
       <div id="article" <?php the_ID(); ?> <?php post_class(); ?>>
<div class="date"><?php the_date(); ?></div>
<h1></h1>
	
	<div class="quote"><?php the_content(''); ?></div>
</div><div class="clear"></div>
         
         
         
         <?php } else { ?>


<div id="article" <?php the_ID(); ?> <?php post_class(); ?>>
<?php if ( is_sticky() ) : ?><div class="featured"><?php _e( 'Featured', 'aura' ); ?></div><?php endif; ?>
<h1><a class="title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
<div class="date"><?php the_date(); ?></div>

  <?php if ( has_post_thumbnail() ) { ?><a href="<?php esc_url ( the_permalink() ); ?>"><div id="post-thumbnail"><?php the_post_thumbnail(); ?></div></a><?php } ?>
	
	<?php the_content('read more'); ?>
</div><div class="clear"></div>



<?php } ?>
  

<?php endwhile; ?> 



<?php endif; ?>

<div id="buttons">
 <?php next_posts_link('<div id="posts-button">&larr; Previous Page</div>') ?>
 <?php previous_posts_link('<div id="posts-button2">Next Page &rarr;</div>'); ?>
</div><div class="clear"></div>


<?php get_footer(); ?>