<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'basis_customizer_admin_styles' ) ) :
/**
 * Styles for our Customizer sections and controls. Prints in the <head>
 *
 * @since 1.1.0
 */
function basis_customizer_admin_styles() { ?>
	<style type="text/css">
		.customize-control.customize-control-heading {
			margin-bottom: -2px;
		}
	</style>
<?php }
endif;

add_action( 'customize_controls_print_styles', 'basis_customizer_admin_styles' );

if ( ! function_exists( 'basis_get_social_links' ) ) :
/**
 * Get the social links from options.
 *
 * @since  1.0.
 *
 * @return array    Keys are service names and the values are links.
 */
function basis_get_social_links() {
	// Define default services; note that these are intentional non-translatable
	$default_services = array(
		'twitter' => array(
			'title' => 'Twitter',
		),
		'facebook' => array(
			'title' => 'Facebook',
		),
		'google' => array(
			'title' => 'Google+',
		),
		'flickr' => array(
			'title' => 'Flickr',
		),
		'pinterest' => array(
			'title' => 'Pinterest',
		),
		'linkedin' => array(
			'title' => 'LinkedIn',
		),
		'rss' => array(
			'title' => __( 'RSS', 'basis' ),
		),
	);

	// Set up the collector array
	$services_with_links = array();

	// Get the links for these services
	foreach ( $default_services as $service => $details ) {
		$url = get_theme_mod( $service, '' );
		if ( '' !== $url ) {
			$services_with_links[ $service ] = array(
				'title' => $details['title'],
				'url'   => $url,
			);
		}
	}

	return apply_filters( 'basis_social_links', $services_with_links );
}
endif;

if ( ! function_exists( 'basis_sanitize_mod_footer_widgets' ) ) :
/**
 * Sanitize theme mod footer-widgets to only allow the options in the dropdown.
 *
 * @since 1.0.
 *
 * @param  string $choice The string to sanitize
 * @return string         The sanitized string
 */
function basis_sanitize_mod_footer_widgets( $choice ) {
	$options = array(
		'everywhere',
		'everywhere-but-front-page',
		'front-page-only'
	);

	if ( in_array( $choice, $options ) ) {
		return $choice;
	} else {
		return 'everywhere';
	}
}
endif;

if ( ! function_exists( 'basis_maybe_show_footer_widgets' ) ) :
/**
 * Conditionally show the footer widget areas.
 *
 * Location depends on the footer-widgets theme mod setting and which page is currently being rendered.
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_maybe_show_footer_widgets() {
	$choice = basis_sanitize_mod_footer_widgets( get_theme_mod( 'footer-widgets' ) );

	switch ( $choice ) {
		case 'everywhere':
		default:
			basis_call_footer_widgets();
			break;

		case 'everywhere-but-front-page':
			if ( ! is_front_page() ) {
				basis_call_footer_widgets();
			}
			break;

		case 'front-page-only':
			if ( is_front_page() ) {
				basis_call_footer_widgets();
			}
			break;
	}
}
endif;

if ( ! function_exists( 'basis_call_footer_widgets' ) ) :
/**
 * Convenience function to call the two footer widget areas.
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_call_footer_widgets() {
	get_sidebar( 'footer-left');
	get_sidebar( 'footer-right');
}
endif;

if ( ! function_exists( 'basis_implement_custom_colors' ) ) :
/**
 * Print custom colors.
 *
 * @since  1.0.
 *
 * @return void
 */
function basis_implement_custom_colors() {
	$primary = basis_maybe_hash_hex_color( get_theme_mod( 'primary-color' ) );

	// Only add color if one is set and it is not the default color
	if ( ! empty( $primary ) && '#18a374' !== $primary ) : ?>
		<style type="text/css" media="all">
			/* Basis custom primary color */
			a,
			.fine-print a:hover,
			.cycle-pager span.cycle-pager-active {
				color: <?php echo $primary; ?>;
			}
			.basis-primary-text {
				color: <?php echo $primary; ?>;
			}
			.basis-primary-background {
				background-color: <?php echo $primary; ?>;
			}
		</style>
	<?php endif;
}
endif;

