<?php
/**
 * @package Basis
 */

if ( ! function_exists( 'basis_customize_colors' ) ) :
/**
 * Add color theme options via the WordPress Customizer.
 *
 * @since  1.0.
 *
 * @param  object    $wp_customize    The main customizer object.
 * @return void
 */
function basis_customize_colors( $wp_customize ) {
	// Accent Color
	$wp_customize->add_setting(
		'primary-color',
		array(
			'default'           => '#18a374',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'basis_maybe_hash_hex_color'
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'primary-color',
			array(
				'label'    => __( 'Accent Color', 'basis' ),
				'section'  => 'colors',
				'settings' => 'primary-color',
				'priority' => 80,
			)
		)
	);
}
endif;

add_action( 'customize_register', 'basis_customize_colors' );

/**
 * Add different theme customizer sections, depending on the environment.
 *
 * @since 1.0.
 *
 * @param  object $wp_customize    The main customizer object.
 * @return void
 */
function basis_add_customizer_sections( $wp_customize ) {
	// WPCOM
	if ( basis_is_wpcom() ) {
		// Add the Theme section
		$wp_customize->add_section(
			'basis_theme',
			array(
				'title'    => __( 'Theme', 'basis' ),
				'priority' => 161,
			)
		);
		basis_customize_display( $wp_customize, 'basis_theme' );
		basis_customize_footer( $wp_customize, 'basis_theme' );
	}
	// TTF
	else {
		// Add the Display section
		$wp_customize->add_section(
			'basis_display',
			array(
				'title'    => __( 'Display', 'basis' ),
				'priority' => 161,
			)
		);
		basis_customize_display( $wp_customize, 'basis_display' );

		// Add the Footer section
		$wp_customize->add_section(
			'basis_footer',
			array(
				'title'    => __( 'Footer', 'basis' ),
				'priority' => 162,
			)
		);
		basis_customize_footer( $wp_customize, 'basis_footer' );
	}
}


add_action( 'customize_register', 'basis_add_customizer_sections' );

if ( ! function_exists( 'basis_customize_display' ) ) :
/**
 * Add display theme options via the WordPress Customizer.
 *
 * @since  1.0.
 *
 * @param  object $wp_customize    The main customizer object.
 * @param  string $section         The section ID to assign controls to.
 * @return void
 */
function basis_customize_display( $wp_customize, $section ) {
	// Sticky post label
	$wp_customize->add_setting(
		'sticky-label',
		array(
			'default'           => __( 'Featured', 'basis' ),
			'type'              => 'theme_mod',
			'sanitize_callback' => 'wp_strip_all_tags',
		)
	);

	$wp_customize->add_control(
		'basis_sticky-label',
		array(
			'settings' => 'sticky-label',
			'section'  => $section,
			'label'    => __( 'Sticky post label', 'basis' ),
			'type'     => 'text',
			'priority' => 30,
		)
	);

	// Archives heading
	$wp_customize->add_control(
		new Basis_Customize_Misc_Control(
			$wp_customize,
			'basis_display-archive-heading',
			array(
				'section'     => $section,
				'type'        => 'heading',
				'label' => __( 'Archives', 'ttf-one' ),
				'priority'    => 35
			)
		)
	);

	// Archive simple view
	$wp_customize->add_setting(
		'archive-content',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'basis_archive-content',
		array(
			'settings' => 'archive-content',
			'section'  => $section,
			'label'    => __( 'Enable simple archive view', 'basis' ),
			'type'     => 'checkbox',
			'priority' => 40,
		)
	);
}
endif;

if ( ! function_exists( 'basis_customize_footer' ) ) :
/**
 * Add theme options for the footer via the WordPress Customizer.
 *
 * @since  1.0.
 *
 * @param  object $wp_customize    The main customizer object.
 * @param  string $section         The section ID to assign controls to.
 * @return void
 */
function basis_customize_footer( $wp_customize, $section ) {
	// Footer widgets
	$wp_customize->add_setting(
		'footer-widgets',
		array(
			'default'           => 'everywhere',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'basis_sanitize_mod_footer_widgets',
		)
	);

	$wp_customize->add_control(
		'basis_footer-widgets',
		array(
			'settings' => 'footer-widgets',
			'section'  => $section,
			'label'    => __( 'Show the footer widgets', 'basis' ),
			'type'     => 'select',
			'choices'  => array(
				'everywhere'                => __( 'Everywhere', 'basis' ),
				'everywhere-but-front-page' => __( 'Everywhere but my front page', 'basis' ),
				'front-page-only'           => __( 'On my front page only', 'basis' )
			),
			'priority' => 70,
		)
	);

	// Footer text
	$wp_customize->add_setting(
		'footer-text',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'basis_allowed_tags',
		)
	);

	$wp_customize->add_control(
		'basis_footer-text',
		array(
			'settings' => 'footer-text',
			'section'  => $section,
			'label'    => __( 'Footer Text', 'basis' ),
			'type'     => 'text',
			'priority' => 80,
		)
	);

	// Twitter
	$wp_customize->add_setting(
		'twitter',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_twitter',
		array(
			'settings' => 'twitter',
			'section'  => $section,
			'label'    => __( 'Twitter URL', 'basis' ),
			'type'     => 'text',
			'priority' => 90,
		)
	);

	// Facebook
	$wp_customize->add_setting(
		'facebook',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_facebook',
		array(
			'settings' => 'facebook',
			'section'  => $section,
			'label'    => __( 'Facebook URL', 'basis' ),
			'type'     => 'text',
			'priority' => 91,
		)
	);

	// Google
	$wp_customize->add_setting(
		'google',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_google',
		array(
			'settings' => 'google',
			'section'  => $section,
			'label'    => __( 'Google&#43; URL', 'basis' ),
			'type'     => 'text',
			'priority' => 92,
		)
	);

	// Flickr
	$wp_customize->add_setting(
		'flickr',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_flickr',
		array(
			'settings' => 'flickr',
			'section'  => $section,
			'label'    => __( 'Flickr URL', 'basis' ),
			'type'     => 'text',
			'priority' => 93,
		)
	);

	// Pinterest
	$wp_customize->add_setting(
		'pinterest',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_pinterest',
		array(
			'settings' => 'pinterest',
			'section'  => $section,
			'label'    => __( 'Pinterest URL', 'basis' ),
			'type'     => 'text',
			'priority' => 94,
		)
	);

	// Linked In
	$wp_customize->add_setting(
		'linkedin',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_linked-in',
		array(
			'settings' => 'linkedin',
			'section'  => $section,
			'label'    => __( 'LinkedIn URL', 'basis' ),
			'type'     => 'text',
			'priority' => 95,
		)
	);

	// RSS
	$wp_customize->add_setting(
		'rss',
		array(
			'default'           => '',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		'basis_rss',
		array(
			'settings' => 'rss',
			'section'  => $section,
			'label'    => __( 'RSS URL', 'basis' ),
			'type'     => 'text',
			'priority' => 96,
		)
	);
}
endif;
