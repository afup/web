<?php
/**
 * Functions for Max Mega Menu which only needs to be used when Max Mega Menu is active.
 *
 * @package Hestia
 * @since   Hestia 1.0
 */
/**
 * Max Mega Menu Hestia Compatibility
 **/
function megamenu_add_theme_hestia_max_menu( $themes ) {
	$themes['hestia_max_menu'] = array(
		'title'                                    => 'Hestia',
		'menu_item_link_height'                    => '50px',
		'menu_item_align'                          => 'right',
		'container_background_from'                => 'rgba(255, 255, 255, 0)',
		'container_background_to'                  => 'rgba(255, 255, 255, 0)',
		'menu_item_background_hover_from'          => 'rgba(255, 255, 255, 0.1)',
		'menu_item_background_hover_to'            => 'rgba(255, 255, 255, 0.1)',
		'menu_item_link_font_size'                 => '12px',
		'menu_item_link_color'                     => '#555',
		'menu_item_link_color_hover'               => '#e91e63',
		'menu_item_highlight_current'              => 'off',
		'panel_background_from'                    => 'rgb(255, 255, 255)',
		'panel_background_to'                      => 'rgb(255, 255, 255)',
		'panel_header_font_size'                   => '12px',
		'panel_header_font_weight'                 => 'normal',
		'panel_header_border_color'                => '#555',
		'panel_font_size'                          => '12px',
		'panel_font_color'                         => 'rgb(49, 49, 49)',
		'panel_font_color_hover'                   => '#e91e63',
		'panel_font_family'                        => 'inherit',
		'panel_second_level_font_color'            => 'rgb(49, 49, 49)',
		'panel_second_level_font_color_hover'      => '#e91e63',
		'panel_second_level_text_transform'        => 'none',
		'panel_second_level_font'                  => 'inherit',
		'panel_second_level_font_size'             => '12px',
		'panel_second_level_font_weight'           => 'normal',
		'panel_second_level_font_weight_hover'     => 'normal',
		'panel_second_level_text_decoration'       => 'none',
		'panel_second_level_text_decoration_hover' => 'none',
		'panel_second_level_padding_left'          => '20px',
		'panel_second_level_border_color'          => '#555',
		'panel_third_level_font_color'             => 'rgb(49, 49, 49)',
		'panel_third_level_font_color_hover'       => '#e91e63',
		'panel_third_level_font'                   => 'inherit',
		'panel_third_level_font_size'              => '12px',
		'panel_third_level_padding_left'           => '20px',
		'flyout_background_from'                   => 'rgb(255, 255, 255)',
		'flyout_background_to'                     => 'rgb(255, 255, 255)',
		'flyout_background_hover_from'             => 'rgb(255, 255, 255)',
		'flyout_background_hover_to'               => 'rgb(255, 255, 255)',
		'flyout_link_size'                         => '12px',
		'flyout_link_color'                        => 'rgb(49, 49, 49)',
		'flyout_link_color_hover'                  => '#e91e63',
		'flyout_link_family'                       => 'inherit',
		'responsive_breakpoint'                    => '768px',
		'resets'                                   => 'on',
		'toggle_background_from'                   => 'rgba(255, 255, 255, 0.1)',
		'toggle_background_to'                     => 'rgba(255, 255, 255, 0.1)',
		'toggle_font_color'                        => 'rgb(102, 102, 102)',
		'mobile_background_from'                   => 'rgb(255, 255, 255)',
		'mobile_background_to'                     => 'rgb(255, 255, 255)',
		'mobile_menu_item_link_font_size'          => '12px',
		'mobile_menu_item_link_color'              => 'rgb(102, 102, 102)',
		'mobile_menu_item_link_text_align'         => 'left',
		'responsive_text'                          => '',
	);

	return $themes;
}

add_filter( 'megamenu_themes', 'megamenu_add_theme_hestia_max_menu' );

