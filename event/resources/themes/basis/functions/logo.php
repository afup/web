<?php
/**
 * @package Basis
 */

if ( ! class_exists( 'Basis_Logo' ) ) :
/**
 * A class that adds custom logo functionality.
 *
 * Two inputs are added in the customizer that allow uploading of a regular and a retina image. Images are displayed
 * using CSS's background image property. CSS is printed to the head dynamically to make this happen.
 *
 * Forked from Collections 1.0.5
 *
 * @since 1.0.
 */
class Basis_Logo {

	/**
	 * The one instance of Basis_Logo.
	 *
	 * @since 1.0.
	 *
	 * @var   Basis_Logo
	 */
	private static $instance;

	/**
	 * Stores the logo image, width, and height information.
	 *
	 * This var acts as a "run-time" cache. Since the functions in this class are called in different places throughout
	 * the page load, once the logo information is computed for the first time, it is cached to this variable.
	 * Subsequent requests for the information are pulled from the variable in memory instead of recomputing it.
	 *
	 * @since 1.0.
	 *
	 * @var   array    Holds the image, width, and height information for the logos.
	 */
	var $logo_information = array();

	/**
	 * Stores whether or not a specified logo type is available.
	 *
	 * @since 1.0.
	 *
	 * @var   array    Holds boolean values to indicate if the logo type is available.
	 */
	var $has_logo_by_type = array();

	/**
	 * Instantiate or return the one Basis_Logo instance.
	 *
	 * @since  1.0.
	 *
	 * @return Basis_Logo
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Initiate the actions.
	 *
	 * @since  1.0.
	 *
	 * @return Basis_Logo
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'print_logo_css' ) );
		add_action( 'customize_register', array( $this, 'customize' ) );
	}

	/**
	 * Add logo options via the WordPress Customizer.
	 *
	 * @since  1.0.
	 *
	 * @param  object    $wp_customize    The main customizer object.
	 * @return void
	 */
	public function customize( $wp_customize ) {
		if ( basis_is_wpcom() ) {
			$section = 'basis_theme';
		} else {
			// Only add Logo section in non-WPCOM environments
			$section = 'basis_logo';
			$wp_customize->add_section(
				$section,
				array(
					'title'    => __( 'Logo', 'basis' ),
					'priority' => 35,
				)
			);
		}

		// Regular Logo
		$wp_customize->add_setting(
			'regular-logo',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new Basis_Customize_Image_Control(
				$wp_customize,
				'regular-logo',
				array(
					'label'    => __( 'Regular Logo', 'basis' ),
					'section'  => $section,
					'settings' => 'regular-logo',
					'priority' => 10,
					'context'  => 'basis-custom-logo-regular' // For Basis_Customize_Image_Control
				)
			)
		);

		// Retina Logo
		$wp_customize->add_setting(
			'retina-logo',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new Basis_Customize_Image_Control(
				$wp_customize,
				'retina-logo',
				array(
					'label'    => __( 'Retina Logo', 'basis' ),
					'section'  => $section,
					'settings' => 'retina-logo',
					'priority' => 20,
					'context'  => 'basis-custom-logo-retina' // For Basis_Customize_Image_Control
				)
			)
		);
	}

	/**
	* Get the ID of an attachment from its image URL.
	*
	* @author  Taken from reverted change to WordPress core http://core.trac.wordpress.org/ticket/23831
	* @since   1.0.
	*
	* @param   string      $url    The path to an image.
	* @return  int|bool            ID of the attachment or 0 on failure.
	*/
	function get_attachment_id_from_url( $url ) {
		global $wpdb;

		if ( preg_match( '#\.[a-zA-Z0-9]+$#', $url ) ) {
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' " . "AND guid = %s", $url ) );

			if ( ! empty( $id ) ) {
				return (int) $id;
			} else {
				// There may be an HTTPS/HTTP mismatch
				if ( false !== strpos( $url, 'https://' ) ) {
					$url = str_replace( 'https://', 'http://', $url );
				} else {
					$url = str_replace( 'http://', 'https://', $url );
				}

				$id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' " . "AND guid = %s", $url ) );

				if ( ! empty( $id ) ) {
					return (int) $id;
				}
			}
		}

		return 0;
	}

	/**
	 * Fallback method for determining the ID of an attachment from its URL.
	 *
	 * @since 1.0.7
	 *
	 * @param  string $attachment_url The URL of the attached image.
     * @return int|bool               The ID of the attachment if one is found. Otherwise false.
	 */
	function get_attachment_id_fallback( $attachment_url = '' ) {
		global $wpdb;
		$attachment_id = false;

		// If there is no url, return.
		if ( '' == $attachment_url )
			return;

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
		}

