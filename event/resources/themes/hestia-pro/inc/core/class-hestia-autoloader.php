<?php
/**
 * The file that defines autoload class
 *
 * A simple autoloader that loads class files recursively starting in the directory
 * where this class resides.  Additional options can be provided to control the naming
 * convention of the class files.
 *
 * @link        https://themeisle.com
 * @copyright   Copyright (c) 2017, Bogdan Preda
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 * @since       1.1.40
 * @package     hestia
 */

/**
 * The Autoloader class.
 *
 * @since      1.1.40
 * @package    hestia
 * @author     Themeisle <friends@themeisle.com>
 */
class Hestia_Autoloader {

	/**
	 * List of available classes to get for autoloading.
	 *
	 * @var array
	 */
	private $classes_to_load = array();

	/**
	 * Hestia_Autoloader constructor.
	 *
	 * Define the file paths.
	 */
	public function __construct() {
		$this->classes_to_load = array(
			'Hestia_Core'                                 => HESTIA_CORE_DIR,
			'Hestia_Admin'                                => HESTIA_CORE_DIR,
			'Hestia_Public'                               => HESTIA_CORE_DIR,
			'Hestia_Feature_Factory'                      => HESTIA_CORE_DIR,
			'Hestia_Abstract_Main'                        => HESTIA_CORE_DIR . 'abstract',
			'Hestia_Abstract_Module'                      => HESTIA_CORE_DIR . 'abstract',
			'Hestia_Register_Customizer_Controls'         => HESTIA_CORE_DIR . 'abstract',
			'Hestia_Front_Page_Section_Controls_Abstract' => HESTIA_CORE_DIR . 'abstract',
			'Hestia_Abstract_Metabox'                     => HESTIA_CORE_DIR . 'abstract',
			'Hestia_Customizer_Panel'                     => HESTIA_CORE_DIR . 'types',
			'Hestia_Customizer_Control'                   => HESTIA_CORE_DIR . 'types',
			'Hestia_Customizer_Partial'                   => HESTIA_CORE_DIR . 'types',
			'Hestia_Customizer_Section'                   => HESTIA_CORE_DIR . 'types',
			'Hestia_Bootstrap_Navwalker'                  => HESTIA_PHP_INCLUDE,
			'Hestia_Admin_Notices_Manager'                => HESTIA_PHP_INCLUDE . 'admin',
			'Hestia_Metabox_Manager'                      => HESTIA_PHP_INCLUDE . 'admin/metabox',
			'Hestia_Metabox_Main'                         => HESTIA_PHP_INCLUDE . 'admin/metabox',
			'Hestia_Metabox_Controls_Base'                => HESTIA_PHP_INCLUDE . 'admin/metabox',
			'Hestia_Metabox_Control_Base'                 => HESTIA_PHP_INCLUDE . 'admin/metabox/controls',
			'Hestia_Metabox_Radio_Image'                  => HESTIA_PHP_INCLUDE . 'admin/metabox/controls',
			'Hestia_Metabox_Checkbox'                     => HESTIA_PHP_INCLUDE . 'admin/metabox/controls',
			'Hestia_Customizer_Main'                      => HESTIA_PHP_INCLUDE . 'customizer',
			'Hestia_Customizer_Notices'                   => HESTIA_PHP_INCLUDE . 'customizer',
			'Hestia_Sync_About'                           => HESTIA_PHP_INCLUDE . 'customizer',
			'Hestia_Customizer_Page_Editor_Helper'        => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/customizer-page-editor',
			'Hestia_Page_Editor'                          => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/customizer-page-editor',
			'Hestia_Customize_Alpha_Color_Control'        => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/alpha-color-picker',
			'Hestia_Customizer_Dimensions'                => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/dimensions',
			'Hestia_Font_Selector'                        => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/font-selector',
			'Hestia_Select_Multiple'                      => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/multi-select',
			'Hestia_Customizer_Range_Value_Control'       => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/range-value',
			'Hestia_Repeater'                             => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/repeater',
			'Hestia_Hiding_Section'                       => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/section-hiding',
			'Hestia_Customize_Control_Radio_Image'        => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/subcontrols-allowing',
			'Hestia_Select_Hiding'                        => HESTIA_PHP_INCLUDE . 'customizer/controls/custom-controls/subcontrols-allowing',
			'Hestia_Customizer_Scroll_Ui'                 => HESTIA_PHP_INCLUDE . 'customizer/controls/ui/customizer-scroll',
			'Hestia_Customize_Control_Tabs'               => HESTIA_PHP_INCLUDE . 'customizer/controls/ui/customizer-tabs',
			'Hestia_Plugin_Install_Helper'                => HESTIA_PHP_INCLUDE . 'customizer/controls/ui/helper-plugin-install',
			'Hestia_Subscribe_Info'                       => HESTIA_PHP_INCLUDE . 'customizer/controls/ui/subscribe-info',
			'Hestia_Button'                               => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Contact_Info'                         => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Control_Upsell'                       => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Customizer_Heading'                   => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Generic_Notice_Section'               => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Main_Notice_Section'                  => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_PageBuilder_Button'                   => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Section_Docs'                         => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Section_Upsell'                       => HESTIA_PHP_INCLUDE . 'customizer/controls/ui',
			'Hestia_Header_Controls'                      => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Color_Controls'                       => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_General_Controls'                     => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Typography_Controls'                  => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Blog_Settings_Controls'               => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Upsell_Manager'                       => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Buttons_Style_Controls'               => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Footer_Controls'                      => HESTIA_PHP_INCLUDE . 'customizer/general',
			'Hestia_Big_Title_Controls'                   => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_About_Controls'                       => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_Shop_Controls'                        => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_Blog_Section_Controls'                => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_Contact_Controls'                     => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_Subscribe_Controls'                   => HESTIA_PHP_INCLUDE . 'customizer/front-page',
			'Hestia_Gutenberg'                            => HESTIA_PHP_INCLUDE . 'compatibility',
			'Hestia_PWA'                                  => HESTIA_PHP_INCLUDE . 'compatibility',
			'Hestia_Header_Footer_Elementor'              => HESTIA_PHP_INCLUDE . 'compatibility/page-builder',
			'Hestia_Page_Builder_Helper'                  => HESTIA_PHP_INCLUDE . 'compatibility/page-builder',
			'Hestia_Elementor_Compatibility'              => HESTIA_PHP_INCLUDE . 'compatibility/page-builder',
			'Hestia_Beaver_Builder_Compatibility'         => HESTIA_PHP_INCLUDE . 'compatibility/page-builder',
			'Hestia_Wpbakery_Compatibility'               => HESTIA_PHP_INCLUDE . 'compatibility/page-builder',
			'Hestia_Child'                                => HESTIA_PHP_INCLUDE . 'compatibility/child-themes',
			'Hestia_Child_Customizer'                     => HESTIA_PHP_INCLUDE . 'compatibility/child-themes',
			'Hestia_Woocommerce_Header_Manager'           => HESTIA_PHP_INCLUDE . 'compatibility/woocommerce',
			'Hestia_Wp_Forms'                             => HESTIA_PHP_INCLUDE . 'compatibility/wp-forms',
			'Hestia_Infinite_Scroll'                      => HESTIA_PHP_INCLUDE . 'infinite-scroll',
			'Hestia_Tweaks'                               => HESTIA_PHP_INCLUDE . 'views',
			'Hestia_Compatibility_Style'                  => HESTIA_PHP_INCLUDE . 'views',
			'Hestia_Content_404'                          => HESTIA_PHP_INCLUDE . 'views/main',
			'Hestia_Top_Bar'                              => HESTIA_PHP_INCLUDE . 'views/main',
			'Hestia_Header'                               => HESTIA_PHP_INCLUDE . 'views/main',
			'Hestia_Footer'                               => HESTIA_PHP_INCLUDE . 'views/main',
			'Hestia_Featured_Posts'                       => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Authors_Section'                      => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Additional_Views'                     => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Sidebar_Layout_Manager'               => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Header_Layout_Manager'                => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Blog_Post_Layout'                     => HESTIA_PHP_INCLUDE . 'views/blog',
			'Hestia_Colors'                               => HESTIA_PHP_INCLUDE . 'views/inline',
			'Hestia_Buttons'                              => HESTIA_PHP_INCLUDE . 'views/inline',
			'Hestia_Inline_Style_Manager'                 => HESTIA_PHP_INCLUDE . 'views/inline',
			'Hestia_Public_Typography'                    => HESTIA_PHP_INCLUDE . 'views/inline',
			'Hestia_First_Front_Page_Section'             => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Big_Title_Section'                    => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_About_Section'                        => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Blog_Section'                         => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Shop_Section'                         => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Contact_Section'                      => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Subscribe_Section'                    => HESTIA_PHP_INCLUDE . 'views/front-page',
			'Hestia_Woocommerce_Manager'                  => HESTIA_PHP_INCLUDE . 'modules/woo_enhancements',

			'Hestia_Main_Addon'                           => HESTIA_PHP_INCLUDE . 'addons',
			'Hestia_Addon_Manager'                        => HESTIA_PHP_INCLUDE . 'addons',
			'Hestia_Hooks_Page'                           => HESTIA_PHP_INCLUDE . 'addons/admin/hooks-page',
			'Hestia_Metabox_Addon'                        => HESTIA_PHP_INCLUDE . 'addons/admin',
			'Hestia_Metabox_View'                         => HESTIA_PHP_INCLUDE . 'views/pluggable',

			'Hestia_Iconpicker'                           => HESTIA_PHP_INCLUDE . 'addons/customizer/controls/iconpicker',
			'Hestia_Section_Ordering'                     => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Clients_Bar_Controls'                 => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Features_Controls'                    => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Portfolio_Controls'                   => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Pricing_Controls'                     => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Ribbon_Controls'                      => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Team_Controls'                        => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Testimonials_Controls'                => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Blog_Section_Controls_Addon'          => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Shop_Controls_Addon'                  => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',
			'Hestia_Slider_Controls_Addon'                => HESTIA_PHP_INCLUDE . 'addons/customizer/front-page',

			'Hestia_Blog_Settings_Controls_Addon'         => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Buttons_Style_Controls_Addon'         => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Color_Controls_Addon'                 => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Footer_Controls_Addon'                => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_General_Controls_Addon'               => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Header_Controls_Addon'                => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Typography_Controls_Addon'            => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_White_Label_Controls_Addon'           => HESTIA_PHP_INCLUDE . 'addons/customizer/general',
			'Hestia_Customizer_Notices_Addon'             => HESTIA_PHP_INCLUDE . 'addons/customizer',
			'Hestia_Defaults_Models'                      => HESTIA_PHP_INCLUDE . 'addons/models',

			'Hestia_Dokan_Compatibility'                  => HESTIA_PHP_INCLUDE . 'addons/plugin-compatibility/dokan',
			'Hestia_Woocommerce_Module'                   => HESTIA_PHP_INCLUDE . 'addons/modules/woo_enhancements',
			'Hestia_Woocommerce_Infinite_Scroll'          => HESTIA_PHP_INCLUDE . 'addons/plugin-compatibility/woocommerce',
			'Hestia_Woocommerce_Settings_Controls'        => HESTIA_PHP_INCLUDE . 'addons/plugin-compatibility/woocommerce',

			'Hestia_Custom_Layouts_Module'                => HESTIA_PHP_INCLUDE . 'addons/modules/custom_layouts',

			'Hestia_Translations_Manager'                 => HESTIA_PHP_INCLUDE . 'addons/plugin-compatibility',
			'Hestia_Elementor_Compatibility_Addon'        => HESTIA_PHP_INCLUDE . 'addons/plugin-compatibility',

			'Hestia_Subscribe_Blog_Section'               => HESTIA_PHP_INCLUDE . 'addons/views/blog',
			'Hestia_Front_Page_Shortcodes'                => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Clients_Bar_Section'                  => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Features_Section'                     => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Portfolio_Section'                    => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Pricing_Section'                      => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Ribbon_Section'                       => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Slider_Section_Addon'                 => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Team_Section'                         => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Testimonials_Section'                 => HESTIA_PHP_INCLUDE . 'addons/views/front-page',
			'Hestia_Header_Addon'                         => HESTIA_PHP_INCLUDE . 'addons/views/main',
			'Hestia_Buttons_Addon'                        => HESTIA_PHP_INCLUDE . 'addons/views/styles-output',
			'Hestia_Colors_Addon'                         => HESTIA_PHP_INCLUDE . 'addons/views/styles-output',
			'Hestia_General_Inline_Style'                 => HESTIA_PHP_INCLUDE . 'addons/views/styles-output',
			'Hestia_Public_Typography_Addon'              => HESTIA_PHP_INCLUDE . 'addons/views/styles-output',
			'Hestia_Compatibility_Style_Addon'            => HESTIA_PHP_INCLUDE . 'addons/views',
			'Hestia_Content_Import'                       => HESTIA_PHP_INCLUDE . 'content-import',
			'Hestia_Import_Utilities'                     => HESTIA_PHP_INCLUDE . 'content-import',
			'Hestia_Import_Zerif'                         => HESTIA_PHP_INCLUDE . 'content-import',
		);
	}


	/**
	 * Autoload function for registration with spl_autoload_register
	 *
	 * @since   1.1.40
	 * @access  public
	 *
	 * @param   string $class_name The class name requested.
	 *
	 * @return mixed
	 */
	public function loader( $class_name ) {
		if ( ! array_key_exists( $class_name, $this->classes_to_load ) ) {
			return false;
		}

		$filename  = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
		$full_path = trailingslashit( $this->classes_to_load[ $class_name ] ) . $filename;
		if ( is_file( $full_path ) ) {
			require $full_path;
		}

		return true;
	}
}
