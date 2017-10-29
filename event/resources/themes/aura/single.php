
<?php get_header(); ?>


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>



<?php    


if (has_post_format('aside')) {
         ?>
         
         
         <div id="article">
<div class="date"><?php the_date(); ?></div>
<h1></h1>
	
	<div class="aside"><?php the_content(''); ?></div>
</div><div class="clear"></div>

<?php if ( comments_open() ) : ?><div id="comments"><?php comments_template('', true); ?> </div>
</div><?php endif; ?>


         
         
<?php } elseif (has_post_format('quote')) { ?>
         
  
       <div id="article">
<div class="date"><?php the_date(); ?></div>
<h1></h1>
	
	<div class="quote"><?php the_content(''); ?></div>
</div><div class="clear"></div>

<?php if ( comments_open() ) : ?><div id="comments"><?php comments_template('', true); ?> </div>
</div><?php endif; ?>
         
         
         
         <?php } else { ?>


<div id="article">
<h1><?php the_title(); ?></h1>
<div class="date"><?php the_date(); ?></div>

  <?php if ( has_post_thumbnail() ) { ?><a href="<?php esc_url ( the_permalink() ); ?>"><div id="post-thumbnail"><?php the_post_thumbnail(); ?></div></a><?php } ?>
	
	<?php the_content(); ?>
</div><div class="clear">

<div class="tags"><?php the_tags('<span class="tags">Tags </span> ',' &bullet; ','</br>'); ?>
   </div>
   
   <div class="tags">#<?php the_category(' #'); ?>
   </div>

<div id="wp-link-pages">
 <?php wp_link_pages('before=<div class="tags"><span class="tags">Pages</span><span class="link-pages">&after=</span></div>'); ?></div>

<div id="post-links">
<div class="alignleft post-button-left"><?php previous_post_link('&larr; %link'); ?></div>    <div class="alignright post-button-right"><?php next_post_link('%link &rarr;'); ?></div>
 <div class="clear"></div>
</div>

	
	
	<?php if ( comments_open() ) : ?><div id="comments"><?php comments_template('', true); ?> </div>
</div><?php endif; ?>

</div>



<?php } ?>
  

<?php endwhile; ?> 


<?php endif; ?>


<?php get_footer(); ?>