		return $attachment_id;
	}

	/**
	* Get the dimensions of a logo image from cache or regenerate the values.
	*
	* @since  1.0.
	*
	* @param  string    $url    The URL of the image in question.
	* @return array             The dimensions array on success, and a blank array on failure.
	*/
	function get_logo_dimensions( $url ) {
		// Build the cache key
		$key = 'basis-' . md5( 'logo-dimensions-' . $url . BASIS_VERSION );

		// Pull from cache
		$dimensions = get_transient( $key );

		// If the value is not found in cache, regenerate
		if ( false === $dimensions ) {
			// Get the ID of the attachment
			$attachment_id = $this->get_attachment_id_from_url( $url );

			// Fallback if the first method doesn't work
			if ( ! $attachment_id ) {
				$attachment_id = $this->get_attachment_id_fallback( $url );
			}

			// Get the dimensions
			add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
			$info = wp_get_attachment_image_src( $attachment_id, 'full' );
			remove_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

			if ( false !== $info && isset( $info[0] ) && isset( $info[1] ) && isset( $info[2] ) ) {
				// Detect JetPack altered src
				if ( false === $info[1] && false === $info[2] ) {
					// Parse the URL for the dimensions
					$pieces = parse_url( urldecode( $info[0] ) );

					// Pull apart the query string
					if ( isset( $pieces['query'] ) ) {
						parse_str( $pieces['query'], $query_pieces );

						// Get the values from "resize"
						if ( isset( $query_pieces['resize'] ) || isset( $query_pieces['fit'] ) ) {
							if ( isset( $query_pieces['resize'] ) ) {
								$jp_dimensions = explode( ',', $query_pieces['resize'] );
							} elseif ( $query_pieces['fit'] ){
								$jp_dimensions = explode( ',', $query_pieces['fit'] );
							}

							if ( isset( $jp_dimensions[0] ) && isset( $jp_dimensions[1] ) ) {
								// Package the data
								$dimensions = array(
									'width'  => $jp_dimensions[0],
									'height' => $jp_dimensions[1],
								);
							}
						}
					}
				} else {
					// Package the data
					$dimensions = array(
						'width'  => $info[1],
						'height' => $info[2],
					);
				}
			} else {
				// Get the image path from the URL
				$wp_upload_dir = wp_upload_dir();
				$path          = trailingslashit( $wp_upload_dir['basedir'] ) . get_post_meta( $attachment_id, '_wp_attached_file', true );

				// Sometimes, WordPress just doesn't have the metadata available. If not, get the image size
				if ( file_exists( $path ) ) {
					$getimagesize  = getimagesize( $path );

					if ( false !== $getimagesize && isset( $getimagesize[0] ) && isset( $getimagesize[1] ) ) {
						$dimensions = array(
							'width'  => $getimagesize[0],
							'height' => $getimagesize[1],
						);
					} else {
						$dimensions = array();
					}
				} else {
					$dimensions = array();
				}
			}

			// Store the transient
			set_transient( $key, $dimensions, 86400 );
		}

		return $dimensions;
	}

	/**
	* Determine if a custom logo should be displayed.
	*
	* @since  1.0.
	*
	* @return bool    True if a logo should be displayed. False if a logo shouldn't be displayed.
	*/
	function has_logo() {
		return ( $this->has_logo_by_type( 'regular-logo' ) || $this->has_logo_by_type( 'retina-logo' ) );
	}

	/**
	* Determine if necessary information is available to show a particular logo.
	*
	* @since  1.0.
	*
	* @param  string    $type    The type of logo to inspect for.
	* @return bool               True if all information is available. False is something is missing.
	*/
	function has_logo_by_type( $type ) {
		// Clean the type value
		$type = sanitize_key( $type );

		// If the information is already set, return it from the instance cache
		if ( isset( $this->has_logo_by_type[ $type ] ) ) {
			return $this->has_logo_by_type[ $type ];
		}

		// Grab the logo information
		$information = $this->get_logo_information();

		// Set the default return value
		$return = false;

		// Verify that the logo type exists in the array
		if ( isset( $information[ $type ] ) ) {

			// Verify that the image is set and has a value
			if ( isset( $information[ $type ]['image'] ) && ! empty( $information[ $type ]['image'] ) ) {

				// Verify that the width is set and has a value
				if ( isset( $information[ $type ]['width'] ) && ! empty( $information[ $type ]['width'] ) ) {

					// Verify that the height is set and has a value
					if ( isset( $information[ $type ]['height'] ) && ! empty( $information[ $type ]['height'] ) ) {
						$return = true;
					}
				}
			}
		}

		// Cache to the instance var for future use
		$this->has_logo_by_type[ $type ] = $return;
		return $this->has_logo_by_type[ $type ];
	}

	/**
	* Utility function for getting information about the theme logos.
	*
	* @since  1.0.
	*
	* @return array    Array containing image file, width, and height for each logo.
	*/
	function get_logo_information() {
		// If the logo information is cached to an instance var, pull from there
		if ( ! empty( $this->logo_information ) ) {
			return $this->logo_information;
		}

		// Set the logo slugs
		$logos = array(
			'regular-logo',
			'retina-logo',
		);

		// For each logo slug, get the image, width and height
		foreach ( $logos as $logo ) {
			$this->logo_information[ $logo ]['image'] = get_theme_mod( $logo );

			// Set the defaults
			$this->logo_information[ $logo ]['width']  = '';
			$this->logo_information[ $logo ]['height'] = '';

			// If there is an image, get the dimensions
			if ( ! empty( $this->logo_information[ $logo ]['image'] ) ) {
				$dimensions = $this->get_logo_dimensions( $this->logo_information[ $logo ]['image'] );

				// Set the dimensions to the array if all information is present
				if ( ! empty( $dimensions ) && isset( $dimensions['width'] ) && isset( $dimensions['height'] ) ) {
					$this->logo_information[ $logo ]['width']  = $dimensions['width'];
					$this->logo_information[ $logo ]['height'] = $dimensions['height'];
				}
			}
		}

		// Allow logo settings to be overridden via filter
		$this->logo_information = apply_filters( 'basis_custom_logo_information', $this->logo_information );

		return $this->logo_information;
	}

	/**
	* Print CSS in the head for the logo.
	*
	* @since  1.0.
	*
	* @return void
	*/
	function print_logo_css() {
		$size = apply_filters( 'basis_custom_logo_max_width', '244' );

		// Grab the logo information
		$info = $this->get_logo_information();

		// CSS for displaying both logos
		if ( $this->has_logo_by_type( 'regular-logo' ) && $this->has_logo_by_type( 'retina-logo' ) ) : ?>
			<?php $final_dimensions = $this->adjust_dimensions( $info['regular-logo']['width'], $info['regular-logo']['height'], $size, false ); ?>

			<style type="text/css" media="all">
				.custom-logo {
					background: url("<?php echo addcslashes( esc_url_raw( $info['regular-logo']['image'] ), '"' ); ?>") no-repeat;
					width: <?php echo absint( $final_dimensions['width'] ); ?>px;
					height: <?php echo absint( $final_dimensions['height'] ); ?>px;
					background-size: contain;
				}
				@media
				(-webkit-min-device-pixel-ratio: 1.3),
				(-o-min-device-pixel-ratio: 2.6/2),
				(min--moz-device-pixel-ratio: 1.3),
				(min-device-pixel-ratio: 1.3),
				(min-resolution: 1.3dppx) {
					.custom-logo {
						background: url("<?php echo addcslashes( esc_url_raw( $info['retina-logo']['image'] ), '"' ); ?>") no-repeat;
						background-size: <?php echo absint( $final_dimensions['width'] ); ?>px <?php echo absint( $final_dimensions['height'] ); ?>px;
					}
				}
			</style>
		<?php elseif ( $this->has_logo_by_type( 'regular-logo' ) ) : // CSS when ONLY the regular logo is available ?>
			<?php $final_dimensions = $this->adjust_dimensions( $info['regular-logo']['width'], $info['regular-logo']['height'], $size ); ?>
			<style type="text/css" media="all">
				.custom-logo {
					background: url("<?php echo addcslashes( esc_url_raw( $info['regular-logo']['image'] ), '"' ); ?>") no-repeat;
					width: <?php echo absint( $final_dimensions['width'] ); ?>px;
					height: <?php echo absint( $final_dimensions['height'] ); ?>px;
					background-size: contain;
				}
			</style>
		<?php elseif ( $this->has_logo_by_type( 'retina-logo' ) ) : // CSS when ONLY the retina logo is available ?>
			<?php $final_dimensions = $this->adjust_dimensions( $info['retina-logo']['width'], $info['retina-logo']['height'], $size, true ); ?>
			<style type="text/css" media="all">
				.custom-logo {
					background: url("<?php echo addcslashes( esc_url_raw(  $info['retina-logo']['image'] ), '"' ); ?>") no-repeat;
					width: <?php echo absint( $final_dimensions['width'] ); ?>px;
					height: <?php echo absint( $final_dimensions['height'] ); ?>px;
					background-size: <?php echo absint( $final_dimensions['width'] ); ?>px <?php echo absint( $final_dimensions['height'] ); ?>px;
					background-size: contain;
				}
			</style>
		<?php endif;
	}

	/**
	 * Scale the image to the width boundary.
	 *
	 * @since  1.0.
	 *
	 * @param  int      $width             The image's width.
	 * @param  int      $height            The image's height.
	 * @param  int      $width_boundary    The maximum width for the image.
	 * @param  bool     $retina            Whether or not to divide the dimensions by 2.
	 * @return array                       Resulting height/width dimensions.
	 */
	function adjust_dimensions( $width, $height, $width_boundary, $retina = false ) {
		// Divide the dimensions by 2 for retina logos
		$divisor = ( true === $retina ) ? 2 : 1;
		$width   = $width / $divisor;
		$height  = $height / $divisor;

		// If width is wider than the boundary, apply the adjustment
		if ( $width > $width_boundary ) {
			$change_percentage = $width_boundary / $width;
			$width             = $width_boundary;
			$height            = $height * $change_percentage;
		}

		// Arrange the resulting dimensions in an array
		return array(
			'width'  => $width,
			'height' => $height,
		);
	}
} // end class
endif;

if ( ! function_exists( 'basis_get_logo' ) ) :
/**
 * Return the one Basis_Logo object.
 *
 * @since  1.0.
 *
 * @return Basis_Logo    The one Basis_Logo instance.
 */
function basis_get_logo() {
	return Basis_Logo::instance();
}
endif;

add_action( 'init', 'basis_get_logo', 1 );

