<?php
/**
 * Home starter content.
 *
 * @package Hestia\Compatibility\Starter_Content
 */
$is_pro               = class_exists( 'Hestia_Addon_Manager' );
$default_home_content = $is_pro ?
	'<div class="col-md-5"><h3>' . esc_html__( 'About Hestia', 'hestia' ) . '</h3>' . esc_html__( 'Need more details? Please check our full documentation for detailed information on how to use Hestia.', 'hestia' ) . '</div><div class="col-md-6 col-md-offset-1"><img class="size-medium alignright" src="' . esc_url( get_template_directory_uri() . '/assets/img/about-content.png' ) . '"/></div>' :
	'<!-- wp:cover {"overlayColor":"white","align":"full","className":"hestia-clear-top-padding"} -->
	<div class="wp-block-cover alignfull has-white-background-color has-background-dim hestia-clear-top-padding"><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","style":{"color":{"text":"#3c4858"}}} -->
	<h2 class="has-text-align-center has-text-color" style="color:#3c4858">What we stand for</h2>
	<!-- /wp:heading -->
	
	<!-- wp:spacer {"height":40} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:columns {"className":"container"} -->
	<div class="wp-block-columns container"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:image {"align":"center","id":70,"sizeSlug":"large","linkDestination":"none"} -->
	<div class="wp-block-image"><figure class="aligncenter size-large"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/feat1.png' . '" alt="" class="wp-image-70"/></figure></div>
	<!-- /wp:image -->
	
	<!-- wp:heading {"textAlign":"center","level":5,"style":{"color":{"text":"#3c4858"},"typography":{"fontSize":18}}} -->
	<h5 class="has-text-align-center has-text-color" style="color:#3c4858;font-size:18px">Responsive</h5>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#999999"},"typography":{"fontSize":14}}} -->
	<p class="has-text-align-center has-text-color" style="color:#999999;font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
	<!-- /wp:paragraph -->
	
	<!-- wp:spacer {"height":25} -->
	<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:image {"align":"center","id":71,"sizeSlug":"large","linkDestination":"none"} -->
	<div class="wp-block-image"><figure class="aligncenter size-large"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/feat2.png' . '" alt="" class="wp-image-71"/></figure></div>
	<!-- /wp:image -->
	
	<!-- wp:heading {"textAlign":"center","level":5,"style":{"color":{"text":"#3c4858"},"typography":{"fontSize":18}}} -->
	<h5 class="has-text-align-center has-text-color" style="color:#3c4858;font-size:18px">Quality</h5>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#999999"},"typography":{"fontSize":14}}} -->
	<p class="has-text-align-center has-text-color" style="color:#999999;font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
	<!-- /wp:paragraph -->
	
	<!-- wp:spacer {"height":25} -->
	<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:image {"align":"center","id":72,"sizeSlug":"large","linkDestination":"none"} -->
	<div class="wp-block-image"><figure class="aligncenter size-large"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/feat3.png' . '" alt="" class="wp-image-72"/></figure></div>
	<!-- /wp:image -->
	
	<!-- wp:heading {"textAlign":"center","level":5,"style":{"color":{"text":"#3c4858"},"typography":{"fontSize":18}}} -->
	<h5 class="has-text-align-center has-text-color" style="color:#3c4858;font-size:18px">Support</h5>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#999999"},"typography":{"fontSize":14}}} -->
	<p class="has-text-align-center has-text-color" style="color:#999999;font-size:14px">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
	<!-- /wp:paragraph -->
	
	<!-- wp:spacer {"height":25} -->
	<div style="height:25px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->
	
	<!-- wp:spacer {"height":40} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer --></div></div>
	<!-- /wp:cover -->
	
	<!-- wp:columns {"className":"container"} -->
	<div class="wp-block-columns container"><!-- wp:column {"className":"col-md-5"} -->
	<div class="wp-block-column col-md-5"><!-- wp:spacer {"height":55} -->
	<div style="height:55px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:heading -->
	<h2>About Hestia</h2>
	<!-- /wp:heading -->
	
	<!-- wp:paragraph -->
	<p>Need more details? Please check our full documentation for detailed information on how to use Hestia.</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column {"className":"col-md-6 col-md-offset1"} -->
	<div class="wp-block-column col-md-6 col-md-offset1"><!-- wp:spacer {"height":55} -->
	<div style="height:55px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->
	
	<!-- wp:image {"id":81,"sizeSlug":"large","linkDestination":"none"} -->
	<figure class="wp-block-image size-large"><img src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/about-content.png' . '" alt="" class="wp-image-81"/></figure>
	<!-- /wp:image -->
	
	<!-- wp:paragraph -->
	<p></p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns -->';

return array(
	'post_type'    => 'page',
	'post_title'   => _x( 'Home', 'Theme starter content' ),
	'post_content' => $default_home_content,
);
