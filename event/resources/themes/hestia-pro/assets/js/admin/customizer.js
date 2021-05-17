/**
 * Main customize js file
 *
 * @package Hestia
 */

/* global initializeAllElements */
/* global AOS */
/* exported hestiaGetCss */

( function( $ ) {

    /**
     * This handles the customizer live actions
     */
    $.hestiaCustomizeLive = {
        'init':function () {
            this.liveShowHideSection();
            this.liveTextReplace();
            this.liveBackgroundReplace();
            this.livePricingSection();
            this.sliderHeightFix();
        },

        /**
         * Fix the slider height.
         */
        'sliderHeightFix': function() {
            var windowWidth = $( window ).width();
            var windowHeight = $( window ).height();

            if ( windowWidth > 768 ) {
                $( '.carousel .page-header' ).css( 'min-height', (windowHeight * 0.9) ); // 90% of window height
            } else {
                $( '.carousel .page-header' ).css( 'min-height', (windowHeight) ); // 90% of window height
            }
        },


        /**
         * This function handle the action when a user clicks on show/hide customizer control.
         * It toggles the section and then refresh animations.
         */
        'liveShowHideSection':function () {
            var showHideControls = {
                'hestia_big_title_hide' : '#carousel-hestia-generic',
                'hestia_features_hide' : '.hestia-features',
                'hestia_about_hide' : '.hestia-about',
                'hestia_shop_hide' : '.hestia-shop',
                'hestia_portfolio_hide' : '.hestia-work',
                'hestia_team_hide' : '.hestia-team',
                'hestia_pricing_hide' : '.hestia-pricing',
                'hestia_ribbon_hide' : '.hestia-ribbon',
                'hestia_testimonials_hide' : '.hestia-testimonials',
                'hestia_subscribe_hide' : '.hestia-subscribe',
                'hestia_clients_bar_hide' : '.hestia-clients-bar',
                'hestia_blog_hide' : '.hestia-blogs',
                'hestia_contact_hide' : '.hestia-contact'

            };
            Object.keys(showHideControls).forEach(function (key) {
                wp.customize(
                    key, function (value) {
                        value.bind(
                            function(newval){
                                $(showHideControls[key]).toggle();
                                if( newval === true ){
                                    if ( typeof AOS !== 'undefined' ) {
                                        AOS.refresh();
                                    }
                                }
                            }
                        );
                    }
                );
            });
        },

        /**
         * This function handle the action when a user change a simple html input.
         * It target the class and then replace the inside of it with the new value.
         */
        'liveTextReplace': function () {
            var textToReplace = [
                { controlName:'hestia_features_title', selector:'.hestia-features .hestia-title', isHtml:true },
                { controlName:'hestia_features_subtitle', selector:'.hestia-features .description', isHtml:true },
                { controlName:'hestia_shop_title', selector:'.hestia-shop .hestia-title', isHtml:true },
                { controlName:'hestia_shop_subtitle', selector:'.hestia-shop .description', isHtml:true },
                { controlName:'hestia_portfolio_title', selector:'.hestia-work .hestia-title', isHtml:true },
                { controlName:'hestia_portfolio_subtitle', selector:'.hestia-work .description', isHtml:true },
                { controlName:'hestia_team_title', selector:'.hestia-team .hestia-title', isHtml:true },
                { controlName:'hestia_team_subtitle', selector:'.hestia-team .description', isHtml:true },
                { controlName:'hestia_pricing_title', selector:'.hestia-pricing .hestia-title', isHtml:true },
                { controlName:'hestia_pricing_subtitle', selector:'.hestia-pricing p.text-gray', isHtml:true },
                { controlName:'hestia_pricing_table_one_title', selector:'.hestia-pricing .hestia-table-one .category', isHtml:true },
                { controlName:'hestia_pricing_table_one_price', selector:'.hestia-pricing .hestia-table-one .card-title', isHtml:true },
                { controlName:'hestia_pricing_table_one_text', selector:'.hestia-pricing .hestia-table-one .btn', isHtml:true },
                { controlName:'hestia_pricing_table_two_title', selector:'.hestia-pricing .hestia-table-two .category', isHtml:true },
                { controlName:'hestia_pricing_table_two_price', selector:'.hestia-pricing .hestia-table-two .card-title', isHtml:true },
                { controlName:'hestia_pricing_table_two_text', selector:'.hestia-pricing .hestia-table-two .btn', isHtml:true },
                { controlName:'hestia_ribbon_text', selector:'.hestia-ribbon .hestia-title', isHtml:true },
                { controlName:'hestia_ribbon_button_text', selector:'.hestia-ribbon .hestia-ribbon-content-right a', isHtml:true },
                { controlName:'hestia_testimonials_title', selector:'.hestia-testimonials .hestia-title', isHtml:true },
                { controlName:'hestia_testimonials_subtitle', selector:'.hestia-testimonials .description', isHtml:true },
                { controlName:'hestia_subscribe_title', selector:'.hestia-subscribe .title', isHtml:true },
                { controlName:'hestia_subscribe_subtitle', selector:'.hestia-subscribe .subscribe-description', isHtml:true },
                { controlName:'hestia_blog_title', selector:'.hestia-blogs .hestia-title', isHtml:true },
                { controlName:'hestia_blog_subtitle', selector:'.hestia-blogs .description', isHtml:true },
                { controlName:'hestia_contact_title', selector:'.hestia-contact .hestia-contact-title-area .hestia-title', isHtml:true },
                { controlName:'hestia_contact_subtitle', selector:'.hestia-contact h5.description', isHtml:true },
                { controlName:'hestia_contact_area_title', selector:'.hestia-contact .card-contact .card-title', isHtml:true },
                { controlName:'hestia_blog_subscribe_title', selector:'#subscribe-on-blog h3.hestia-title', isHtml:true },
                { controlName:'hestia_blog_subscribe_subtitle', selector:'#subscribe-on-blog p.description', isHtml:true }
            ];
            textToReplace.forEach(function (item) {
                wp.customize(
                    item.controlName, function( value ) {
                        value.bind(
                            function( newval ) {
                                if(typeof item.isHtml !== 'undefined' ){
                                    $( item.selector ).html( newval );
                                } else {
                                    $( item.selector ).text( newval );
                                }
                            }
                        );
                    }
                );
            });
        },

        /**
         * This function handle the action when a user change the background of a section.
         * It target the class and then replace the background. If the input is empty,
         * it will toggle the class section-image (or you can specify what class to toggle),
         * class that adds overlay and make the text white.
         */
        'liveBackgroundReplace': function () {
            var backgroundImages = [
                {controlName: 'hestia_feature_thumbnail', selector:'.hestia-about'},
                {controlName: 'hestia_ribbon_background', selector:'.hestia-ribbon'},
                {controlName: 'hestia_subscribe_background', selector:'.hestia-subscribe', toggleClass:'subscribe-line-image'},
                {controlName: 'hestia_contact_background', selector:'.hestia-contact'}
            ];
            backgroundImages.forEach(function (item) {
                wp.customize(
                    item.controlName, function( value ) {
                        value.bind(
                            function( newval ) {
                                $( item.selector ).css( 'background-image', 'url('+newval+')' );
                                var toggleClass = 'section-image';
                                if( typeof item.toggleClass !== 'undefined' ){
                                    toggleClass = item.toggleClass;
                                }
                                if ( newval === '' ) {
                                    $( item.selector ).removeClass( toggleClass );
                                } else {
                                    $( item.selector ).addClass( toggleClass );
                                }
                            }
                        );
                    }
                );
            });
        },

        'updatePricingTableIcon': function( newval, tableNumber ){
            var accent_color = wp.customize._value.accent_color();
            var packageSelector = '.home .hestia-pricing .hestia-table-' + tableNumber + ' .hestia-pricing-icon-wrapper';

            if ( newval ) {
                $( packageSelector + ' i' ).removeClass().addClass( newval );
                $( packageSelector ).addClass( 'pricing-has-icon' ).css( {'display' : 'block', 'color' : accent_color } );
            } else {
                $( packageSelector + '.pricing-has-icon .card-title' ).css( { 'font-size' : '60px', 'margin-top' : '30px'  } );
                $( packageSelector + '.pricing-has-icon .card-title small' ).css( { 'color' : '#777', 'top' : '-17px', 'font-size' : '26px', 'font-weight' : 'normal', 'line-height' : '1'  } );
                $( packageSelector + '.pricing-has-icon' ).removeClass( 'pricing-has-icon' ).css( 'display', 'none' );
            }
        },
        /**
         * This function handle the action when a user change the content of pricing tables.
         */
        'livePricingSection':function(){
            var self = this;
            wp.customize(
                'hestia_pricing_table_one_features', function( value ) {
                    value.bind(
                        function( newval ) {
                            var result = self.parsePricingFeatures(newval);
                            $( '.hestia-pricing .hestia-table-one ul' ).html( result );
                        }
                    );
                }
            );

            wp.customize(
                'hestia_pricing_table_two_features', function( value ) {
                    value.bind(
                        function( newval ) {
                            var result = self.parsePricingFeatures(newval);
                            $( '.hestia-pricing .hestia-table-two ul' ).html( result );
                        }
                    );
                }
            );

            /* Live refresh for pricing icon, table one */
            wp.customize(
                'hestia_pricing_table_one_icon', function( value ) {
                    value.bind(
                        function( newval ) {
                            $.hestiaCustomizeLive.updatePricingTableIcon( newval, 'one' );
                        }
                    );
                }
            );
            /* Live refresh for pricing icon, table two */
            wp.customize(
                'hestia_pricing_table_two_icon', function( value ) {
                    value.bind(
                        function( newval ) {
                            $.hestiaCustomizeLive.updatePricingTableIcon( newval, 'two' );
                        }
                    );
                }
            );
        },

        /**
         * This function parse the content of pricing table and create html for it.
         */
        'parsePricingFeatures': function (newval) {
            var val = newval.replace('\\r', '');
            var features = val.split('\\n');
            var result = '';
            features.forEach(function (feature) {
                result += '<li>'+feature+'</li>';
            });
            return result;
        },
    };
    $.hestiaCustomizeLive.init();

    /**
     * This extends jQuery functionality and adds a function to check if a jQuery element contain
     * any of classes in an array.
     */
    $.fn.extend({
        hasClasses: function (selectors) {
            var self = this;
            for (var i in selectors) {
                if ($(self).hasClass(selectors[i])) {
                    return true;
                }
            }
            return false;
        }
    });

    /**
     * This handles the customizer shortcuts.
     * Send events on click etc.
     */
    $.hestiaCustomize = {
        'init': function () {
            this.focusForCustomShortcut();
            this.focusTab();
            this.handleShowHideShortcut();
            this.addShortcutForMenu();
            this.handleTextEditor();
            this.handleTopBarWidgetFocus();
            this.handleShowHideSlider();
        },

        /**
         * This function does the focus on controls when the user clicks on a custom shortcut.
         * If controls are in a tab, it does focus on tab too.
         */
        'focusForCustomShortcut': function () {
            /**
             * All controls that have custom shortcuts
             */
            var fakeShortcutClasses = [
                'hestia_features_hide',
                'hestia_features_title',
                'hestia_about_hide',
                'hestia_shop_hide',
                'hestia_shop_title',
                'hestia_portfolio_hide',
                'hestia_portfolio_title',
                'hestia_team_hide',
                'hestia_team_title',
                'hestia_pricing_hide',
                'hestia_pricing_title',
                'hestia_pricing_table_one_title',
                'hestia_pricing_table_two_title',
                'hestia_ribbon_hide',
                'hestia_ribbon_text',
                'hestia_testimonials_hide',
                'hestia_testimonials_title',
                'hestia_clients_bar_hide',
                'hestia_subscribe_hide',
                'hestia_subscribe_title',
                'hestia_blog_hide',
                'hestia_blog_title',
                'hestia_contact_hide',
                'hestia_contact_title',
                'hestia_contact_info'
            ];

            /**
             * Controls that have custom shortcuts and are in a tab in customizer.
             * We need to focus on tab too if clicked on one of those controls.
             */
            var controlsInTabs = [
                'hestia_pricing_title',
                'hestia_pricing_table_one_title',
                'hestia_pricing_table_two_title',
                'hestia_subscribe_hide',
                'hestia_subscribe_title',
                'hestia_contact_hide',
                'hestia_contact_title',
                'hestia_contact_info'
            ];
            fakeShortcutClasses.forEach(function (element) {
                $('.customize-partial-edit-shortcut-'+element).on('click',function () {
                    if( controlsInTabs.indexOf(element) > -1) {
                        var tabToActivate = $('.hestia-customizer-tab>.' + element);
                        wp.customize.preview.send('focus-control', element);
                        wp.customize.preview.send('tab-previewer-edit', tabToActivate);
                    } else {
                        wp.customize.preview.send( 'hestia-customize-focus-control', element );
                    }
                });
            });
        },
        'handleShowHideSlider': function () {
            wp.customize(
                'hestia_big_title_hide', function( value ) {
                    'use strict';
                    value.bind(
                        function( to ) {

                            var navbar_fixed = $( '.home.page .navbar-fixed-top' );
                            var navbar_height = navbar_fixed.outerHeight();
                            if( navbar_fixed.hasClass('navbar-transparent') ){
                                navbar_height -= 15;
                            }

                            if( to === true ){
                                $('.home.page .navbar').addClass('no-slider navbar-not-transparent');
                                var mobile_media = window.matchMedia( '(max-width: 600px)' );
                                if( $('#wpadminbar').length && mobile_media.matches ) {
                                    $('.home.page .main').css( 'margin-top', navbar_height - 46 );
                                } else {
                                    $('.home.page .main').css( 'margin-top', navbar_height );
                                }
                            } else {
                                $('.home.page .navbar').removeClass('no-slider navbar-not-transparent');
                                $('.home.page .main').removeAttr('style');
                                $.navigation.toggleNavbarTransparency();
                            }

                            if( typeof (wp.customize._value.hestia_navbar_transparent ) !== 'undefined' && wp.customize._value.hestia_navbar_transparent() === true ){
                                if( to === true ){
                                    $('.home.page .navbar').removeClass('navbar-transparent');
                                    $('.home.page .navbar').removeClass('navbar-color-on-scroll');
                                } else {
                                    $('.home.page .navbar').addClass('navbar-transparent');
                                    $('.home.page .navbar').addClass('navbar-color-on-scroll');
                                    $.navigation.toggleNavbarTransparency();
                                }
                            } else {
                                if( typeof (wp.customize._value.hestia_navbar_transparent ) === 'undefined' ){
                                    if( to === true ){
                                        $('.home.page .navbar').removeClass('navbar-transparent');
                                        $('.home.page .navbar').removeClass('navbar-color-on-scroll');
                                    } else {
                                        $('.home.page .navbar').addClass('navbar-transparent');
                                        $('.home.page .navbar').addClass('navbar-color-on-scroll');
                                        $.navigation.toggleNavbarTransparency();
                                    }
                                } else {
                                    $('.home.page .navbar').removeClass('navbar-transparent');
                                    $('.home.page .navbar').removeClass('navbar-color-on-scroll');
                                }
                            }
                        }
                    );
                }
            );
        },
        'focusTab': function () {
            $( '.customize-partial-edit-shortcut' ).bind(
                'DOMNodeInserted', function () {
                    $( this ).on(
                        'click', function() {
                            var controlId     = $( this ).attr( 'class' );
                            var tabToActivate = '';
                            var controlFinalId = controlId.split( ' ' ).pop().split( '-' ).pop();

                            if ( controlId.indexOf( 'widget' ) !== -1 ) {
                                tabToActivate = $( '.hestia-customizer-tab>.widgets' );
                            } else {
                                tabToActivate      = $( '.hestia-customizer-tab>.' + controlFinalId );
                            }

                            wp.customize.preview.send( 'tab-previewer-edit', tabToActivate );
                            wp.customize.preview.send( 'focus-control', controlFinalId );
                        }
                    );
                }
            );
        },

        /**
         * This function triggers click on show/hide control when user clicks on one of their custom shortcut.
         */
        'handleShowHideShortcut': function () {
            var classesToLook = [
                'hestia_features_hide',
                'hestia_about_hide',
                'hestia_shop_hide',
                'hestia_portfolio_hide',
                'hestia_team_hide',
                'hestia_pricing_hide',
                'hestia_ribbon_hide',
                'hestia_testimonials_hide',
                'hestia_clients_bar_hide',
                'hestia_subscribe_hide',
                'hestia_blog_hide',
                'hestia_contact_hide'];

            classesToLook.forEach(function(element){
                $( '.customize-partial-edit-shortcut-'+element ).on( 'click', function() {
                    wp.customize.preview.send('hestia-customize-disable-section', element);
                });
            });
        },

        /**
         * Add shortcut button for primary menu.
         */
        'addShortcutForMenu' : function () {
            var primaryMenu = $('.navbar-nav');
            var menuShortcutHtml = '<span class="menu-shortcut customize-partial-edit-shortcut customize-partial-edit-shortcut-primary-menu"><button class="customize-partial-edit-shortcut-button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg></button></span>';
            primaryMenu.before(menuShortcutHtml);
            this.handleMenuShortcutClick();
        },

        /**
         * Handle the click of shortcut of the menu
         */
        'handleMenuShortcutClick': function () {
            $( '.menu-shortcut' ).on( 'click', function() {
                wp.customize.preview.send('trigger-focus-menu');
            });
        },

        /**
         * Handle the opening of text editor
         */
        'handleTextEditor': function(){
            $(document).on('DOMNodeInserted','.customize-partial-edit-shortcut', function() {
                $( this ).on(
                    'click', function(){
                        var controls = ['hestia_contact_content_new'];
                        var clickedControl = $( this ).attr('class');
                        var openControl = '';
                        $.each(controls, function(index, value){
                            if (clickedControl.indexOf( value ) !== -1){
                                openControl = value;
                                return false;
                            }
                        });
                        if( openControl !== ''){
                            wp.customize.preview.send( 'trigger-open-editor', openControl );
                        } else {
                            wp.customize.preview.send( 'trigger-close-editor');
                        }
                    }
                );
            });
        },

        'handleTopBarWidgetFocus': function(){
            $('.customize-partial-edit-shortcut-hestia-top-bar-widget').on('click', function(){
                wp.customize.preview.send('focus-section', 'sidebar-widgets-sidebar-top-bar');
            });
        }
    };

    $.hestiaCustomize.init();

    /**
     * Live refresh for container width
     */
    wp.customize(
        'hestia_container_width', function( value ) {
            'use strict';
            value.bind(
                function( to ) {
                    if ( to ) {
                        var values = JSON.parse( to );
                        if ( values ) {
                            if ( values.mobile ) {
                                var settings = {
                                    selectors: 'div.container',
                                    cssProperty: 'width',
                                    propertyUnit: 'px',
                                    styleClass: 'hestia-container-width-css'
                                }, val;
                                val          = JSON.parse( to );
                                hestiaSetCss( settings, val );
                            }
                        }
                    }
                }
            );
        }
    );

    // Site Identity > Site Title
    wp.customize(
        'blogname', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.navbar .navbar-brand p' ).text( newval );
                }
            );
        }
    );

    // Site Identity > Site Description
    wp.customize(
        'blogdescription', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.blog .page-header .title' ).text( newval );
                }
            );
        }
    );

    // Appearance Settings > General Settings > Boxed Layout
    wp.customize(
        'hestia_general_layout', function( value ) {
            value.bind(
                function() {
                    var navbar_height = $( '.navbar-fixed-top' ).outerHeight();
                    if ( $( '.main' ).hasClass( 'main-raised' ) ) {
                        $( '.main' ).removeClass( 'main-raised' );
                        $( '.main.classic-blog' ).css( 'margin-top', navbar_height );
                    } else {
                        $( '.main' ).addClass( 'main-raised' );
                        $('.main.classic-blog').css('margin-top', navbar_height);
                    }
                }
            );
        }
    );

    // Appearance Settings > General Settings > Footer Credits
    wp.customize(
        'hestia_general_credits', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.footer-black .copyright' ).html( newval );
                }
            );
        }
    );

    // Footer Options > Alternative Footer Style
    wp.customize(
        'hestia_alternative_footer_style', function( value ) {
            value.bind(
                function() {
                    var footer = $( '.footer.footer-big' );
                    if ( footer.hasClass( 'footer-black' ) ) {
                        footer.removeClass( 'footer-black' );
                    } else {
                        footer.addClass( 'footer-black' );
                    }
                }
            );
        }
    );

    // Appearance Settings > Appearance Settings > General Settings > Sidebar Width
    wp.customize(
        'hestia_sidebar_width', function( value ) {
            value.bind(
                function( newval ) {
                    if ( $( 'body > .wrapper' ).width() > 991 ) {
                        var layout = wp.customize._value.hestia_page_sidebar_layout(), hestia_content_width, content_width;
                        if (layout !== 'full-width' && layout !== '') {
                            hestia_content_width = 100 - newval;

                            if (newval <= 3 || newval >= 80) {
                                hestia_content_width = 100;
                                newval               = 100;
                            }
                            content_width = hestia_content_width - 8.33333333;

                            $( '.content-sidebar-left, .content-sidebar-right, .page-content-wrap' ).css( 'width', hestia_content_width + '%' );
                            $( '.blog-sidebar-wrapper:not(.no-variable-width), .shop-sidebar.col-md-3' ).css( 'width', newval + '%' );
                        }

                        // layout = wp.customize._value.hestia_blog_sidebar_layout();
                        layout = $( '.single-post-container' ).data('layout');
                        if( typeof layout === 'undefined' ){
                            layout = $( '.hestia-blogs' ).data('layout');
                        }
                        if (layout !== 'full-width' && layout !== '') {
                            hestia_content_width = 100 - newval;

                            if (newval <= 3 || newval >= 80) {
                                hestia_content_width = 100;
                                newval               = 100;
                                if (layout === 'sidebar-left') {
                                    $( '.blog-posts-wrap, .archive-post-wrap, .single-post-container' ).removeClass( 'col-md-offset-1' );
                                } else {
                                    $( 'body:not(.page) .blog-sidebar-wrapper:not(.no-variable-width)' ).removeClass( 'col-md-offset-1' );
                                }
                            } else {
                                if (layout === 'sidebar-left') {
                                    $( '.blog-posts-wrap, .archive-post-wrap, .single-post-container' ).addClass( 'col-md-offset-1' );
                                } else {
                                    $( 'body:not(.page) .blog-sidebar-wrapper:not(.no-variable-width)' ).addClass( 'col-md-offset-1' );
                                }
                            }
                            content_width = hestia_content_width - 8.33333333;

                            $( '.blog-posts-wrap, .archive-post-wrap, .single-post-container' ).css( 'width', content_width + '%' );
                            $( '.blog-sidebar-wrapper:not(.no-variable-width), .shop-sidebar-wrapper' ).css( 'width', newval + '%' );
                        }


                      layout = typeof wp.customize._value.hestia_shop_sidebar_layout !== 'undefined' ? wp.customize._value.hestia_shop_sidebar_layout() : '';
	                    if (layout !== 'full-width' && layout !== '') {
		                    hestia_content_width = 100 - newval;

		                    if (newval <= 3 || newval >= 80) {
			                    hestia_content_width = 100;
			                    newval               = 100;
		                    }

		                    $( '.content-sidebar-left, .content-sidebar-right, .page-content-wrap' ).css( 'width', hestia_content_width + '%' );
		                    $( '.blog-sidebar-wrapper:not(.no-variable-width), .shop-sidebar.col-md-3' ).css( 'width', newval + '%' );
	                    }
                    }
                }
            );
        }
    );

    // Frontpage Sections > Portfolio  > Title
    wp.customize(
        'hestia_portfolio_title', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-work .title' ).text( newval );
                }
            );
        }
    );

    // Frontpage Sections > Portfolio  > Subtitle
    wp.customize(
        'hestia_portfolio_subtitle', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-work .description' ).text( newval );
                }
            );
        }
    );

    // Frontpage Sections > Contact  > Background
    wp.customize(
        'hestia_contact_background', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.contactus' ).css( 'background-image', 'url(' + newval + ')' );
                }
            );
        }
    );

    // Blog Settiungs > Authors Section > Background
    wp.customize(
        'hestia_authors_on_blog_background', function( value ) {
            value.bind(
                function( newval ) {
                    var section = $( '#authors-on-blog.authors-on-blog' );
                    section.removeClass('section-image');
                    if( newval !== '' ){
                        section.addClass('section-image');
                        section.css( 'background', 'url(' + newval + ')' );
                        section.css( 'background-size', 'cover' );
                        section.css( 'background-position', 'center center' );
                    } else {
                        section.removeAttr('style');
                    }
                }
            );
        }
    );

    // Colors > Accent Color
    wp.customize(
        'accent_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.main section:not(.hestia-blogs) a:not(.btn):not(.blog-item-title-link):not(.shop-item-title-link):not(.moretag):not(.button), .navbar.navbar-color-on-scroll:not(.navbar-transparent) li.active a, .single-product a.woocommerce-review-link, .woocommerce-checkout .woocommerce-checkout-payment a, .woocommerce-account .blog-post a:not(.woocommerce-Button)' ).css( 'color', newval );
                    $( '.btn.btn-primary:not(.has-color), .card .header-primary, input#searchsubmit, .woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce ul.products li.product .onsale, .woocommerce span.onsale, article .section-text a, body:not(.woocommerce-cart) .woocommerce .button:not(.btn-just-icon), .woocommerce-cart .checkout-button.button, .hestia-work .portfolio-item:nth-child(6n+1) .label, .pagination span.current, div.wpforms-container .wpforms-form button[type=submit].wpforms-submit, .woocommerce-product-search button, .single-product input[type="submit"]' ).css( 'background-color', newval );

                    var accentColorVariation2 = convertHex( newval, 20 );
                    var accentColorVariation3 = convertHex( newval, 42 );
                    var accentColorVariation4 = convertHex( newval, 12 );
                    var accentColorVariation5 = convertHex( newval, 14 );

                    // Pricing icon
                    $( '.home .hestia-pricing .card-pricing .content .hestia-pricing-icon-wrapper' ).css( 'color', newval) ;

                    // Price filter widget
                    $( 'div[id^=woocommerce_price_filter] .price_slider .ui-slider-range' ).css( 'background-color', newval) ;
                    $( '.price_slider .ui-slider-handle' ).css( 'border-color', newval) ;

                    // LINKS HOVER STYLE
                    var style = '<style class="hover-styles">', el;

                    // Product Page
                    style += '.single-product div.product .woocommerce-tabs ul.tabs.wc-tabs li.active a { color: ' + newval + '; border-color: ' + newval + '; }';

                    style += 'input[type="submit"], input[type="submit"]:hover { background-color: ' + newval + '; }';

                    style += '.card-blog a.moretag:hover, aside .widget a:not(.button):hover' +
                        '{ color: ' + newval + '!important; }';

                    style += '.footer-menu li > a:hover' +
                        '{ color: ' + newval + '; }';

                    style += 'a, .navbar .dropdown-menu li:hover > a, .navbar .dropdown-menu li:focus > a, .navbar .dropdown-menu li:active > a, .navbar .navbar-nav > li .dropdown-menu li:hover > a, body:not(.home) .navbar-default .navbar-nav > .active:not(.btn) > a, body:not(.home) .navbar-default .navbar-nav > .active:not(.btn) > a:hover, body:not(.home) .navbar-default .navbar-nav > .active:not(.btn) > a:focus, a:hover,  .card-blog a.moretag:hover,  .card-blog a.more-link:hover,  .widget a:hover, .has-text-color.has-accent-color, p.has-text-color a' +
                        '{ color: ' + newval + '; }';

	                style += '.svg-text-color' +
		                '{ fill: ' + newval + '; }';

                    style += '.navbar-not-transparent .navbar-nav li:not(.btn):hover > a' +
                        '{ color: ' + newval + '; }';

                    style += '.navbar .navbar-nav > li .dropdown-menu li:hover > a ' +
                        '{ color : ' + newval + '; }';

                    style += '.woocommerce div.product form.cart .reset_variations:after' +
	                    '{ background-color: ' + newval + '; }';

                    style += '.woocommerce div.product .woocommerce-tabs ul.tabs.wc-tabs li a:hover ' +
                        '{ border-color: ' + newval + '; }';

                    // BUTTONS BOX SHADOW
                    style += 'div.wpforms-container .wpforms-form button[type=submit].wpforms-submit, body:not(.woocommerce-page) input[type="submit"], body:not(.woocommerce-page) input[type="button"], .btn.btn-primary:not(.btn-left):not(.btn-right), .woocommerce-product-search button, aside a.button, .widget.woocommerce a.button, .woocommerce.widget button, .woocommerce div[id^=woocommerce_widget_cart].widget .buttons .button, .single-product button.button.single_add_to_cart_button, .single-product #respond input#submit, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce-checkout button.button[type="submit"]' +
                      '{ ' +
                      '-webkit-box-shadow: 0 2px 2px 0 ' + accentColorVariation5 + ',0 3px 1px -2px ' + accentColorVariation2 + ',0 1px 5px 0 ' + accentColorVariation4 + ';' +
                      'box-shadow: 0 2px 2px 0 ' + accentColorVariation5 + ',0 3px 1px -2px ' + accentColorVariation2 + ',0 1px 5px 0 ' + accentColorVariation4 + ';' +
                      '}';

                    // BUTTONS BOX SHADOW ON HOVER
                    style += 'input#searchsubmit:hover, .pagination span.current, .btn.btn-primary:not(.btn-left):not(.btn-right):hover, .btn.btn-primary:focus, .btn.btn-primary:active, .btn.btn-primary.active, .btn.btn-primary:active:focus, .btn.btn-primary:not(.btn-left):not(.btn-right):active:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .added_to_cart.wc-forward:hover, .woocommerce .single-product div.product form.cart .button:hover, .woocommerce #respond input#submit:hover, body:not(.woocommerce-cart) .woocommerce button.button:hover, .woocommerce input.button:hover, #add_payment_method .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-checkout .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce input.button:disabled:hover, .woocommerce input.button:disabled[disabled]:hover, .woocommerce-message a.button:hover, .woocommerce a.button.wc-backward:hover, .hestia-sidebar-open.btn.btn-rose:hover, .hestia-sidebar-close.btn.btn-rose:hover, div.wpforms-container .wpforms-form button[type=submit].wpforms-submit:hover, input[type="submit"]:hover, .woocommerce-product-search button:hover, aside a.button:hover' +
                        '{	' +
                        '-webkit-box-shadow: 0 14px 26px -12px' + accentColorVariation3 + ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' + accentColorVariation2 + '!important;' +
                        'box-shadow: 0 14px 26px -12px ' + accentColorVariation3 + ',0 4px 23px 0 rgba(0,0,0,0.12),0 8px 10px -5px ' + accentColorVariation2 + '!important;' +
                        '}	';

                    style += '.form-group.is-focused .form-control, div.wpforms-container .wpforms-form .form-group.is-focused .form-control, .nf-form-cont input:not([type=button]):focus, .nf-form-cont select:focus, .nf-form-cont textarea:focus, .woocommerce-cart .shop_table .actions .coupon .input-text:focus, .woocommerce-checkout #customer_details .input-text:focus, .woocommerce-checkout #customer_details select:focus, .woocommerce-checkout #order_review .input-text:focus, .woocommerce-checkout #order_review select:focus, .woocommerce-checkout .woocommerce-form .input-text:focus, .woocommerce-checkout .woocommerce-form select:focus, .woocommerce div.product form.cart .variations select:focus, .woocommerce .woocommerce-ordering select:focus {' +
                        'background-image: -webkit-gradient(linear,left top, left bottom,from(' + newval + '),to(' + newval + ')),-webkit-gradient(linear,left top, left bottom,from(#d2d2d2),to(#d2d2d2));' +
                        'background-image: -webkit-linear-gradient(' + newval + '),to(' + newval + '),-webkit-linear-gradient(#d2d2d2,#d2d2d2);' +
                        'background-image: linear-gradient(' + newval + '),to(' + newval + '),linear-gradient(#d2d2d2,#d2d2d2);' +
                        '}';

                    style += '</style>';
                    el     = $( '.hover-styles' ); // look for a matching style element that might already be there
                    if ( el.length ) {
                        el.replaceWith( style ); // style element already exists, so replace it
                    } else {
                        $( 'head' ).append( style ); // style element doesn't exist so add it
                    }
                }
            );
        }
    );

    // Colors > Gradient Color
    wp.customize(
        'hestia_header_gradient_color', function( value ) {
            value.bind(
                function( newval ) {

                    var gradientColor1 = convertHex( newval, 100 );
                    var gradientColor2 = generateGradientSecondColor( newval, 100 );

                    var style = '<style class="gradient-styles">';

                    style += '.header-filter-gradient { background: linear-gradient(45deg, ' + gradientColor1 + ' 0%, ' + gradientColor2 + ' 100%); }';

                    style += '</style>';

                    $( 'head' ).append( style );
                }
            );
        }
    );

    // Colors > Secondary Color
    wp.customize(
        'secondary_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.main .title, .main .title a, .card-title,.card-title a, .info-title, .info-title a, .footer-brand, .footer-brand a, .media .media-heading, .media .media-heading a, .hestia-info .info-title, .card-blog a.moretag, .card .author a, aside .widget h5, aside .widget a, .hestia-about:not(.section-image) h1, .hestia-about:not(.section-image) h2, .hestia-about:not(.section-image) h3, .hestia-about:not(.section-image) h4, .hestia-about:not(.section-image) h5' ).css( 'color', newval );
                    $( '.section-image .title, .section-image .card-plain .card-title, .card [class*="header-"] .card-title, .contactus .hestia-info .info-title, .hestia-work h4.card-title' ).css( 'color', '#fff' );
                }
            );
        }
    );

    // Colors > Body Color
    wp.customize(
        'body_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.description, .card-description, .footer-big, .hestia-features .hestia-info p, .text-gray, .card-description p, .hestia-about:not(.section-image) p, .hestia-about:not(.section-image) h6' ).css( 'color', newval );
                    $( '.contactus .description' ).css( 'color', '#fff' );
                }
            );
        }
    );

    // Colors > Header/Slider Text Color
    wp.customize(
        'header_text_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.page-header, .page-header .hestia-title, .page-header .sub-title' ).css( 'color', newval );
                }
            );
        }
    );

    // Header options > Top Bar > Background color
    wp.customize(
        'hestia_top_bar_background_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-top-bar' ).css( 'background-color', newval );
                }
            );
        }
    );

    // Header options > Top Bar > Text color
    wp.customize(
        'hestia_top_bar_text_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-top-bar' ).css( 'color', newval );

                    var headStyle = $('head style.top-bar-icons-color');
                    if ( headStyle.length > 0 ) {
                    	headStyle.html( '.hestia-top-bar div.widget.widget_shopping_cart:before, .hestia-top-bar .widget.widget_product_search form.form-group:before, .hestia-top-bar .widget.widget_search form.form-group:before{ background-color:' + newval + '}' );
                    } else {
	                    $('head').append('<style class="top-bar-icons-color">.hestia-top-bar div.widget.widget_shopping_cart:before, .hestia-top-bar .widget.widget_product_search form.form-group:before, .hestia-top-bar .widget.widget_search form.form-group:before{ background-color:' + newval + ';}</style>');
                    }
                }
            );
        }
    );

    // Header options > Top Bar > Link color
    wp.customize(
        'hestia_top_bar_link_color', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-top-bar a' ).css( 'color', newval );

	                var headStyle = $('head style.top-bar-link-icons-color');
	                if ( headStyle.length > 0 ) {
		                headStyle.html( '.hestia-top-bar ul li a[href*="mailto:"]:before, .hestia-top-bar ul li a[href*="tel:"]:before{ background-color:' + newval + '}' );
	                } else {
		                $('head').append('<style class="top-bar-link-icons-color">.hestia-top-bar ul li a[href*="mailto:"]:before, .hestia-top-bar ul li a[href*="tel:"]:before{ background-color:' + newval + ';}</style>');
	                }
                }
            );
        }
    );

    // Header options > Top Bar > Link color on hover
    wp.customize(
        'hestia_top_bar_link_color_hover', function( value ) {
            value.bind(
                function( newval ) {
                    $( '.hestia-top-bar a' ).hover(
                        function(){
                            $( this ).css( 'color', newval );
                        }, function(){
                            var initial = wp.customize._value.hestia_top_bar_link_color();
                            $( this ).css( 'color', initial );
                        }
                    );

	                var headStyle = $('head style.top-bar-link-icons-color-hover');
	                if ( headStyle.length > 0 ) {
		                headStyle.html( '.hestia-top-bar ul li:hover a[href*="mailto:"]:before, .hestia-top-bar ul li:hover a[href*="tel:"]:before{ background-color:' + newval + '}' );
	                } else {
		                $('head').append('<style class="top-bar-link-icons-color-hover">.hestia-top-bar ul li:hover a[href*="mailto:"]:before, .hestia-top-bar ul li:hover a[href*="tel:"]:before{ background-color:' + newval + ';}</style>');
	                }
                }
            );
        }
    );

    if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize && 'undefined' !== typeof wp.customize.selectiveRefresh ) {
        wp.customize.selectiveRefresh.bind(
            'partial-content-rendered', function( placement ) {
                initializeAllElements( $( placement.container ) );
            }
        );
    }

    wp.customize(
        'header_video', function( value ) {
            value.bind(
                function( newval ) {
                    var linkedControl = wp.customize._value.external_header_video();
                    trigger_slider_selective( newval, linkedControl );
                }
            );
        }
    );

    wp.customize(
        'external_header_video', function( value ) {
            value.bind(
                function( newval ) {
                    var linkedControl = wp.customize._value.header_video();
                    trigger_slider_selective( newval, linkedControl );
                }
            );
        }
    );

    /**
     * Documentation: http://vitaliykiyko.com/en/4077/fire-custom-js-code-wp-customizer-selective-refresh-made/
     */
    var reEnableParallaxFor = ['hestia_slider_content','hestia_slider_alignment'];
    reEnableParallaxFor.forEach(function (controlName) {
        wp.customize(
            controlName, function(value) {
                value.bind(
                    function() {
                        wp.customize.selectiveRefresh.bind('partial-content-rendered', function ( placement ) {
                            if( typeof wp.customize._value.hestia_slider_type === 'function' ) {
                                var sliderType = wp.customize._value.hestia_slider_type();
                                if (placement.partial.id === controlName && sliderType) {
                                    $.hestiaCustomizeLive.sliderHeightFix();
                                    if (sliderType === 'parallax') {
                                        $.hestiaParallax.parallaxMove();
                                    } else if ( typeof wp.customHeader !== 'undefined' ) {
                                        wp.customHeader.initialize();
                                    }
                                }
                            }
                        });
                    }
                );
            }
        );
    });



    function trigger_slider_selective( newval, linkedControl ){
        if ( newval || linkedControl ) {
            return;
        }
        var partial        = wp.customize.selectiveRefresh.partial( 'hestia_slider_content' );
        var refreshPromise = wp.customize.selectiveRefresh.requestPartial( partial );
        if ( ! partial._pendingRefreshPromise ) {
            _.each(
                partial.placements(), function( placement ) {
                    partial.preparePlacement( placement );
                }
            );

            refreshPromise.done(
                function( placements ) {
                    _.each(
                        placements, function( placement ) {
                            partial.renderContent( placement );
                        }
                    );
                }
            );

            refreshPromise.fail(
                function( data, placements ) {
                    partial.fallback( data, placements );
                }
            );

            // Allow new request when this one finishes.
            partial._pendingRefreshPromise = refreshPromise;
            refreshPromise.always(
                function() {
                    partial._pendingRefreshPromise = null;
                }
            );
        }
    }

    function convertHex(hex,opacity){
        hex   = hex.replace( '#','' );
        var r = parseInt( hex.substring( 0,2 ), 16 );
        var g = parseInt( hex.substring( 2,4 ), 16 );
        var b = parseInt( hex.substring( 4,6 ), 16 );

        var result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')';
        return result;
    }

    function generateGradientSecondColor(hex, opacity){
        hex   = hex.replace( '#','' );
        var r = parseInt( hex.substring( 0,2 ), 16 );
        var g = parseInt( hex.substring( 2,4 ), 16 );
        var b = parseInt( hex.substring( 4,6 ), 16 );

        var x = r + 66;
        var y = g + 28;
        var z = b - 21;

        if ( x >= 255 ) {
            x = 255; }
        if ( y >= 255 ) {
            y = 255; }
        if ( z <= 0 ) {
            z = 0; }

        var result = 'rgba(' + x + ',' + y + ',' + z + ',' + opacity / 100 + ')';
        return result;
    }

} )( jQuery );

