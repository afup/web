<?php
/**
 * The default template for displaying content on page builders templates.
 *
 * Used for page builder full width and page builder blank.
 *
 * @package Hestia
 * @since Hestia 1.1.24
 * @author Themeisle
 */ ?>
<article id="post-<?php the_ID(); ?>" class="section pagebuilder-section">
	<?php the_content(); ?>
</article>