if ( ! function_exists( 'basis_sanitize_hex_color' ) ) :
/**
 * Validates a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or null.
 * For validating values without a #, see sanitize_hex_color_no_hash().
 *
 * @since  1.0.
 *
 * @param  string         $color    Hexidecimal value to sanitize.
 * @return string|null              Sanitized value.
 */
function basis_sanitize_hex_color( $color ) {
	if ( '' === $color )
		return '';

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;

	return null;
}
endif;

if ( ! function_exists( 'basis_sanitize_hex_color_no_hash' ) ) :
/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * Saving hex colors without a hash puts the burden of adding the hash on the
 * UI, which makes it difficult to use or upgrade to other color types such as
 * rgba, hsl, rgb, and html color names.
 *
 * Returns either '', a 3 or 6 digit hex color (without a #), or null.
 *
 * @since  1.0.
 *
 * @param  string         $color    Hexidecimal value to sanitize.
 * @return string|null              Sanitized value.
 */
function basis_sanitize_hex_color_no_hash( $color ) {
	$color = ltrim( $color, '#' );

	if ( '' === $color )
		return '';

	return basis_sanitize_hex_color( '#' . $color ) ? $color : null;
}
endif;

if ( ! function_exists( 'basis_maybe_hash_hex_color' ) ) :
/**
 * Ensures that any hex color is properly hashed.
 * Otherwise, returns value untouched.
 *
 * This method should only be necessary if using sanitize_hex_color_no_hash().
 *
 * @since  1.0.
 *
 * @param  string    $color    Hexidecimal value to sanitize.
 * @return string              Sanitized value.
 */
function basis_maybe_hash_hex_color( $color ) {
	if ( $unhashed = basis_sanitize_hex_color_no_hash( $color ) )
		return '#' . $unhashed;

	return $color;
}
endif;

add_action( 'wp_head', 'basis_implement_custom_colors', 15 );

if ( class_exists( 'WP_Customize_Image_Control' ) && ! class_exists( 'Basis_Customize_Image_Control' ) ) :
/**
 * Basis_Customize_Image_Control
 *
 * Extend WP_Customize_Image_Control allowing access to uploads made within the same context.
 *
 * @link  https://gist.github.com/eduardozulian/4739075
 * @since 1.0.
 */
class Basis_Customize_Image_Control extends WP_Customize_Image_Control {
	/**
	 * Constructor.
	 *
	 * @since 1.0.
	 *
	 * @uses WP_Customize_Image_Control::__construct()
	 *
	 * @param  WP_Customize_Manager    $manager
	 * @param  string                  $id
	 * @param  array                   $args
	 * @return Basis_Customize_Image_Control
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Search for images within the defined context. If there's no context, it'll bring all images from the library.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	public function tab_uploaded() {
		$context_uploads = get_posts( array(
			'post_type'  => 'attachment',
			'meta_key'   => '_wp_attachment_context',
			'meta_value' => $this->context,
			'orderby'    => 'post_date',
			'nopaging'   => true
		) );
	?>
		<div class="uploaded-target"></div>
	<?php
		if ( empty( $context_uploads ) ) {
			return;
		}

		foreach ( (array) $context_uploads as $context_upload ) {
			$this->print_tab_image( esc_url( $context_upload->guid ) );
		}
	}
}
endif;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Basis_Customize_Misc_Control' ) ) :
/**
 * Class Basis_Customize_Misc_Control
 *
 * Control for adding arbitrary HTML to a Customizer section.
 *
 * @since 1.1.0
 */
class Basis_Customize_Misc_Control extends WP_Customize_Control {
	public $settings = 'blogname';
	public $description = '';
	public $group = '';

	public function render_content() {
		switch ( $this->type ) {
			default:
			case 'text' :
				echo '<p class="description">' . basis_allowed_tags( $this->description ) . '</p>';
				break;

			case 'heading':
				echo '<span class="customize-control-title">' . basis_allowed_tags( $this->label ) . '</span>';
				break;

			case 'line' :
				echo '<hr />';
				break;
		}
	}
}
endif;