/**
 * This function builds two arrays of settings for each value from arraySizes. Those two arrays are parameters for
 * hestiaSetCss function. Those parameters are:
 * 	data: an object with desktop, tablet and mobile value
 * 	settings: an object with class of the style tag and the selectors on witch the style will be applied
 *
 *
 * @param arraySizes
 * An object with multiple sizes. Foreach size you have to specify:
 * 	selectors on which to apply sizes
 * 	list of values on mobile, tablet and desktop
 *
 * @param settings
 * An object with the following components:
 * cssProperty: what css property is changed (ex: font-size, width etc. )
 * propertyUnit: unit (ex: px, em etc.)
 * styleClass: the class of the temporary style tag that is added while changing the control.
 *
 * @param to
 * Current value of the control
 */
function hestiaGetCss( arraySizes, settings, to ) {
    'use strict';
    var data, desktopVal, tabletVal, mobileVal,
        className = settings.styleClass, i = 1;

    var val = JSON.parse( to );
    if ( typeof( val ) === 'object' && val !== null ) {
        if ('desktop' in val) {
            desktopVal = val.desktop;
        }
        if ('tablet' in val) {
            tabletVal = val.tablet;
        }
        if ('mobile' in val) {
            mobileVal = val.mobile;
        }
    }

    for ( var key in arraySizes ) {
        // skip loop if the property is from prototype
        if ( ! arraySizes.hasOwnProperty( key )) {
            continue;
        }
        var obj = arraySizes[key];
        var limit = 0;
        var correlation = [1,1,1];
        if ( typeof( val ) === 'object' && val !== null ) {

            if( typeof obj.limit !== 'undefined'){
                limit = obj.limit;
            }

            if( typeof obj.correlation !== 'undefined'){
                correlation = obj.correlation;
            }

            data = {
                desktop: ( parseInt(parseFloat( desktopVal ) / correlation[0]) + obj.values[0]) > limit ? ( parseInt(parseFloat( desktopVal ) / correlation[0]) + obj.values[0] ) : limit,
                tablet: ( parseInt(parseFloat( tabletVal ) / correlation[1]) + obj.values[1] ) > limit ? ( parseInt(parseFloat( tabletVal ) / correlation[1]) + obj.values[1] ) : limit,
                mobile: ( parseInt(parseFloat( mobileVal ) / correlation[2]) + obj.values[2] ) > limit ? ( parseInt(parseFloat( mobileVal ) / correlation[2]) + obj.values[2] ) : limit
            };
        } else {
            if( typeof obj.limit !== 'undefined'){
                limit = obj.limit;
            }

            if( typeof obj.correlation !== 'undefined'){
                correlation = obj.correlation;
            }
            data =( parseInt( parseFloat( to ) / correlation[0] ) ) + obj.values[0] > limit ? ( parseInt( parseFloat( to ) / correlation[0] ) ) + obj.values[0] : limit;
        }
        settings.styleClass = className + '-' + i;
        settings.selectors  = obj.selectors;

        hestiaSetCss( settings, data );
        i++;
    }
}

