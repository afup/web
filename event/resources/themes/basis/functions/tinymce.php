<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'basis_mce_editor_buttons_2' ) ) :
/**
 * Activate the Styles dropdown for the Visual editor.
 *
 * @since  1.0.
 *
 * @param  array    $buttons    Array of activated buttons.
 * @return array                The modified array.
 */
function basis_mce_editor_buttons_2( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
endif;

add_filter( 'mce_buttons_2', 'basis_mce_editor_buttons_2' );

if ( ! function_exists( 'basis_mce_before_init' ) ) :
/**
 * Add styles to the Styles dropdown.
 *
 * @since  1.0.
 *
 * @param  array    $settings    TinyMCE settings array.
 * @return mixed                 Modified array.
 */
function basis_mce_before_init( $settings ) {
	$style_formats = array(
		array(
			'title'   => __( 'Introduction', 'basis' ),
			'block'   => 'p',
			'classes' => 'intro'
		),
		array(
			'title'   => __( 'Alert', 'basis' ),
			'block'   => 'div',
			'classes' => 'alert'
		),
		array(
			'title'   => __( 'Button Link', 'basis' ),
			'selector'=> 'a',
			'classes' => 'basis-button'
		),
	);

	$settings['style_formats'] = json_encode( $style_formats );

	return $settings;
}
endif;

add_filter( 'tiny_mce_before_init', 'basis_mce_before_init' );