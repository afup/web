<?php
/**
 * @package Basis Child
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