/**
 * Add media query on settings from setStyle function.
 *
 * @param settings
 * An object with the following components:
 * 	styleClass class that will be on style tag
 * 	selectors specified selectors
 *
 * @param to
 * Current value of the control
 */
function hestiaSetCss( settings, to ){
    'use strict';
    var result     = '';
    var styleClass = jQuery( '.' + settings.styleClass );
    if ( to !== null && typeof to === 'object' ) {
        jQuery.each(
            to, function ( key, value ) {
                var style_to_add;
                if ( settings.selectors === '.container' ) {
                    style_to_add = settings.selectors + '{ ' + settings.cssProperty + ':' + value + settings.propertyUnit + '; max-width: 100%; }';
                } else {
                    style_to_add = settings.selectors + '{ ' + settings.cssProperty + ':' + value + settings.propertyUnit + '}';
                }
                switch ( key ) {
                    case 'desktop':
                        result += style_to_add;
                        break;
                    case 'tablet':
                        result += '@media (max-width: 767px){' + style_to_add + '}';
                        break;
                    case 'mobile':
                        result += '@media (max-width: 480px){' + style_to_add + '}';
                        break;
                }
            }
        );
        if ( styleClass.length > 0 ) {
            styleClass.text( result );
        } else {
            jQuery( 'head' ).append( '<style type="text/css" class="' + settings.styleClass + '">' + result + '</style>' );
        }
    } else {
        jQuery( settings.selectors ).css( settings.cssProperty, to + 'px' );
    }
}



