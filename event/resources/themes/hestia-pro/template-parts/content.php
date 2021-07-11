<?php
/**
 * The default template for displaying content
 *
 * Used for index/archive/search.
 *
 * @package Hestia
 * @since Hestia 1.0
 */

do_action( 'hestia_index_page_before_content' );
do_action( 'hestia_blog_post_template_part', 'default' );
