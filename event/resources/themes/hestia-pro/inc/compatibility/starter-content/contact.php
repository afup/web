<?php
/**
 * Contact starter content.
 *
 * @package Hestia\Compatibility\Starter_Content
 */

return array(
	'post_type'    => 'page',
	'post_title'   => _x( 'Contact', 'Theme starter content' ),
	'thumbnail'    => '{{featured-image-home}}',
	'template'     => 'page-templates/template-pagebuilder-full-width.php',
	'post_content' => '<!-- wp:columns {"align":"full"} -->
		<div class="wp-block-columns alignfull"><!-- wp:column -->
		<div class="wp-block-column"><!-- wp:html -->
		<div style="width: 100%"><iframe width="100%" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=400&amp;hl=en&amp;q=povernei%2020+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe></div>
		<!-- /wp:html -->
		
		<!-- wp:paragraph -->
		<p></p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns -->
		
		<!-- wp:columns {"className":"container"} -->
		<div class="wp-block-columns container"><!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"className":"hestia-title"} -->
		<h2 class="hestia-title">Send us a message</h2>
		<!-- /wp:heading -->
		
		<!-- wp:spacer {"height":40} -->
		<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		
		<!-- wp:html -->
		<div class="contact-wrapper">
																			<div class="content">
			
			  <form>
			   <div class="form-group">
			    <input type="text" id="fname" name="firstname" placeholder="Your name..">
			</div>
			<div class="form-group">
			    <input type="text" id="lname" name="lastname" placeholder="Your last name..">
			</div>
			<div class="form-group">
			    <textarea id="subject" name="subject" placeholder="Write something.." style="height:200px"></textarea>
			</div>
			    <input type="submit" value="Submit">
			
			  </form>
			</div>
			</div>
		<!-- /wp:html --></div>
		<!-- /wp:column -->
		
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center"><!-- wp:columns -->
		<div class="wp-block-columns"><!-- wp:column -->
		<div class="wp-block-column"></div>
		<!-- /wp:column -->
		
		<!-- wp:column {"width":"50%"} -->
		<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading {"textAlign":"left","level":4,"className":"hestia-title","style":{"typography":{"fontSize":18},"color":{"text":"#3c4858"}}} -->
		<h4 class="has-text-align-left hestia-title has-text-color" style="color:#3c4858;font-size:18px"><img class="wp-image-86" style="width: 25px;" src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/contact1.png' . '" alt="Map icon">  <strong>Find us at the office</strong></h4>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph {"align":"left","style":{"typography":{"fontSize":14},"color":{"text":"#999999"}}} -->
		<p class="has-text-align-left has-text-color" style="color:#999999;font-size:14px">Strada Povernei, nr 20<br>Bucharest<br>Romania</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:spacer {"height":40} -->
		<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		
		<!-- wp:heading {"textAlign":"left","level":4,"className":"hestia-title","style":{"typography":{"fontSize":18},"color":{"text":"#3c4858"}}} -->
		<h4 class="has-text-align-left hestia-title has-text-color" style="color:#3c4858;font-size:18px"><img class="wp-image-98" style="width: 25px;" src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/contact2.png' . '" alt="">  <strong><strong>Give us a ring</strong></strong></h4>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph {"align":"left","style":{"color":{"text":"#999999"},"typography":{"fontSize":14}}} -->
		<p class="has-text-align-left has-text-color" style="color:#999999;font-size:14px">John Doe<br>+40 712 345 678<br>Mon - Fri, 8:00-22:00</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:spacer {"height":40} -->
		<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		
		<!-- wp:heading {"textAlign":"left","level":4,"className":"hestia-title","style":{"color":{"text":"#3c4858"},"typography":{"fontSize":18}}} -->
		<h4 class="has-text-align-left hestia-title has-text-color" style="color:#3c4858;font-size:18px"><img class="wp-image-105" style="width: 25px;" src="' . trailingslashit( get_template_directory_uri() ) . 'assets/img/contact3.png' . '" alt=""><strong>  <strong>Legal information</strong></strong></h4>
		<!-- /wp:heading -->
		
		<!-- wp:paragraph {"align":"left","style":{"color":{"text":"#999999"},"typography":{"fontSize":14}}} -->
		<p class="has-text-align-left has-text-color" style="color:#999999;font-size:14px">John Doe Co.<br>Fiscal Code: 12345678</p>
		<!-- /wp:paragraph --></div>
		<!-- /wp:column -->
		
		<!-- wp:column -->
		<div class="wp-block-column"></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns --></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns -->
		
		<!-- wp:paragraph -->
		<p></p>
		<!-- /wp:paragraph -->',
);
