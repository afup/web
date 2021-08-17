<?php
/**
 * @package Hestia Pro Child
 */

/** Allow upload of SVG files **/

function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


function afup_website_func($attrs) {
    $url = getenv('AFUP_WEBSITE_URL') . html_entity_decode($attrs['path']);
    $ch = curl_init();

    if (defined('WP_DEBUG') && WP_DEBUG == true) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      }

    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $body = curl_exec($ch);
    curl_close($ch);

    return $body;
}
add_shortcode('afup_website', 'afup_website_func');


function add_custom_head() {
    $html_block = get_post_meta(get_the_ID(), 'html_head', true);
    if ($html_block !== '') {
        echo $html_block;
    }
}
add_filter('wp_head', 'add_custom_head');

if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'hestia_child_parent_css' ) ):
	    function hestia_child_parent_css() {
		            wp_enqueue_style( 'hestia_child_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'bootstrap' ) );
			    	if( is_rtl() ) {
							wp_enqueue_style( 'hestia_child_parent_rtl', trailingslashit( get_template_directory_uri() ) . 'style-rtl.css', array( 'bootstrap' ) );
								}

			        }
endif;
add_action( 'wp_enqueue_scripts', 'hestia_child_parent_css', 1 );

/**
 *  * Import options from the parent theme
 *   *
 *    * @since 1.0.0
 *     */
function hestia_child_get_parent_options() {
		$hestia_mods = get_option( 'theme_mods_hestia-pro' );
			if ( ! empty( $hestia_mods ) ) {
						foreach ( $hestia_mods as $hestia_mod_k => $hestia_mod_v ) {
										set_theme_mod( $hestia_mod_k, $hestia_mod_v );
												}
							}
}
add_action( 'after_switch_theme', 'hestia_child_get_parent_options' );

// cf get_time_tags et maybe_render_post_meta dans event/resources/themes/hestia-pro/inc/views/blog/class-hestia-header-layout-manager.php
// avec cette modification on n'affiche plus la personne ayant créé l'article
function afup_posted_by() {
    $time = 'Publié le ';

    $time .= '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" content="' . esc_attr( get_the_date( 'Y-m-d' ) ) . '">';
    $time .= esc_html( get_the_time( get_option( 'date_format' ) ) );
    $time .= '</time>';
    if ( get_the_time( 'U' ) === get_the_modified_time( 'U' ) ) {
        return $time;
    }
    $time .= '<time class="updated hestia-hidden" datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">';
    $time .= esc_html( get_the_time( get_option( 'date_format' ) ) );
    $time .= '</time>';

    return $time;
}

add_filter('hestia_single_post_meta', 'afup_posted_by');
