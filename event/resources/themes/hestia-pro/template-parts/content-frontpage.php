<?php
/**
 * The default template for displaying content
 *
 * Used for frontpage.
 *
 * @package Hestia
 * @since Hestia 1.0
 */
$content = get_the_content();
maybe_trigger_fa_loading( $content );
the_content();

