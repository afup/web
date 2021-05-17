<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-04-01
 * @package trait-image-handling.php
 */

trait Themeisle_OB {
	/**
	 * A list of allowed mimes.
	 *
	 * @var array
	 */
	protected $extensions = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
		'svg'          => 'image/svg+xml',
	);

	/**
	 * Replace demo urls in meta with site urls.
	 */
	public function replace_image_urls( $markup ) {
		// Get all slashed and un-slashed urls.
		$old_urls = $this->get_urls_to_replace( $markup );
		if ( ! is_array( $old_urls ) || empty( $old_urls ) ) {
			return $markup;
		}

		// Create an associative array.
		$urls = array_combine( $old_urls, $old_urls );
		// Unslash values of associative array.
		$urls = array_map( 'wp_unslash', $urls );
		// Remap host and directory path.
		$urls = array_map( array( $this, 'remap_host' ), $urls );
		// Replace image urls in meta.
		$markup = str_replace( array_keys( $urls ), array_values( $urls ), $markup );

		return $markup;
	}

	/**
	 * Get url replace array.
	 *
	 * @return array
	 */
	private function get_urls_to_replace( $markup ) {
		$regex = '/(?:http(?:s?):)(?:[\/\\\\\\\\|.|\w|\s|-])*\.(?:' . implode( '|', array_keys( $this->extensions ) ) . ')/m';

		if ( ! is_string( $markup ) ) {
			return array();
		}

		preg_match_all( $regex, $markup, $urls );

		$urls = array_map(
			function ( $value ) {
				return rtrim( html_entity_decode( $value ), '\\' );
			},
			$urls[0]
		);

		$urls = array_unique( $urls );

		return array_values( $urls );
	}

	/**
	 * Remap URLs host.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	private function remap_host( $url ) {
		if ( ! strpos( $url, '/uploads/' ) ) {
			return $url;
		}
		$old_url   = $url;
		$url_parts = parse_url( $url );

		if ( ! isset( $url_parts['host'] ) ) {
			return $url;
		}
		$url_parts['path'] = preg_split( '/\//', $url_parts['path'] );
		$url_parts['path'] = array_slice( $url_parts['path'], - 3 );

		$uploads_dir = wp_get_upload_dir();
		$uploads_url = $uploads_dir['baseurl'];

		$new_url = esc_url( $uploads_url . '/' . join( '/', $url_parts['path'] ) );

		return str_replace( $old_url, $new_url, $url );
	}

	/**
	 * Hash the demo slug and prefix the page name with it. Drop words like demo, neve, or the demo slug from page slug.
	 *
	 * @param string $slug      page slug.
	 * @param string $demo_slug demo slug.
	 *
	 * @return string
	 */
	public function cleanup_page_slug( $slug, $demo_slug ) {
		$hash = substr( md5( $demo_slug ), 0, 5 );
		$slug = str_replace( $demo_slug, '', $slug );
		$slug = str_replace( 'demo', '', $slug );
		$slug = ltrim( $slug, '-' );
		$slug = $hash . '-' . $slug;

		return $slug;
	}
}
