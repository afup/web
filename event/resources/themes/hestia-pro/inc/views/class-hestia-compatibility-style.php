<?php
/**
 * This class handles the inline style to add compatibility with various plugins
 *
 * @package Hestia
 */

/**
 * Class Hestia_Compatibility_Style
 */
class Hestia_Compatibility_Style {

	/**
	 * The css code that will be loaded.
	 *
	 * @var string
	 */
	public static $css = '';

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_inline_style' ) );
	}

	/**
	 * Add css to the loading variable.
	 *
	 * @param string $css Css code that will be added.
	 */
	protected function add_css( $css ) {
		self::$css .= $css;
	}

	/**
	 * Enqueue inline style
	 *
	 * @return bool
	 */
	public function enqueue_inline_style() {
		$this->collect_inline_style();
		if ( empty( self::$css ) ) {
			return false;
		}
		wp_add_inline_style( 'hestia_style', self::$css );
		return true;
	}

	/**
	 * Load each function with its style.
	 */
	protected function collect_inline_style() {
		$this->load_everest();
		$this->load_beaver_buttons();
		$this->load_max_mega_menu();
		$this->load_sb_instagram_feed();
		$this->load_wp_forms();
		$this->load_elementor();
		$this->load_woo_mailchimp();
		$this->load_elementor_beaver_section_editing();
		$this->load_bbpress();
		$this->load_wpml();
		$this->load_sib();
		$this->load_top_bar_social_menu();
		$this->load_footer_social_menu();
	}

	/**
	 * Everest Forms css.
	 *
	 * @return bool
	 */
	private function load_everest() {
		if ( ! class_exists( 'EverestForms' ) ) {
			return false;
		}

		$css = '
		.everest-forms .evf-field-container .evf-frontend-row input,
		.everest-forms .evf-field-container .evf-frontend-row select,
		.everest-forms .evf-field-container .evf-frontend-row textarea,
		.everest-forms .evf-field-container .evf-frontend-row input:focus,
		.everest-forms .evf-field-container .evf-frontend-row select:focus,
		.everest-forms .evf-field-container .evf-frontend-row textarea:focus  {
            border: none;
            border-radius: 0;
		}

		.everest-forms .everest-forms-part-button,
		.everest-forms button[type=submit],
		.everest-forms input[type=submit] {
            padding: 12px 30px;
            border: none;
            border-radius: 3px;
            color: #fff;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Beaver builder buttons style.
	 *
	 * @return bool
	 */
	private function load_beaver_buttons() {
		if ( ! class_exists( 'FLBuilderLoader' ) ) {
			return false;
		}

		$css = '
		.fl-builder-bar-actions button, .fl-builder-bar-actions input[type="submit"], .fl-builder-bar-actions input[type="button"],
		.fl-builder-panel button,
		.fl-builder-panel input[type="submit"],
		.fl-builder-panel input[type="button"],
		.fl-builder--main-menu-panel-views button,
		.fl-builder--main-menu-panel-views input[type="submit"],
		.fl-builder--main-menu-panel-views input[type="button"],
		.fl-lightbox-footer button,
		.fl-lightbox-footer input[type="submit"],
		.fl-lightbox-footer input[type="button"] {
		  -webkit-box-shadow: inherit;
		  -moz-box-shadow: inherit;
		  box-shadow: inherit;
		}
		.fl-builder-bar-actions button:hover, .fl-builder-bar-actions input[type="submit"]:hover, .fl-builder-bar-actions input[type="button"]:hover,
		.fl-builder-panel button:hover,
		.fl-builder-panel input[type="submit"]:hover,
		.fl-builder-panel input[type="button"]:hover,
		.fl-builder--main-menu-panel-views button:hover,
		.fl-builder--main-menu-panel-views input[type="submit"]:hover,
		.fl-builder--main-menu-panel-views input[type="button"]:hover,
		.fl-lightbox-footer button:hover,
		.fl-lightbox-footer input[type="submit"]:hover,
		.fl-lightbox-footer input[type="button"]:hover {
		  -webkit-box-shadow: inherit;
		  -moz-box-shadow: inherit;
		  box-shadow: inherit;
		}
		
		.media-modal.wp-core-ui .media-modal-close {
		  background-color: inherit;
		}
		.media-modal.wp-core-ui select {
		  -webkit-appearance: menulist-button;
		  -moz-appearance: menulist-button;
		  appearance: menulist-button;
		}
		
		body.fl-builder-edit .navbar.header-with-topbar.navbar-default:not(.navbar-transparent) {
		  margin-top: 40px;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Max Mega Menu Compatibility.
	 *
	 * @return bool
	 */
	private function load_max_mega_menu() {
		if ( ! class_exists( 'Mega_Menu' ) ) {
			return false;
		}
		$css = '
		#mega-menu-wrap-primary {
		  display: table-cell;
		  width: 100%;
		  text-align: right;
		  vertical-align: middle;
		}
		
		#mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
		  text-transform: uppercase;
		}
		
		.navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
		  color: #fff;
		}
		.navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover {
		  color: #fff;
		}
		
		.mega-menu-primary .navbar-toggle-wrapper {
		  display: none;
		}
		
		.mega-menu-primary .obfx-menu-icon {
		  margin-right: 5px;
		  vertical-align: middle;
		}
		
		@media (max-width: 768px) {
		  .mega-menu-primary .container .navbar-header {
		    width: auto;
		    float: left;
		  }
		
		  #mega-menu-wrap-primary {
		    width: auto;
		    display: inline;
		    top: 5px;
		  }
		
		  #mega-menu-wrap-primary #mega-menu-primary {
		    width: 100%;
		  }
		
		  .navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link,
		  .navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover {
		    color: inherit;
		  }
		}
		@media (min-width: 769px) {
		  .mega-menu-primary .navbar.full-screen-menu .navbar-header {
		    width: auto;
		  }
		
		  .mega-menu-primary .hestia_center #mega-menu-wrap-primary #mega-menu-primary {
		    text-align: center;
		  }
		
		  .mega-menu-primary .hestia_right #mega-menu-wrap-primary #mega-menu-primary {
		    text-align: left;
		  }
		}';

		if ( is_rtl() ) {
			$css = '
			#mega-menu-wrap-primary {
			    display: table-cell;
			    width: 100%;
			    text-align: left;
			    vertical-align: middle;
			}
			
			#mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
			    text-transform: uppercase;
			}
			
			.navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
			    color: #fff;
			}
			.navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover {
			    color: #fff;
			}
			
			.mega-menu-primary .navbar-toggle-wrapper {
			    display: none;
			}
			
			.mega-menu-primary .obfx-menu-icon {
			    margin-left: 5px;
			    vertical-align: middle;
			}
			
			@media (max-width: 768px) {
			    .mega-menu-primary .container .navbar-header {
			        width: auto;
			        float: right;
			    }
			
			    #mega-menu-wrap-primary {
			        width: auto;
			        display: inline;
			        top: 5px;
			    }
			
			    #mega-menu-wrap-primary #mega-menu-primary {
			        width: 100%;
			    }
			
			    .navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link,
			    .navbar-transparent #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover {
			        color: inherit;
			    }
			}
			@media (min-width: 769px) {
			    .mega-menu-primary .navbar.full-screen-menu .navbar-header {
			        width: auto;
			    }
			
			    .mega-menu-primary .hestia_center #mega-menu-wrap-primary #mega-menu-primary {
			        text-align: center;
			    }
			
			    .mega-menu-primary .hestia_right #mega-menu-wrap-primary #mega-menu-primary {
			        text-align: right;
			    }
			}';
		}

		$this->add_css( $css );
		return true;
	}

	/**
	 * Smash Balloon Social Photo Feed - Instagram feed.
	 *
	 * @return bool
	 */
	private function load_sb_instagram_feed() {
		if ( ! class_exists( 'SB_Instagram_Feed' ) ) {
			return false;
		}
		$css = '
		.sbi_photo {
		  border-radius: 6px;
		  overflow: hidden;
		  box-shadow: 0 10px 15px -8px rgba(0, 0, 0, 0.24), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * WPForms compatibility style.
	 *
	 * @return bool
	 */
	private function load_wp_forms() {
		if ( ! class_exists( 'WPForms' ) ) {
			return false;
		}
		$css = '
			div.wpforms-container-full .wpforms-form div.wpforms-field input.wpforms-error {
				border: none;
			}
			div.wpforms-container .wpforms-form input[type=date],
			div.wpforms-container .wpforms-form input[type=datetime],
			div.wpforms-container .wpforms-form input[type=datetime-local],
			div.wpforms-container .wpforms-form input[type=email],
			div.wpforms-container .wpforms-form input[type=month],
			div.wpforms-container .wpforms-form input[type=number],
			div.wpforms-container .wpforms-form input[type=password],
			div.wpforms-container .wpforms-form input[type=range],
			div.wpforms-container .wpforms-form input[type=search],
			div.wpforms-container .wpforms-form input[type=tel],
			div.wpforms-container .wpforms-form input[type=text],
			div.wpforms-container .wpforms-form input[type=time],
			div.wpforms-container .wpforms-form input[type=url],
			div.wpforms-container .wpforms-form input[type=week],
			div.wpforms-container .wpforms-form select,
			div.wpforms-container .wpforms-form textarea, .nf-form-cont input:not([type=button]),
			div.wpforms-container .wpforms-form .form-group.is-focused .form-control {
			  box-shadow: none;
			}
			
			div.wpforms-container .wpforms-form input[type=date],
			div.wpforms-container .wpforms-form input[type=datetime],
			div.wpforms-container .wpforms-form input[type=datetime-local],
			div.wpforms-container .wpforms-form input[type=email],
			div.wpforms-container .wpforms-form input[type=month],
			div.wpforms-container .wpforms-form input[type=number],
			div.wpforms-container .wpforms-form input[type=password],
			div.wpforms-container .wpforms-form input[type=range],
			div.wpforms-container .wpforms-form input[type=search],
			div.wpforms-container .wpforms-form input[type=tel],
			div.wpforms-container .wpforms-form input[type=text],
			div.wpforms-container .wpforms-form input[type=time],
			div.wpforms-container .wpforms-form input[type=url],
			div.wpforms-container .wpforms-form input[type=week],
			div.wpforms-container .wpforms-form select,
			div.wpforms-container .wpforms-form textarea, .nf-form-cont input:not([type=button]){
				background-image: linear-gradient(#9c27b0, #9c27b0), linear-gradient(#d2d2d2, #d2d2d2);
				float: none;
				border: 0;
				border-radius: 0;
				background-color: transparent;
				background-repeat: no-repeat;
				background-position: center bottom, center calc(100% - 1px);
				background-size: 0 2px, 100% 1px;
				font-weight: 400;
				transition: background 0s ease-out;
			}
			
			div.wpforms-container .wpforms-form .form-group.is-focused .form-control{
				outline: none;
				background-size: 100% 2px,100% 1px;
				transition-duration: 0.3s;
			}
			
			/* Compatibility with WPForms */
			div.wpforms-container .wpforms-form input[type=date].form-control,
			div.wpforms-container .wpforms-form input[type=datetime].form-control,
			div.wpforms-container .wpforms-form input[type=datetime-local].form-control,
			div.wpforms-container .wpforms-form input[type=email].form-control,
			div.wpforms-container .wpforms-form input[type=month].form-control,
			div.wpforms-container .wpforms-form input[type=number].form-control,
			div.wpforms-container .wpforms-form input[type=password].form-control,
			div.wpforms-container .wpforms-form input[type=range].form-control,
			div.wpforms-container .wpforms-form input[type=search].form-control,
			div.wpforms-container .wpforms-form input[type=tel].form-control,
			div.wpforms-container .wpforms-form input[type=text].form-control,
			div.wpforms-container .wpforms-form input[type=time].form-control,
			div.wpforms-container .wpforms-form input[type=url].form-control,
			div.wpforms-container .wpforms-form input[type=week].form-control,
			div.wpforms-container .wpforms-form select.form-control,
			div.wpforms-container .wpforms-form textarea.form-control {
			  border: none;
			  padding: 7px 0;
			  font-size: 14px;
			}
			div.wpforms-container .wpforms-form .wpforms-field-select select {
			  border-radius: 3px;
			}
			div.wpforms-container .wpforms-form .wpforms-field-number input[type=number] {
			  background-image: none;
			  border-radius: 3px;
			}
			div.wpforms-container .wpforms-form button[type=submit].wpforms-submit,
			div.wpforms-container .wpforms-form button[type=submit].wpforms-submit:hover {
			  color: #ffffff;
			  border: none;
			}
			
			.home div.wpforms-container-full .wpforms-form {
			  margin-left: 15px;
			  margin-right: 15px;
			}
			
			div.wpforms-container-full .wpforms-form .wpforms-field {
			  padding: 0 0 24px 0 !important;
			}
			div.wpforms-container-full .wpforms-form .wpforms-submit-container {
			  text-align: right;
			}
			div.wpforms-container-full .wpforms-form .wpforms-submit-container button {
			  text-transform: uppercase;
			}
			div.wpforms-container-full .wpforms-form textarea {
			  border: none !important;
			}
			div.wpforms-container-full .wpforms-form textarea:focus {
			  border-width: 0 0 0 0 !important;
			}
			
			.home div.wpforms-container .wpforms-form textarea {
			  background-image: linear-gradient(#9c27b0, #9c27b0), linear-gradient(#d2d2d2, #d2d2d2);
			  background-color: transparent;
			  background-repeat: no-repeat;
			  background-position: center bottom, center calc(100% - 1px);
			  background-size: 0 2px, 100% 1px;
			}
			
			/* WPForms media queries for front page and mobile*/
			@media only screen and (max-width: 768px) {
			  .wpforms-container-full .wpforms-form .wpforms-one-half, .wpforms-container-full .wpforms-form button {
			    width: 100% !important;
			    margin-left: 0 !important;
			  }
			  .wpforms-container-full .wpforms-form .wpforms-submit-container {
			    text-align: center;
			  }
			}
			
			div.wpforms-container .wpforms-form input:focus,
			div.wpforms-container .wpforms-form select:focus {
			  border: none;
			}
		';

		if ( is_rtl() ) {
			$css = '
			div.wpforms-container .wpforms-form input[type=date],
			div.wpforms-container .wpforms-form input[type=datetime],
			div.wpforms-container .wpforms-form input[type=datetime-local],
			div.wpforms-container .wpforms-form input[type=email],
			div.wpforms-container .wpforms-form input[type=month],
			div.wpforms-container .wpforms-form input[type=number],
			div.wpforms-container .wpforms-form input[type=password],
			div.wpforms-container .wpforms-form input[type=range],
			div.wpforms-container .wpforms-form input[type=search],
			div.wpforms-container .wpforms-form input[type=tel],
			div.wpforms-container .wpforms-form input[type=text],
			div.wpforms-container .wpforms-form input[type=time],
			div.wpforms-container .wpforms-form input[type=url],
			div.wpforms-container .wpforms-form input[type=week],
			div.wpforms-container .wpforms-form select,
			div.wpforms-container .wpforms-form textarea, .nf-form-cont input:not([type=button]),
			div.wpforms-container .wpforms-form .form-group.is-focused .form-control {
			    box-shadow: none;
			}
			
			div.wpforms-container .wpforms-form input[type=date],
			div.wpforms-container .wpforms-form input[type=datetime],
			div.wpforms-container .wpforms-form input[type=datetime-local],
			div.wpforms-container .wpforms-form input[type=email],
			div.wpforms-container .wpforms-form input[type=month],
			div.wpforms-container .wpforms-form input[type=number],
			div.wpforms-container .wpforms-form input[type=password],
			div.wpforms-container .wpforms-form input[type=range],
			div.wpforms-container .wpforms-form input[type=search],
			div.wpforms-container .wpforms-form input[type=tel],
			div.wpforms-container .wpforms-form input[type=text],
			div.wpforms-container .wpforms-form input[type=time],
			div.wpforms-container .wpforms-form input[type=url],
			div.wpforms-container .wpforms-form input[type=week],
			div.wpforms-container .wpforms-form select,
			div.wpforms-container .wpforms-form textarea, .nf-form-cont input:not([type=button]){
			    background-image: linear-gradient(#9c27b0, #9c27b0), linear-gradient(#d2d2d2, #d2d2d2);
			    float: none;
			    border: 0;
			    border-radius: 0;
			    background-color: transparent;
			    background-repeat: no-repeat;
			    background-position: center bottom, center calc(100% - 1px);
			    background-size: 0 2px, 100% 1px;
			    font-weight: 400;
			    transition: background 0s ease-out;
			}
			
			div.wpforms-container .wpforms-form .form-group.is-focused .form-control{
			    outline: none;
			    background-size: 100% 2px,100% 1px;
			    transition-duration: 0.3s;
			}
			
			/* Compatibility with WPForms */
			div.wpforms-container .wpforms-form input[type=date].form-control,
			div.wpforms-container .wpforms-form input[type=datetime].form-control,
			div.wpforms-container .wpforms-form input[type=datetime-local].form-control,
			div.wpforms-container .wpforms-form input[type=email].form-control,
			div.wpforms-container .wpforms-form input[type=month].form-control,
			div.wpforms-container .wpforms-form input[type=number].form-control,
			div.wpforms-container .wpforms-form input[type=password].form-control,
			div.wpforms-container .wpforms-form input[type=range].form-control,
			div.wpforms-container .wpforms-form input[type=search].form-control,
			div.wpforms-container .wpforms-form input[type=tel].form-control,
			div.wpforms-container .wpforms-form input[type=text].form-control,
			div.wpforms-container .wpforms-form input[type=time].form-control,
			div.wpforms-container .wpforms-form input[type=url].form-control,
			div.wpforms-container .wpforms-form input[type=week].form-control,
			div.wpforms-container .wpforms-form select.form-control,
			div.wpforms-container .wpforms-form textarea.form-control {
			    border: none;
			    padding: 7px 0;
			    font-size: 14px;
			}
			div.wpforms-container .wpforms-form .wpforms-field-select select {
			    border-radius: 3px;
			}
			div.wpforms-container .wpforms-form .wpforms-field-number input[type=number] {
			    background-image: none;
			    border-radius: 3px;
			}
			div.wpforms-container .wpforms-form button[type=submit].wpforms-submit,
			div.wpforms-container .wpforms-form button[type=submit].wpforms-submit:hover {
			    color: #ffffff;
			    border: none;
			}
			
			.home div.wpforms-container-full .wpforms-form {
			    margin-right: 15px;
			    margin-left: 15px;
			}
			
			div.wpforms-container-full .wpforms-form .wpforms-field {
			    padding: 0 0 24px 0 !important;
			}
			div.wpforms-container-full .wpforms-form .wpforms-submit-container {
			    text-align: left;
			}
			div.wpforms-container-full .wpforms-form .wpforms-submit-container button {
			    text-transform: uppercase;
			}
			div.wpforms-container-full .wpforms-form textarea {
			    border: none !important;
			}
			div.wpforms-container-full .wpforms-form textarea:focus {
			    border-width: 0 0 0 0 !important;
			}
			
			.home div.wpforms-container .wpforms-form textarea {
			    background-image: linear-gradient(#9c27b0, #9c27b0), linear-gradient(#d2d2d2, #d2d2d2);
			    background-color: transparent;
			    background-repeat: no-repeat;
			    background-position: center bottom, center calc(100% - 1px);
			    background-size: 0 2px, 100% 1px;
			}
			
			/* WPForms media queries for front page and mobile*/
			@media only screen and (max-width: 768px) {
			    .wpforms-container-full .wpforms-form .wpforms-one-half, .wpforms-container-full .wpforms-form button {
			        width: 100% !important;
			        margin-right: 0 !important;
			    }
			    .wpforms-container-full .wpforms-form .wpforms-submit-container {
			        text-align: center;
			    }
			}
			
			div.wpforms-container .wpforms-form input:focus,
			div.wpforms-container .wpforms-form select:focus {
			    border: none;
			}';
		}

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load Elementor style.
	 *
	 * @return bool
	 */
	private function load_elementor() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}

		$css = '
		.elementor-page .hestia-about > .container {
		  width: 100%;
		}
		.elementor-page .pagebuilder-section {
		  padding: 0;
		}
		.elementor-page .title-in-content, .elementor-page .image-in-page {
		  display: none;
		}
		
		.home.elementor-page .main-raised > section.hestia-about {
		  overflow: visible;
		}
		
		.elementor-editor-active .navbar {
		  pointer-events: none;
		}
		
		.elementor-editor-active #elementor.elementor-edit-mode .elementor-element-overlay {
		  z-index: 1000000;
		}
		
		.elementor-page.page-template-template-fullwidth .blog-post-wrapper > .container {
		  width: 100%;
		}
		.elementor-page.page-template-template-fullwidth .blog-post-wrapper > .container .col-md-12 {
		  padding: 0;
		}
		.elementor-page.page-template-template-fullwidth article.section {
		  padding: 0;
		}

		.elementor-text-editor p, 
		.elementor-text-editor h1, 
		.elementor-text-editor h2, 
		.elementor-text-editor h3, 
		.elementor-text-editor h4, 
		.elementor-text-editor h5, 
		.elementor-text-editor h6 {
		  font-size: inherit;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * MailChimp for WordPress style.
	 *
	 * @return bool
	 */
	private function load_woo_mailchimp() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		if ( ! class_exists( 'MC4WP_Form_Manager' ) ) {
			return false;
		}

		$css = '
		.woocommerce-checkout .mc4wp-checkbox,
		.woocommerce-checkout .mc4wp-checkbox-woocommerce {
		  margin-bottom: 0 !important;
		  padding-left: 3px;
		}
		.woocommerce-checkout .mc4wp-checkbox span,
		.woocommerce-checkout .mc4wp-checkbox-woocommerce span {
		  color: #999999;
		  font-size: 16px;
		  font-weight: 300;
		  margin-left: 7px;
		}';

		if ( is_rtl() ) {
			$css = '
			.woocommerce-checkout .mc4wp-checkbox,
			.woocommerce-checkout .mc4wp-checkbox-woocommerce {
			    margin-bottom: 0 !important;
			    padding-right: 3px;
			}
			.woocommerce-checkout .mc4wp-checkbox span,
			.woocommerce-checkout .mc4wp-checkbox-woocommerce span {
			    color: #999999;
			    font-size: 16px;
			    font-weight: 300;
			    margin-right: 7px;
			}';
		}

		$this->add_css( $css );
		return true;
	}

	/**
	 * Elementor / Beaver Builder Section Editing styling.
	 *
	 * @return bool
	 */
	private function load_elementor_beaver_section_editing() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! class_exists( 'FLBuilderLoader' ) ) {
			return false;
		}

		$css = '
		.elementor-editor-preview .hestia-pagebuilder-frontpage-controls {
		  display: none;
		}
		
		.elementor-editor-active .main > section,
		.fl-builder-edit .main > section {
		  position: relative;
		}
		.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls,
		.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls {
		  position: absolute;
		  top: 35px;
		  bottom: 20px;
		  left: 20px;
		  right: 20px;
		  border: 2px solid #76cfe8;
		  text-align: center;
		  display: none;
		  z-index: 100;
		}
		.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a,
		.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a {
		  cursor: pointer;
		  position: relative;
		  background: #71d7f7;
		  border-radius: 3px 3px 0 0;
		  top: -30px;
		  line-height: 30px;
		  min-width: 30px;
		  text-align: center;
		  color: #fff;
		  font-weight: 800;
		  padding: 0 10px;
		  display: inline-block;
		}
		.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a:hover,
		.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a:hover {
		  color: #f00;
		}
		.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a > .dashicons,
		.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a > .dashicons {
		  height: 30px;
		  width: auto;
		  margin-right: 5px;
		  line-height: 27px;
		}
		.elementor-editor-active .main > section:hover .hestia-pagebuilder-frontpage-controls,
		.fl-builder-edit .main > section:hover .hestia-pagebuilder-frontpage-controls {
		  display: block;
		}';

		if ( is_rtl() ) {
			$css = '
			.elementor-editor-preview .hestia-pagebuilder-frontpage-controls {
			    display: none;
			}
			
			.elementor-editor-active .main > section,
			.fl-builder-edit .main > section {
			    position: relative;
			}
			.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls,
			.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls {
			    position: absolute;
			    top: 35px;
			    bottom: 20px;
			    right: 20px;
			    left: 20px;
			    border: 2px solid #76cfe8;
			    text-align: center;
			    display: none;
			    z-index: 100;
			}
			.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a,
			.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a {
			    cursor: pointer;
			    position: relative;
			    background: #71d7f7;
			    border-radius: 3px 3px 0 0;
			    top: -30px;
			    line-height: 30px;
			    min-width: 30px;
			    text-align: center;
			    color: #fff;
			    font-weight: 800;
			    padding: 0 10px;
			    display: inline-block;
			}
			.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a:hover,
			.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a:hover {
			    color: #f00;
			}
			.elementor-editor-active .main > section .hestia-pagebuilder-frontpage-controls > a > .dashicons,
			.fl-builder-edit .main > section .hestia-pagebuilder-frontpage-controls > a > .dashicons {
			    height: 30px;
			    width: auto;
			    margin-left: 5px;
			    line-height: 27px;
			}
			.elementor-editor-active .main > section:hover .hestia-pagebuilder-frontpage-controls,
			.fl-builder-edit .main > section:hover .hestia-pagebuilder-frontpage-controls {
			    display: block;
			}';
		}

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load BBPress styling.
	 *
	 * @return bool
	 */
	private function load_bbpress() {
		if ( ! class_exists( 'bbPress' ) ) {
			return false;
		}

		$css = '
		.bbp-template-notice.info {
		  max-width: inherit;
		}
		
		#bbpress-forums p.bbp-topic-meta img.avatar, #bbpress-forums ul.bbp-reply-revision-log img.avatar, #bbpress-forums ul.bbp-topic-revision-log img.avatar, #bbpress-forums div.bbp-template-notice img.avatar, #bbpress-forums .widget_display_topics img.avatar, #bbpress-forums .widget_display_replies img.avatar {
		  margin-bottom: 0;
		}
		
		.bbpress.blog-post .section-text p {
		  font-size: 15px;
		  margin-bottom: 20px;
		}
		
		#wp-link-close {
		  box-shadow: none;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Load WPML styling.
	 *
	 * @return bool
	 */
	private function load_wpml() {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return false;
		}

		$css = '
		footer .wpml-ls-item-legacy-dropdown a,
		footer .wpml-ls-item-legacy-dropdown-click a {
		  color: #000000;
		}
		
		.wpml-ls-statics-footer {
		  margin: 0 auto;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Send In Blue styling.
	 */
	private function load_sib() {
		if ( ! class_exists( 'SIB_Manager' ) ) {
			return false;
		}

		$css = '
		form.form-group[id^=sib] input[type=text],
		form.form-group[id^=sib] input[type=email],
		form.form-group[id^=sib] select {
		  border: none !important;
		}';

		$this->add_css( $css );
		return true;
	}

	/**
	 * Check if is social menu and trigger load font awesome.
	 *
	 * @param string $menu_slug Menu slug.
	 *
	 * @return bool
	 */
	private function maybe_should_load_menu_social_fa( $menu_slug ) {
		$theme_locations = get_nav_menu_locations();
		if ( ! array_key_exists( $menu_slug, $theme_locations ) ) {
			return false;
		}

		$menu = wp_get_nav_menu_items( $theme_locations[ $menu_slug ] );
		if ( empty( $menu ) ) {
			return false;
		}
		$social_networks = array( 'facebook', 'twitter', 'pinterest', 'google', 'linkedin', 'dribbble', 'github', 'youtube', 'instagram', 'reddit', 'tumblr', 'behance', 'snapchat', 'deviantart', 'vimeo' );
		$regex           = '(' . implode( '|', $social_networks ) . ')';

		foreach ( $menu as $menu_item ) {
			$url = $menu_item->url;
			if ( preg_match( $regex, $url ) === 1 ) {
				hestia_load_fa();
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if top bar is using social icons and load font awesome.
	 * Add social icons styling.
	 *
	 * @return bool
	 */
	private function load_top_bar_social_menu() {

		$should_load = $this->maybe_should_load_menu_social_fa( 'top-bar-menu' );

		if ( $should_load ) {
			$css = '
				.hestia-top-bar li a[href*="facebook.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="facebook.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="facebook.com"]:hover:before {
				  color: #3b5998;
				}
				
				.hestia-top-bar li a[href*="twitter.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="twitter.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="twitter.com"]:hover:before {
				  color: #55acee;
				}
				
				.hestia-top-bar li a[href*="pinterest.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="pinterest.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="pinterest.com"]:hover:before {
				  color: #cc2127;
				}
				
				.hestia-top-bar li a[href*="google.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="google.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="google.com"]:hover:before {
				  color: #dd4b39;
				}
				
				.hestia-top-bar li a[href*="linkedin.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="linkedin.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="linkedin.com"]:hover:before {
				  color: #0976b4;
				}
				
				.hestia-top-bar li a[href*="dribbble.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="dribbble.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="dribbble.com"]:hover:before {
				  color: #ea4c89;
				}
				
				.hestia-top-bar li a[href*="github.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="github.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="github.com"]:hover:before {
				  color: #000;
				}
				
				.hestia-top-bar li a[href*="youtube.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="youtube.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="youtube.com"]:hover:before {
				  color: #e52d27;
				}
				
				.hestia-top-bar li a[href*="instagram.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="instagram.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="instagram.com"]:hover:before {
				  color: #125688;
				}
				
				.hestia-top-bar li a[href*="reddit.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="reddit.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="reddit.com"]:hover:before {
				  color: #ff4500;
				}
				
				.hestia-top-bar li a[href*="tumblr.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="tumblr.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="tumblr.com"]:hover:before {
				  color: #35465c;
				}
				
				.hestia-top-bar li a[href*="behance.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="behance.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="behance.com"]:hover:before {
				  color: #1769ff;
				}
				
				.hestia-top-bar li a[href*="snapchat.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="snapchat.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="snapchat.com"]:hover:before {
				  color: #fffc00;
				}
				
				.hestia-top-bar li a[href*="deviantart.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="deviantart.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="deviantart.com"]:hover:before {
				  color: #05cc47;
				}
				
				.hestia-top-bar li a[href*="vimeo.com"] {
				  font-size: 0;
				}
				.hestia-top-bar li a[href*="vimeo.com"]:before {
				  content: "";
				}
				.hestia-top-bar li a[href*="vimeo.com"]:hover:before {
				  color: #1ab7ea;
				}
			';

			$this->add_css( $css );
			return true;
		}

		return false;
	}


	/**
	 * Check if footer is using social icons and load font awesome.
	 * Add social icons styling.
	 *
	 * @return bool
	 */
	private function load_footer_social_menu() {
		$should_load = $this->maybe_should_load_menu_social_fa( 'footer' );

		if ( $should_load ) {
			$css = '
				.footer-big .footer-menu li a[href*="facebook.com"],
				.footer-big .footer-menu li a[href*="twitter.com"],
				.footer-big .footer-menu li a[href*="pinterest.com"],
				.footer-big .footer-menu li a[href*="google.com"],
				.footer-big .footer-menu li a[href*="linkedin.com"],
				.footer-big .footer-menu li a[href*="dribbble.com"],
				.footer-big .footer-menu li a[href*="github.com"],
				.footer-big .footer-menu li a[href*="youtube.com"],
				.footer-big .footer-menu li a[href*="instagram.com"],
				.footer-big .footer-menu li a[href*="reddit.com"],
				.footer-big .footer-menu li a[href*="tumblr.com"],
				.footer-big .footer-menu li a[href*="behance.com"],
				.footer-big .footer-menu li a[href*="snapchat.com"],
				.footer-big .footer-menu li a[href*="deviantart.com"],
				.footer-big .footer-menu li a[href*="vimeo.com"]{
				  color: transparent;
				  font-size: 0;
				  padding: 10px;
				}
				
				.footer-big .footer-menu li a[href*="facebook.com"]:hover,
				.footer-big .footer-menu li a[href*="twitter.com"]:hover,
				.footer-big .footer-menu li a[href*="pinterest.com"]:hover,
				.footer-big .footer-menu li a[href*="google.com"]:hover,
				.footer-big .footer-menu li a[href*="linkedin.com"]:hover,
				.footer-big .footer-menu li a[href*="dribbble.com"]:hover,
				.footer-big .footer-menu li a[href*="github.com"]:hover,
				.footer-big .footer-menu li a[href*="youtube.com"]:hover,
				.footer-big .footer-menu li a[href*="instagram.com"]:hover,
				.footer-big .footer-menu li a[href*="reddit.com"]:hover,
				.footer-big .footer-menu li a[href*="tumblr.com"]:hover,
				.footer-big .footer-menu li a[href*="behance.com"]:hover,
				.footer-big .footer-menu li a[href*="snapchat.com"]:hover,
				.footer-big .footer-menu li a[href*="deviantart.com"]:hover,
				.footer-big .footer-menu li a[href*="vimeo.com"]:hover {
				  opacity: 1 !important;
				}
				
				.footer-big .footer-menu li a[href*="facebook.com"]:hover:before {
				    color: #3b5998;
				}
				.footer-big .footer-menu li a[href*="twitter.com"]:hover:before {
				    color: #55acee;
				}
				.footer-big .footer-menu li a[href*="pinterest.com"]:hover:before {
				    color: #cc2127;
				}
				.footer-big .footer-menu li a[href*="google.com"]:hover:before {
				    color: #dd4b39;
				}
				.footer-big .footer-menu li a[href*="linkedin.com"]:hover:before {
				    color: #0976b4;
				}
				.footer-big .footer-menu li a[href*="dribbble.com"]:hover:before {
				    color: #ea4c89;
				}
				.footer-big .footer-menu li a[href*="github.com"]:hover:before {
				    color: #000;
				}
				.footer-big .footer-menu li a[href*="youtube.com"]:hover:before {
				    color: #e52d27;
				}
				.footer-big .footer-menu li a[href*="instagram.com"]:hover:before {
				    color: #125688;
				}
				.footer-big .footer-menu li a[href*="reddit.com"]:hover:before {
				    color: #ff4500;
				}
				.footer-big .footer-menu li a[href*="tumblr.com"]:hover:before {
				    color: #35465c;
				}
				.footer-big .footer-menu li a[href*="behance.com"]:hover:before {
				    color: #1769ff;
				}
				.footer-big .footer-menu li a[href*="snapchat.com"]:hover:before {
				    color: #fffc00;
				}
				.footer-big .footer-menu li a[href*="deviantart.com"]:hover:before {
				    color: #05cc47;
				}
				.footer-big .footer-menu li a[href*="vimeo.com"]:hover:before {
				    color: #1ab7ea;
				}
				
				
				.footer-big .footer-menu li a[href*="facebook.com"]:before,
				.footer-big .footer-menu li a[href*="twitter.com"]:before,
				.footer-big .footer-menu li a[href*="pinterest.com"]:before,
				.footer-big .footer-menu li a[href*="google.com"]:before,
				.footer-big .footer-menu li a[href*="linkedin.com"]:before,
				.footer-big .footer-menu li a[href*="dribbble.com"]:before,
				.footer-big .footer-menu li a[href*="github.com"]:before,
				.footer-big .footer-menu li a[href*="youtube.com"]:before,
				.footer-big .footer-menu li a[href*="instagram.com"]:before,
				.footer-big .footer-menu li a[href*="reddit.com"]:before,
				.footer-big .footer-menu li a[href*="tumblr.com"]:before,
				.footer-big .footer-menu li a[href*="behance.com"]:before,
				.footer-big .footer-menu li a[href*="snapchat.com"]:before,
				.footer-big .footer-menu li a[href*="deviantart.com"]:before,
				.footer-big .footer-menu li a[href*="vimeo.com"]:before {
				    font-family: "Font Awesome 5 Brands";
				    font-weight: 900;
				    color: #3c4858;
				    font-size: 16px;
				}
				
				.footer-black .footer-menu li a[href*="facebook.com"]:before,
				.footer-black .footer-menu li a[href*="twitter.com"]:before,
				.footer-black .footer-menu li a[href*="pinterest.com"]:before,
				.footer-black .footer-menu li a[href*="google.com"]:before,
				.footer-black .footer-menu li a[href*="linkedin.com"]:before,
				.footer-black .footer-menu li a[href*="dribbble.com"]:before,
				.footer-black .footer-menu li a[href*="github.com"]:before,
				.footer-black .footer-menu li a[href*="youtube.com"]:before,
				.footer-black .footer-menu li a[href*="instagram.com"]:before,
				.footer-black .footer-menu li a[href*="reddit.com"]:before,
				.footer-black .footer-menu li a[href*="tumblr.com"]:before,
				.footer-black .footer-menu li a[href*="behance.com"]:before,
				.footer-black .footer-menu li a[href*="snapchat.com"]:before,
				.footer-black .footer-menu li a[href*="deviantart.com"]:before,
				.footer-black .footer-menu li a[href*="vimeo.com"]:before {
				  color: #fff;
				}
				
				.footer-big .footer-menu li a[href*="facebook.com"]:before {
				    content: "";
				}
				
				.footer-big .footer-menu li a[href*="twitter.com"]:before {
				    content: "";
				}
				
				
				.footer-big .footer-menu li a[href*="pinterest.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="google.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="linkedin.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="dribbble.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="github.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="youtube.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="instagram.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="reddit.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="tumblr.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="behance.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="snapchat.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="deviantart.com"]:before {
				  content: "";
				}
				
				.footer-big .footer-menu li a[href*="vimeo.com"]:before {
				  content: "";
				}
			';

			$this->add_css( $css );
			return true;
		}

		return false;
	}

}
