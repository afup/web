<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'basis_avatar_defaults' ) ):
/**
 * Add "Retina-Ready" as an avatar choice in the admin.
 *
 * @since  1.1
 *
 * @param  array    $avatar_defaults    The current default avatars.
 * @return array                        Modified defaults.
 */
function basis_avatar_defaults( $avatar_defaults ) {
	// Add Basis Mystery Person to the list of avatars
	$image           = get_template_directory_uri() . '/images/custom-avatar.png';
	$new_item        = array( $image => __( 'Basis Mystery Person', 'basis' ) );
	$avatar_defaults = $new_item + $avatar_defaults;

	return $avatar_defaults;
}
endif;

add_filter( 'avatar_defaults', 'basis_avatar_defaults' );