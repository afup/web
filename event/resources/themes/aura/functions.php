<?php

// Basic Setup

add_editor_style();
add_theme_support('post-thumbnails');
add_theme_support( 'automatic-feed-links' );
set_post_thumbnail_size( 900, 300, true );
load_theme_textdomain( 'aura', get_template_directory() . '/languages' );
if ( ! isset( $content_width ) ) $content_width = 700;


add_theme_support( 'post-formats', array( 'aside', 'link', 'quote', 'image' ) );


// wp_title filter

function aura_filter_wp_title( $title ) {
 $aura_site_name = get_bloginfo( 'name' );
 $aura_filtered_title = $aura_site_name . $title;
 return $aura_filtered_title;
}

add_filter( 'wp_title', 'aura_filter_wp_title' );

        
        
// Filter for Untitled Articles
        
add_filter('the_title', 'aura_title');
function aura_title($title) {
	if ( $title == '' ) {
		return __( 'Untitled', 'aura');
	} else {
		return $title;
	}
}
        
        

// Stylesheets

function aura_style() {
 wp_register_style('aura_style', get_stylesheet_uri(), array(), 1.0, 'all'); 	 
 wp_enqueue_style('aura_style');
 wp_enqueue_script( 'comment-reply' );
}


add_filter( 'use_default_gallery_style', '__return_false' );


// Nav

// Custom Menus
 
function aura_nav() {
    register_nav_menus(array(
        'header-nav' => 'Header Menu',
    ));
}
add_action('init', 'aura_nav');



// Comment Form

if ( ! function_exists( 'aura_comment' ) ) :

function aura_comment($comment, $args, $depth) {
$GLOBALS['comment'] = $comment;


if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'div';
			$add_below = 'div-comment';
		}
		
		
	switch ( $comment->comment_type ) :
		case '' :
	?>
	
	
	
<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">

		<?php if ( 'div' != $args['style'] ) : ?>
		<div class="comment">
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>
		
	
		
		
		<div class="comment-box">
		<div class="comment-name"><?php printf(__('%s', 'aura'), get_comment_author_link()) ?></div>
		<div class="comment-date"><?php
		
				printf( __('%1$s @ %2$s', 'aura'), get_comment_date(),  get_comment_time()) ?></div>

		<div class="comment-text">
		<?php if ($comment->comment_approved == '0') : ?>
		<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'aura') ?></em></br>
		<?php endif; ?>
		
		<?php comment_text(); ?>
		
		<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		
		<div class="comment-reply">
			
			</div>
			



<div class="comment-line"></div>
		</div></div></div></div>
		


	</div><div style="clear:both;"></div>
	
	

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'aura' ); ?> <?php comment_author_link(); ?></p>
	<?php
			break;
	endswitch;
}
endif;


// Sidebar

function aura_widgets_init() {

register_sidebar(array(
'name' => __('Sidebar', 'aura'),

'before_widget' => '<div class="widget %2$s">',
'after_widget' => '</div>',
'before_title' => '<div class="sidebar-headline">',
'after_title' => '</div></p>',
));

}

add_action( 'widgets_init', 'aura_widgets_init' );

add_action('admin_menu', 'aura_create_menu');

function aura_create_menu() {
   add_theme_page('Other Fimply Themes', 'Other Fimply Themes', 'edit_theme_options', 'fimplythemes', 'aura_other_themes');
}


function aura_other_themes () {
	locate_template( array( '/inc/other-themes.php' ), true );
}

function aura_admin_enqueue() {
 
   wp_enqueue_style( 'admin_style', get_template_directory_uri() . '/css/options.css', array(), null, 'all' );
}

add_action( 'admin_enqueue_scripts', 'aura_admin_enqueue' );
add_action('wp_enqueue_scripts', 'aura_style');



 ?>