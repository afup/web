<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'basis_page_screen_styles' ) ) :
/**
 * Add metabox styles to the Edit Page screen
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_page_screen_styles() {
	global $typenow;
	if ( 'page' === $typenow ) { ?>
		<style type="text/css">
			#basis-template-help-link {
				padding: 0px 6px;
			}
			.basis-hide {
				display: none;
			}
		</style>
	<?php }
}
endif;

add_action( 'admin_print_styles', 'basis_page_screen_styles' );

if ( ! function_exists( 'basis_page_screen_scripts' ) ) :
/**
 * Add scripts to the Edit Page screen
 *
 * @since 1.0.
 *
 */
function basis_page_screen_scripts() {
	global $typenow;
	if ( 'page' === $typenow ) {
		wp_enqueue_script(
			'basis-page-screen',
			get_template_directory_uri() . '/includes/javascripts/page-screen.js',
			array( 'jquery' ),
			BASIS_VERSION,
			true
		);
		wp_localize_script(
			'basis-page-screen',
			'BasisPageScreenVars',
			array(
				'helplink'    => __( 'Help', 'basis' ),
				'unavailable' => __( 'This feature is unavailable for this page while using the current page template.', 'basis' )
			)
		);
	}
}
endif;

add_action( 'admin_enqueue_scripts', 'basis_page_screen_scripts' );

if ( ! function_exists( 'basis_page_help_tab' ) ) :
/**
 * Add page template help tab
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_page_help_tab() {
	$screen = get_current_screen();

	if ( 'page' !== $screen->id ) {
		return;
	}

	$args = array(
		'id'       => 'basis-page-help-tab',
		'title'    => 'Basis ' . __( 'Page Templates', 'basis' ),
		'callback' => 'basis_page_help_content'
	);

	$screen->add_help_tab( $args );
}
endif;

if ( ! function_exists( 'basis_page_help_content' ) ) :
/**
 * Callback to render the content in the page template help tab
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_page_help_content() {
?>
	<p><?php printf( __( 'Here is how to make the most out of each of the page templates in %s:', 'basis' ), 'Basis' ); ?></p>
	<p><strong><?php _e( 'Default template:', 'basis' ); ?></strong></p>
	<ol>
		<li><?php _e( 'Set a featured image. This will create a column to the left of the content where the image will appear.', 'basis' ); ?></li>
		<li><?php _e( 'Add content to the description field of the featured image, and it will appear beneath the image in the left column.', 'basis' ); ?></li>
	</ol>
	<p><strong><?php _e( 'Slideshow template:', 'basis' ); ?></strong></p>
	<ol>
		<li><?php _e( 'Add slides. For each one:', 'basis' ); ?>
			<ul>
				<li><?php _e( 'Set a background image that is at least <strong>800 pixels</strong> wide.', 'basis' ); ?></li>
				<li><?php _e( 'Add text to the Title field for a short statement in large letters. Add text to the content editor for a longer content block in a smaller font.', 'basis' ); ?></li>
				<li><?php _e( 'Add a button URL and label if you want the slide to link to somewhere else.', 'basis' ); ?></li>
			</ul>
		</li>
		<li><?php _e( 'Arrange the slides. Drag and drop them into the order you want.', 'basis' ); ?></li>
	</ol>
	<p><strong><?php _e( 'Product template:', 'basis' ); ?></strong></p>
	<ol>
		<li><?php _e( 'Add sections:', 'basis' ); ?>
			<ul>
				<li><?php _e( 'For best results and a visually interesting layout, add a variety of section types.', 'basis' ); ?></li>
				<li><?php _e( '<em>Banner</em>: Make sure the background image is at least <strong>800 pixels</strong> wide.', 'basis' ); ?></li>
				<li><?php _e( '<em>Feature</em>: Drag and drop the featured image to switch the side on which it will appear.', 'basis' ); ?></li>
				<li><?php _e( '<em>Profile</em>: Drag and drop the columns to change their order.', 'basis' ); ?></li>
			</ul>
		</li>
		<li><?php _e( 'Arrange the sections. Drag and drop them into the order you want.', 'basis' ); ?></li>
	</ol>
	<p><?php printf( __( 'Check out the <a href="%1$s" target="_blank">%2$s tutorial</a> for more information.', 'basis' ), 'http://thethemefoundry.com/tutorials/basis/', 'Basis' ); ?></p>
<?php
}
endif;

if ( ! function_exists( 'basis_page_help_loader' ) ) :
/**
 * Handler to ensure the page template help tab appears beneath the default WordPress ones.
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_page_help_loader() {
	add_action( 'admin_head', 'basis_page_help_tab' );
}
endif;

add_action( 'load-page-new.php', 'basis_page_help_loader' );
add_action( 'load-page.php', 'basis_page_help_loader' );