/**
 * Main scripts file
 *
 * @package Hestia
 */

/* global jQuery */
/* global Hammer */
/* global AOS */
/* global requestpost */

(function ( $ ) {
	$.hestiaFeatures = {

		/**
		 * Initalize masonry for blog.
		 */
		'initMasonry': function () {
			if ( typeof requestpost === 'undefined' ) {
				return;
			}

			if ( !requestpost.masonry ) {
				return;
			}

			$( '.post-grid-display' ).masonry( {
				// set itemSelector so .grid-sizer is not used in layout
				itemSelector: '.card-no-width',
				// use element for option
				columnWidth: '.card-no-width',
				percentPosition: true
			} );
		},

		/**
		 * Initialize animations.
		 */
		'initAnimations': function () {
			if ( typeof AOS !== 'undefined' ) {
				var aosOpt = {
					offset: 250,
					delay: 300,
					duration: 900,
					once: true,
					disable: 'mobile'
				};
				AOS.init( aosOpt );
			}
		},

		/**
		 * Enable tooltips
		 * https://getbootstrap.com/docs/4.1/components/tooltips/
		 */
		'initTooltips': function () {
			$( '[data-toggle="tooltip"], [rel="tooltip"]' ).tooltip();
		}
	};

	$.utilitiesFunctions = {

		/**
		 * Returns a function, that, as long as it continues to be invoked, will not
		 * be triggered. The function will be called after it stops being called for
		 * N milliseconds. If `immediate` is passed, trigger the function on the
		 * leading edge, instead of the trailing.
		 */
		'debounce': function ( func, wait, immediate ) {
			var timeout;
			return function () {
				var context = this, args = arguments;
				var later = function () {
					timeout = null;
					if ( !immediate ) {
						func.apply( context, args );
					}
				};
				var callNow = immediate && !timeout;
				clearTimeout( timeout );
				timeout = setTimeout( later, wait );
				if ( callNow ) {
					func.apply( context, args );
				}
			};
		},

		/**
		 * Determine if a class is in viewport or not.
		 * @param elem
		 * @returns {boolean}
		 */
		'isElementInViewport': function ( elem ) {
			var $elem = $( elem );

			// Get the scroll position of the page.
			var viewportTop = $( window ).scrollTop();
			var viewportBottom = viewportTop + $( window ).height();

			// Get the position of the element on the page.
			var elemTop = Math.round( $elem.offset().top );
			var elemBottom = elemTop + $elem.height();

			return ((elemTop < viewportBottom) && (elemBottom > viewportTop));
		},

		/**
		 * Get navbar height.
		 * @returns {*}
		 */
		'verifyNavHeight': function () {
			var window_width = $( window ).width();
			var navHeight;
			if ( window_width < 768 ) {
				navHeight = $( '.navbar' ).outerHeight();
			} else {
				navHeight = ($( '.navbar' ).outerHeight() - 15);
			}
			return navHeight;
		},

		/**
		 * Get window width
		 */
		'getWidth': function () {
			if ( this.innerWidth ) {
				return this.innerWidth;
			}

			if ( document.documentElement && document.documentElement.clientWidth ) {
				return document.documentElement.clientWidth;
			}

			if ( document.body ) {
				return document.body.clientWidth;
			}
		},

		/**
		 * Add control-label for each contact form field
		 */
		'addControlLabel': function ( field ) {
			var placeholderField = field.attr( 'placeholder' );
			field.removeAttr( 'placeholder' );
			$( '<label class="control-label"> ' + placeholderField + ' </label>' ).insertBefore( field );
		}
	};

	$.hestia = {
		'init': function () {
			this.navSearch();
			this.getPortfolioModalData();
			this.fixHeaderPadding();
			this.headerSpacingFrontpage();
			this.initCarousel();
			this.initCarouselSwipe();
			this.scrollToTop();
			this.detectIos();
			this.parallaxHeader();
			this.addViewCart();
			this.setSearchSizeInput();
			this.setControlLabel();
			this.styleDefaultSubscribeWidget();
			this.fixElementorTemplates();
			this.handleGutenbergAlignment();
		},

		/**
		 * Fix elementor templates.
		 */
		'fixElementorTemplates': function () {
			if ( $( '.elementor' ).length <= 0 ) {
				return false;
			}
			var navHeight = $( '.navbar' ).outerHeight();
			$( '.elementor-template-full-width header' ).css( 'margin-bottom', navHeight );
			$( '.page-template-template-fullwidth .main.classic-blog' ).css( 'margin-top', navHeight );
			return false;
		},

		/**
		 * Animation for search in menu.
		 */
		'navSearch': function () {
			$( '.hestia-toggle-search' ).on( 'click', function () {
				$( '.navbar' ).toggleClass( 'nav-searching' );
                var navSearching = $( '.nav-searching' );
				navSearching.find( '.hestia-nav-search' ).addClass( 'is-focused' );
				navSearching.find( '.hestia-nav-search' ).find( '.search-field' ).focus();
				$( this ).find( 'i' )
					.fadeOut( 200, function () {
						$( this ).toggleClass( 'fa-search' );
						$( this ).toggleClass( 'fa-times' );
					} ).fadeIn( 200 );
			} );
		},

		/**
		 * This function is used for grabbing post content via Ajax.
		 * It triggers when the modal is open.
		 */
		'getPortfolioModalData': function () {
			$( '#portfolio' ).find( 'a[data-toggle="modal"]' ).on( 'click', function ( e ) {
				e.preventDefault();
				var pid = $( this ).data( 'pid' );

				$.ajax(
					{
						url: requestpost.ajaxurl,
						type: 'post',
						data: {
							action: 'hestia_get_portfolio_item_data',
							pid: pid
						},
						success: function ( result ) {
							var modal = $( '.hestia-portfolio-modal' );
							modal.find( '.modal-content' ).html( result );
							modal.on( 'hidden.bs.modal', function () {
								var html = '<div class="portfolio-loading text-center"><i class="fa fa-3x fa-spin fa-circle-o-notch"></i></div>';
								$( this ).find( '.modal-content' ).html( html );
							} );
						}
					}
				);
			} );
		},

		/**
		 * Add padding in post/page header
		 */
		'fixHeaderPadding': function () {
			/**
			 * This code is related to hestia_header_layout control.
			 * Handle the classic blog header layout.
			 *
			 * Because of the scroll bar on Windows, $( window ).width() is not the same with the media query, so we
			 * need matchMedia to target the media queries.
			 * See more info here: https://www.sitepoint.com/javascript-media-queries/
			 *
			 * - On mobile < 600px with admin bar (which becomes fixed) we have to adjust the margin of the content container
			 * so that the navigation bar doesn't overlap it.
			 * - In all other cases we should just adjust the margin with the navigation bar height.
			 */
			var navbar_height = $( '.navbar-fixed-top' ).outerHeight();


			var mobile_media = window.matchMedia( '(max-width: 600px)' );
			if ( $( '#wpadminbar' ).length && mobile_media.matches ) {
				$( '.wrapper.classic-blog' ).find( '.main' ).css( 'margin-top', navbar_height - 46 );
				$( '.carousel .item .container' ).css( 'padding-top', navbar_height + 50 - 46 );
				if ( $( '.home.page.page-template-default .navbar' ).hasClass( 'no-slider' ) ) {
					$( '.home.page.page-template-default .main' ).css( 'margin-top', navbar_height - 46 );
				}
			} else {
				$( '.header-layout-classic-blog' ).find( '.main' ).css( 'margin-top', navbar_height );
				$( '.carousel .item .container' ).css( 'padding-top', navbar_height + 50 );
				if ( $( '.home.page.page-template-default .navbar' ).hasClass( 'no-slider' ) ) {
					$( '.home.page.page-template-default .main' ).css( 'margin-top', navbar_height );
				}
			}

			if ( $( window ).width() > 768 ) {
				var beaver_offset = 40;
				if ( $( '.wrapper.classic-blog' ).length < 1 ) {
					$( '.pagebuilder-section' ).css( 'padding-top', navbar_height );
				} else {
					$( '.pagebuilder-section' ).css( 'padding-top', 0 );
				}
				$( '.fl-builder-edit .pagebuilder-section' ).css( 'padding-top', navbar_height + beaver_offset );
				$( '.page-header.header-small .container' ).css( 'padding-top', navbar_height + 100 );
				var headerHeight = $( '.single-product .page-header' ).height();
				var offset = headerHeight + 100;
				$( '.single-product .page-header.header-small .container' ).css( 'padding-top', headerHeight - offset );

				var marginOffset = headerHeight - navbar_height - 172;
				$( '.woocommerce.single-product:not(.header-layout-classic-blog) .blog-post .col-md-12 > div[id^=product].product' ).css( 'margin-top', -marginOffset );

			} else {
				$( '.page-header.header-small .container , .woocommerce.single-product .blog-post .col-md-12 > div[id^=product].product' ).removeAttr( 'style' );
			}
			if ( $( '.no-content' ).length ) {
				$( '.page-header.header-small' ).css( 'min-height', navbar_height + 230 );
			}
		},

		/**
		 * Calculate height for .page-header on front page
		 */
		'headerSpacingFrontpage': function () {
			if ( this.inIframe() && this.isMobileUA() ) {
				return;
			}
			if ( $( '.home .carousel' ).length > 0 ) {
				var pageHeader = $( '.page-header' ),
					windowWidth = $( window ).width(),
					windowHeight = $( window ).height();
				if ( windowWidth > 768 ) {
					pageHeader.css( 'min-height', (windowHeight * 0.9) ); // 90% of window height
				} else {
					pageHeader.css( 'min-height', (windowHeight) );
				}
			}
		},

		'inIframe': function () {
			if ( window.self !== window.top ) {
				return true;
			}
			return false;
		},

		'isMobileUA': function () {
			return navigator.userAgent.match( /(iPhone|iPod|iPad|Android|BlackBerry|BB10|mobi|tablet|opera mini|nexus 7)/i );
		},

		/**
		 * Init Carousel
		 */
		'initCarousel': function () {

			var settings = {
				interval: 10000
			};

			/**
			 * Deactivate carousel slide if disable autoslide option in customizer is checked.
			 */
			if ( typeof requestpost.disable_autoslide !== 'undefined' && requestpost.disable_autoslide === '1' ) {
				settings.interval = false;
			}

			/**
			 * Invert carousel buttons in RTL mode
			 */
			if ( $( 'body.rtl' ).length !== 0 ) {

				$( '.carousel-control.left' ).click(
					function () {
						$( '.carousel' ).carousel( 'next' );
					}
				);

				$( '.carousel-control.right' ).click(
					function () {
						$( '.carousel' ).carousel( 'prev' );
					}
				);
			}

			$( '.carousel' ).carousel(
				settings
			);
		},

		/**
		 * Init Carousel Swipe on touch devices.
		 */
		'initCarouselSwipe': function () {

			if ( typeof Hammer === 'undefined' ) {
				return;
			}

			var hammerLeft = 'swipeleft',
				hammerRight = 'swiperight';
			// RTL
			if ( $( 'body.rtl' ).length !== 0 ) {
				hammerLeft = 'swiperight';
				hammerRight = 'swipeleft';
			}

			// Add swipe support on carousel
			if ( $( '#carousel-hestia-generic' ).length !== 0 ) {
				var hestiaCarousel = document.getElementById( 'carousel-hestia-generic' );
				Hammer( hestiaCarousel ).on(
					hammerLeft, function () {
						$( '.carousel' ).carousel( 'next' );
					}
				);
				Hammer( hestiaCarousel ).on(
					hammerRight, function () {
						$( '.carousel' ).carousel( 'prev' );
					}
				);
			}
		},

		/**
		 * Scroll to top feature
		 */
		'scrollToTop': function () {
			var showScrollToTop = 0;
			$( window ).on( 'scroll', function () {

				var y_scroll_pos = window.pageYOffset;
				var scroll_pos_test = $( '.page-header' ).height();
				if ( y_scroll_pos > scroll_pos_test && showScrollToTop === 0 ) {
					$( '.hestia-scroll-to-top' ).addClass( 'hestia-fade' );
					showScrollToTop = 1;
				}

				if ( y_scroll_pos < scroll_pos_test && showScrollToTop === 1 ) {
					$( '.hestia-scroll-to-top' ).removeClass( 'hestia-fade' );
					showScrollToTop = 0;
				}

			} );

			$( '.hestia-scroll-to-top' ).on( 'click', function () {
				window.scroll( {
					top: 0,
					behavior: 'smooth'
				} );
			} );
		},

		/**
		 * Woo sidebar toggle
		 */
		'sidebarToggle': function () {
			// Sidebar toggle
			if ( $( '.blog-sidebar-wrapper,.shop-sidebar-wrapper' ).length <= 0 ) {
				return;
			}

			$( '.hestia-sidebar-open' ).click( function () {
					$( '.sidebar-toggle-container' ).addClass( 'sidebar-open' );
				}
			);

			$( '.hestia-sidebar-close' ).click(
				function () {
					$( '.sidebar-toggle-container' ).removeClass( 'sidebar-open' );
				}
			);
		},

		/**
		 * Detect if browser is iPhone or iPad then add body class
		 */
		'detectIos': function () {
			if ( $( '.hestia-about' ).length > 0 || $( '.hestia-ribbon' ).length > 0 ) {
				var iOS = /iPad|iPhone|iPod/.test( navigator.userAgent ) && !window.MSStream;

				if ( iOS ) {
					$( 'body' ).addClass( 'is-ios' );
				}
			}
		},

		/**
		 * Parallax on blog/archive/page header image
		 */
		'parallaxHeader': function () {
			if ( $( '.header-footer-elementor' ).length > 0 ) {
				return;
			}
			if ( $( '.elementor-location-header' ).length > 0 ) {
				return;
			}
			if ( $( '.fl-theme-builder-header' ).length > 0 ) {
				return;
			}

			var window_width = $( window ).width();
			if ( window_width < 768 ) {
				return;
			}

			var big_image = $( '.page-header[data-parallax="active"]' );
			if ( big_image.length !== 0 ) {
				$( window ).on(
					'scroll', function () {
						if ( $.utilitiesFunctions.isElementInViewport( big_image ) ) {
							var oVal = ($( window ).scrollTop() / 3);
							big_image.css(
								{
									'transform': 'translate3d(0,' + oVal + 'px,0)',
									'-webkit-transform': 'translate3d(0,' + oVal + 'px,0)',
									'-ms-transform': 'translate3d(0,' + oVal + 'px,0)',
									'-o-transform': 'translate3d(0,' + oVal + 'px,0)'
								}
							);
						}
					}
				);
			}
		},

		/**
		 * Add view cart button in its place after adding a product in cart.
		 */
		'addViewCart': function () {
			$( document ).on(
				'DOMNodeInserted', '.added_to_cart', function () {
					if ( !($( this ).parent().hasClass( 'hestia-view-cart-wrapper' ) ) ) {
						$( this ).wrap( '<div class="hestia-view-cart-wrapper"></div>' );
					}
				}
			);
		},

		/**
		 * Add size for each search input in top-bar
		 */
		'setSearchSizeInput': function () {
			if ( $( '.hestia-top-bar' ).find( 'input[type=search]' ).length > 0 ) {
				$( '.hestia-top-bar input[type=search]' ).each(
					function () {
						$( this ).attr( 'size', $( this ).parent().find( '.control-label' ).text().replace( / |…/g, '' ).length );
					}
				);
			}
		},

		/**
		 * Add control-label for each contact form field
		 */
		'setControlLabel': function () {
			var searchForm = $( '.search-form label' );
			if ( typeof (searchForm) !== 'undefined' ) {

				var searchField = $( searchForm ).find( '.search-field' );
				if ( $( searchField ).attr( 'value' ) === '' ) {
					$( searchForm ).addClass( 'label-floating is-empty' );
				} else {
					$( searchForm ).addClass( 'label-floating' );
				}

				$.utilitiesFunctions.addControlLabel( searchField );
			}

			var wooSearchForm = $( '.woocommerce-product-search' );
			if ( typeof (wooSearchForm) !== 'undefined' ) {

				var wooSearchField = $( wooSearchForm ).find( '.search-field' );
				if ( $( wooSearchField ).attr( 'value' ) === '' ) {
					$( wooSearchForm ).addClass( 'label-floating is-empty' );
				} else {
					$( wooSearchForm ).addClass( 'label-floating' );
				}

				$.utilitiesFunctions.addControlLabel( wooSearchField );
			}

			if ( typeof $( '.contact_submit_wrap' ) !== 'undefined' ) {
				$( '.pirate-forms-submit-button' ).addClass( 'btn btn-primary' );
			}

			if ( typeof $( '.form_captcha_wrap' ) !== 'undefined' ) {
				if ( $( '.form_captcha_wrap' ).hasClass( 'col-sm-4' ) ) {
					$( '.form_captcha_wrap' ).removeClass( 'col-sm-6' );
				}
				if ( $( '.form_captcha_wrap' ).hasClass( 'col-lg-6' ) ) {
					$( '.form_captcha_wrap' ).removeClass( 'col-lg-6' );
				}
				$( '.form_captcha_wrap' ).addClass( 'col-md-12' );
			}

			if ( typeof $( 'form' ) !== 'undefined' ) {
				$( 'form' ).addClass( 'form-group' );
			}

			if ( typeof $( 'input' ) !== 'undefined' ) {
				if ( typeof $( 'input[type="text"]' ) !== 'undefined' ) {
					$( 'input[type="text"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input[type="email"]' ) !== 'undefined' ) {
					$( 'input[type="email"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input[type="url"]' ) !== 'undefined' ) {
					$( 'input[type="url"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input[type="password"]' ) !== 'undefined' ) {
					$( 'input[type="password"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input[type="tel"]' ) !== 'undefined' ) {
					$( 'input[type="tel"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input[type="search"]' ) !== 'undefined' ) {
					$( 'input[type="search"]' ).addClass( 'form-control' );
				}

				if ( typeof $( 'input.select2-input' ) !== 'undefined' ) {
					$( 'input.select2-input' ).removeClass( 'form-control' );
				}
			}

			if ( typeof $( 'textarea' ) !== 'undefined' ) {
				$( 'textarea' ).addClass( 'form-control' );
			}

			if ( typeof $( '.form-control' ) !== 'undefined' ) {
				$( '.form-control' ).parent().addClass( 'form-group' );

				$( window ).on(
					'scroll', function () {
						$( '.form-control' ).parent().addClass( 'form-group' );
					}
				);
			}
		},

		/**
		 * Add classes for default subscribe widget.
		 */
		'styleDefaultSubscribeWidget': function () {
			var sibForm = $( '.hestia-subscribe #sib_signup_form_1' );
			sibForm.find( 'p.sib-email-area' ).before( '<span class="input-group-addon"><i class="fa fa-envelope"></i></span>' );
			sibForm.find( 'p.sib-NAME-area' ).before( '<span class="input-group-addon"><i class="fa fa-user"></i></span>' );
			sibForm.find( '.form-group' ).each( function () {
				$( this ).addClass( 'is-empty' );
			} );
		},

		/**
		 * Handle Gutenberg alignments.
		 * @returns {boolean}
		 */
		'handleGutenbergAlignment': function () {
			var bodyNode = $( 'body' );
			if (
				bodyNode.hasClass( 'page-template-template-pagebuilder-full-width' ) ||
				bodyNode.hasClass( 'page-template-template-pagebuilder-blank' ) ||
				bodyNode.hasClass( 'page-template-template-page-sidebar' )
			) {
				return false;
			}
			if ( $( '.main #secondary' ).length > 0 ) {
				return false;
			}

			var fullAlignments = $( '.alignfull' );
			var wideAlignments = $( '.alignwide' );

			if ( !fullAlignments.length && !wideAlignments.length ) {
				return false;
			}

			var mainWrapWidth = $( '.main' ).innerWidth();

			if ( fullAlignments.length ) {
				$( fullAlignments ).each( function ( index, element ) {
					$( element ).css( {
						'margin-left': '0',
						'margin-right': '0'
					} );

					var margin = (mainWrapWidth - $( element ).innerWidth()) / 2;

					$( element ).css( {
						'margin-left': '-' + margin + 'px',
						'margin-right': '-' + margin + 'px'
					} );
				} );
			}
			if ( wideAlignments.length ) {
				$( wideAlignments ).each( function ( index, element ) {
					$( element ).css( {
						'margin-left': '0',
						'margin-right': '0'
					} );
					var margin = (mainWrapWidth - $( element ).innerWidth()) / 5;

					$( element ).css( {
						'margin-left': '-' + margin + 'px',
						'margin-right': '-' + margin + 'px'
					} );
				} );
			}
		},
		'isMobile': function () {
			var windowWidth = window.innerWidth;
			return windowWidth <= 991;
		},
	};

	$.navigation = {
		/**
		 * Initialize navigation.
		 */
		'init': function () {
			this.toggleNavbarTransparency();
			this.handleResponsiveDropdowns();
			this.handleTouchDropdowns();
			this.repositionDropdowns();
			this.smoothScroll();
			this.activeParentLink();
			this.highlightMenu();
			this.setBodyOverflow();
		},

		/**
		 * Handles dropdowns on touch devices.
		 * @returns {boolean}
		 */
		'handleTouchDropdowns': function () {
			var windowWidth = window.innerWidth;
			if ( windowWidth < 991 ) {
				return false;
			}

			var self = this;
			$( '.caret-wrap' ).on( 'touchstart', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				var menuItem = $( this ).closest( 'li' );
				if ( $( menuItem ).hasClass( 'dropdown-submenu' ) ) {
					$( menuItem ).siblings().removeClass( 'open' ).find( 'dropdown-submenu' ).removeClass( 'open' );
					$( menuItem ).siblings().find( '.caret-open' ).removeClass( 'caret-open' );
				}
				if ( $( this ).closest( 'li' ).parent().is( '.nav' ) ) {
					self.clearDropdowns();
				}
				$( this ).toggleClass( 'caret-open' );
				$( this ).closest( '.dropdown' ).toggleClass( 'open' );
				self.createOverlay();
			} );
			return false;
		},

		/**
		 * Create helper overlay used for touch dropdowns.
		 * @returns {boolean}
		 */
		'createOverlay': function () {
			var dropdownOverlay = $( '.dropdown-helper-overlay' );
			if ( dropdownOverlay.length > 0 ) {
				return false;
			}
			var self = this;
			dropdownOverlay = document.createElement( 'div' );
			dropdownOverlay.setAttribute( 'class', 'dropdown-helper-overlay' );
			$( '#main-navigation' ).append( dropdownOverlay );
			$( '.dropdown-helper-overlay' ).on( 'touchstart click', function () {
				this.remove();
				self.clearDropdowns();
			} );
			return false;
		},

		'clearDropdowns': function () {
			$( '.dropdown.open' ).removeClass( 'open' );
			$( '.caret-wrap.caret-open' ).removeClass( 'caret-open' );
		},

		/**
		 * Toggle navbar transparency
		 */
		'toggleNavbarTransparency': function () {

			var navbarHome = $( '.navbar-color-on-scroll' );
			if ( navbarHome.length === 0 ) {
				return;
			}

			var transparent = true,
				headerWithTopbar = 0;

			if ( navbarHome.hasClass( 'header-with-topbar' ) ) {
				headerWithTopbar = 40;
			}

			$( window ).on(
				'scroll', $.utilitiesFunctions.debounce(
					function () {
						if ( !$( '.home.page .navbar' ).hasClass( 'no-slider' ) ) {
							if ( $( document ).scrollTop() > headerWithTopbar ) {
								if ( transparent ) {
									transparent = false;
									navbarHome.removeClass( 'navbar-transparent' );
									navbarHome.addClass( 'navbar-not-transparent' );
								}
							} else {
								if ( !transparent ) {
									transparent = true;
									navbarHome.addClass( 'navbar-transparent' );
									navbarHome.removeClass( 'navbar-not-transparent' );
								}
							}
						}
					}, 17
				)
			);
		},

		/**
		 * Handle the responsive menus under 769px.
		 */
		'handleResponsiveDropdowns': function () {
			var windowWidth = window.innerWidth;
			if ( windowWidth > 768 ) {
				return false;
			}
			$( '.navbar .dropdown > a .caret-wrap' ).on( 'click touchend',
				function ( event ) {
					var caret = $( this );
					event.preventDefault();
					event.stopPropagation();
					//Change caret wrap.
					$( caret ).toggleClass( 'caret-open' );
					//Open dropdown.
					$( caret ).parent().siblings().toggleClass( 'open' );
				}
			);
		},

		/**
		 * Smooth scroll when click on menu items anchors.
		 */
		'smoothScroll': function () {
			$( '.navbar a[href*="#"], a.btn[href*="#"]' ).click(
				function () {
					//Do nothing if it's an empty hash.
					if ( $( this ).attr( 'href' ) === '#' ) {
						return false;
					}
					if ( location.pathname.replace( /^\//, '' ) === this.pathname.replace( /^\//, '' ) && location.hostname === this.hostname ) {
						var target = $( this.hash );
						target = target.length ? target : $( '[name=' + this.hash.slice( 1 ) + ']' );
						if ( target.length ) {
							$( 'html,body' ).animate(
								{
									scrollTop: (target.offset().top - $.utilitiesFunctions.verifyNavHeight())
								}, 1200
							);

							// Hide drop-down and submenu
							if ( $( '.navbar .navbar-collapse' ).hasClass( 'in' ) ) {
								$( '.navbar .navbar-collapse.in' ).removeClass( 'in' );
							}
							if ( $( 'body' ).hasClass( 'menu-open' ) ) {
								$( 'body' ).removeClass( 'menu-open' );
								$( '.navbar-collapse' ).css( 'height', '0' );
								$( '.navbar-toggle' ).attr( 'aria-expanded', 'false' );
							}

							return false;
						}
					}
				}
			);
		},

		/**
		 * Add active parent links on navigation
		 */
		'activeParentLink': function () {
			$( '.navbar .dropdown > a' ).click(
				function () {
					//Do nothing if it's an empty hash.
					if ( $( this ).attr( 'href' ) === '#' ) {
						return false;
					}
					location.href = this.href;
					return false;
				}
			);
		},

		/**
		 * Highlight menu items when section is in viewport only if section is in root of menu, not submenu.
		 */
		'highlightMenu': function () {
			$( window ).on(
				'scroll', function () {
					if ( $( 'body' ).hasClass( 'home' ) ) {
						if ( $( window ).width() >= 751 ) {
							var hestia_scrollTop = $( window ).scrollTop(); // cursor position
							var headerHeight = $( '.navbar' ).outerHeight(); // header height
							var isInOneSection = 'no'; // used for checking if the cursor is in one section or not
							// for all sections check if the cursor is inside a section
							$( '#carousel-hestia-generic, section' ).each(
								function () {
									var thisID = '#' + $( this ).attr( 'id' ); // section id
									var hestia_offset = $( this ).offset().top; // distance between top and our section
									var thisHeight = $( this ).outerHeight(); // section height
									var thisBegin = hestia_offset - headerHeight; // where the section begins
									var thisEnd = hestia_offset + thisHeight - headerHeight; // where the section ends
									// if position of the cursor is inside of the this section
									if ( hestia_scrollTop + $.utilitiesFunctions.verifyNavHeight() >= thisBegin &&
										hestia_scrollTop + $.utilitiesFunctions.verifyNavHeight() <= thisEnd ) {
										isInOneSection = 'yes';
										$( 'nav .on-section' ).removeClass( 'on-section' );
										$( 'nav a[href$="' + thisID + '"]' ).parent( 'li' ).addClass( 'on-section' ); // find the menu button with the same ID section
										return false;
									}
									if ( isInOneSection === 'no' ) {
										$( 'nav .on-section' ).removeClass( 'on-section' );
									}
								}
							);
						}
					}
				}
			);
		},

		/**
		 * Set body overflow on mobile when menu is opened/closed.
		 */
		'setBodyOverflow': function () {
			var navigation = $( '#main-navigation' );
			navigation.on(
				'show.bs.collapse', function () {
					$( 'body' ).addClass( 'menu-open' );
				}
			);
			navigation.on(
				'hidden.bs.collapse', function () {
					$( 'body' ).removeClass( 'menu-open' );
				}
			);
		},

		/**
		 * Reposition dropdowns if they are out of screen.
		 * @returns {boolean}
		 */
		'repositionDropdowns': function () {
			var windowWidth = window.innerWidth;
			//Do nothing on mobile.
			if ( windowWidth <= 768 ) {
				return false;
			}
			//Do nothing without dropdowns.
			var dropdowns = $( '.dropdown-menu' );
			if ( dropdowns.length === 0 ) {
				return false;
			}
			//Loop dropdowns and move them if needed.
			$.each( dropdowns, function ( key, dropdown ) {
				var submenu = $( dropdown );
				var bounding = submenu.offset().left;
				if ( /webkit.*mobile/i.test( navigator.userAgent ) ) {
					bounding -= window.scrollX;
				}
				var dropdownWidth = submenu.outerWidth();
				if ( bounding + dropdownWidth >= windowWidth ) {
					$( dropdown ).css( { 'right': '100%', 'left': 'auto' } );
				}
			} );
			return false;
		}
	};

	var navbarScrollPoint = 0;
	$.hestiaNavBarScroll = {

		/**
		 * Check navbar scroll point.
		 */
		'checkNavbarScrollPoint': function () {
			if ( $( '.navbar-header' ).length === 0 ) {
				return false;
			}

			// Window width bigger or equal with 768px
			if ( $.utilitiesFunctions.getWidth() >= 768 ) {
				if ( typeof $( '.navbar-header' ).offset() !== 'undefined' ) {
					var topOffset = $( '.navbar-header' ).offset().top;
					// Account for mobile safari (which reports broken values for offset).
					if ( /webkit.*mobile/i.test( navigator.userAgent ) ) {
						topOffset -= window.scrollY;
					}
					navbarScrollPoint = topOffset + $( '.navbar-header' ).height(); // Distance from top to the bottom of the logo
				}

				// Check if topbar is active when navbar is left aligned
				if ( $( '.hestia_left.header-with-topbar' ).length !== 0 || $( '.full-screen-menu.header-with-topbar' ).length !== 0 ) {
					navbarScrollPoint = 40;
				}

				// Window width less than 768px
			} else {
				// Check if topbar is active
				if ( $( '.header-with-topbar' ).length !== 0 ) {
					navbarScrollPoint = 40; // Topbar height

				} else {
					navbarScrollPoint = 0; // Topbar disabled
				}
			}
		},

		/**
		 * On screen scroll add scroll-related class.
		 * This function is related with top bar. It decides when to show or hide it.
		 */
		'addScrollClass': function () {
			$( window ).on(
				'scroll', function () {
					if ( $( document ).scrollTop() >= navbarScrollPoint ) {
						$( '.navbar' ).addClass( 'navbar-scroll-point' );
					} else {
						$( '.navbar' ).removeClass( 'navbar-scroll-point' );
					}
				}
			);
		}

	};
}( jQuery ));


jQuery( document ).ready(
	function () {
		jQuery.material.init();
		jQuery.hestia.init();
		jQuery.navigation.init();
		jQuery.hestiaFeatures.initAnimations();
		jQuery.hestiaFeatures.initTooltips();
		jQuery.hestiaNavBarScroll.checkNavbarScrollPoint();
		jQuery.hestiaNavBarScroll.addScrollClass();

	}
);

jQuery( window ).on( 'load',
	function () {
		jQuery.hestiaFeatures.initMasonry();
		jQuery.hestia.sidebarToggle();
	}
);

jQuery( window ).resize(
	function () {
		jQuery.hestiaFeatures.initMasonry();
		jQuery.hestia.fixHeaderPadding();
		jQuery.hestia.headerSpacingFrontpage();
		jQuery.hestia.handleGutenbergAlignment();
		jQuery.hestiaNavBarScroll.checkNavbarScrollPoint();
		jQuery.navigation.repositionDropdowns();
	}
);
