<?php
/**
 * Bootstrap nav walker
 *
 * @package Hestia
 */

/**
 * Class Name: hestia_bootstrap_navwalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme
 * using the WordPress built in menu manager. Version: 2.0.4 Author: Edward McIntyre - @twittem License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Modified by Hardeep Asrani for the theme.
 *
 * @since Hestia 1.0
 */
class Hestia_Bootstrap_Navwalker extends Walker_Nav_Menu {

	/**
	 * Start_lvl
	 *
	 * @see   Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of page. Used for padding.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul role=\"menu\" class=\"dropdown-menu\">\n";
	}

	/**
	 * Start_el
	 *
	 * @see   Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string  $output Output.
	 * @param WP_Post $item   Item.
	 * @param int     $depth  Depth.
	 * @param array   $args   Args.
	 * @param int     $id     id.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		/**
		 * Dividers, Headers or Disabled
		 * =============================
		 * Determine whether the item is a Divider, Header, Disabled or regular
		 * menu item. To prevent errors we use the strcasecmp() function to so a
		 * comparison that is not case sensitive. The strcasecmp() function returns
		 * a 0 if the strings are equal.
		 */

		if ( property_exists( $item, 'attr_title' ) && strcasecmp( $item->attr_title, 'divider' ) === 0 && $depth >= 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} elseif ( property_exists( $item, 'divider' ) && strcasecmp( $item->title, 'divider' ) === 0 && $depth >= 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else {

			$value     = '';
			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if ( $args->has_children && $depth === 0 ) {
				$class_names .= ' dropdown';
			} elseif ( $args->has_children && $depth > 0 ) {
				$class_names .= ' dropdown dropdown-submenu';
			}

			if ( in_array( 'current-menu-item', $classes, true ) ) {
				$class_names .= ' active';
			}

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names . '>';

			$atts           = array();
			$title          = ! empty( $item->title ) ? strip_tags( apply_filters( 'the_title', sanitize_text_field( $item->title ), $item->ID ) ) : '';
			$atts['title']  = ! empty( $item->attr_title ) ? strip_tags( apply_filters( 'the_title', sanitize_text_field( $item->attr_title ), $item->ID ) ) : $title;
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';

			// Remove the title if the menu has 'social-item' as class.
			if ( in_array( 'social-item', $classes, true ) ) {
				$item->title = '';
			}

			// If item has_children add atts to a.
			if ( $args->has_children ) {
				$atts['href']  = ! empty( $item->url ) ? $item->url : '#';
				$atts['class'] = 'dropdown-toggle';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			$is_wpml_item = ! empty( $item->type ) && $item->type === 'wpml_ls_menu_item';

			/*
			 * Glyphicons
			 * ===========
			 * Since the the menu item is NOT a Divider or Header we check the see
			 * if there is a value in the attr_title property. If the attr_title
			 * property is NOT null we apply it as the class name for the glyphicon.
			 */

			if ( ! empty( $item->attr_title ) && ! $is_wpml_item ) {
				$item_output .= '<a' . $attributes . '><i class="' . esc_attr( hestia_display_fa_icon( $item->attr_title ) ) . '"></i>&nbsp;';
			} elseif ( in_array( 'hestia-mm-heading', $item->classes, true ) && ( $item->url === '#' ) ) {
				$item_output .= '<span class="mm-heading-wrapper">';
			} elseif ( in_array( 'hestia-mm-heading', $item->classes, true ) ) {
				$item_output .= '<span class="mm-heading-wrapper"><a' . $attributes . '>';
			} else {
				$item_output .= '<a' . $attributes . '>';
			}
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ( $args->has_children ) ? ' <span class="caret-wrap"><span class="caret"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" class="svg-inline--fa fa-chevron-down fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg></span></span></a>' : '</a>';

			if ( ! empty( $item->description ) && ( $item->description !== ' ' ) && $depth >= 1 ) {
				$item_output .= '<span class="hestia-mm-description">' . $item->description . '</span>';
			}

			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @see   Walker::start_el()
	 * @since 2.5.0
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              Args.
	 * @param string $output            Passed by reference. Used to append additional content.
	 *
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];

		// Display this element.
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Menu Fallback
	 * =============
	 * If this function is assigned to the wp_nav_menu's fallback_cb variable
	 * and a manu has not been assigned to the theme location in the WordPress
	 * menu manager the function with display nothing to a non-logged in user,
	 * and will add a link to the WordPress menu manager if logged in as an admin.
	 *
	 * @param array $args passed from the wp_nav_menu function.
	 */
	public static function fallback( $args ) {
		if ( current_user_can( 'edit_theme_options' ) ) {

			$fb_output = null;

			if ( $args['container'] ) {
				$fb_output = '<' . $args['container'];

				if ( $args['container_id'] ) {
					$fb_output .= ' id="' . $args['container_id'] . '"';
				}

				if ( $args['container_class'] ) {
					$fb_output .= ' class="' . $args['container_class'] . '"';
				}

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $args['menu_id'] ) {
				$fb_output .= ' id="' . $args['menu_id'] . '"';
			}

			if ( $args['menu_class'] ) {
				$fb_output .= ' class="' . $args['menu_class'] . '"';
			}

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">Add a menu</a></li>';
			$fb_output .= '</ul>';

			if ( $args['container'] ) {
				$fb_output .= '</' . $args['container'] . '>';
			}

			$allowed_html = array(
				'a'   => array(
					'href' => array(),
				),
				'div' => array(
					'id'    => array(),
					'class' => array(),
				),
				'ul'  => array(
					'class' => array(),
				),
				'li'  => array(),
			);

			echo wp_kses( $fb_output, $allowed_html );
		}
	}
}
