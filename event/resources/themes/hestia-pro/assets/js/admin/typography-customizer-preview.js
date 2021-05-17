/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @package hestia
 * @since 1.1.38
 */

/* global wp*/
/* global hestiaGetCss */

/**
 * -------------------
 * Posts & Pages
 * -------------------
 */

/**
 * Live refresh for font size for:
 * pages/posts titles
 */
wp.customize(
    'hestia_header_titles_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-header-titles-fs'
                };

                var arraySizes = {
                    size3: { selectors: '.page-header.header-small .hestia-title, .page-header.header-small .title, h1.hestia-title.title-in-content', values: [42,36,36] }
                };
                hestiaGetCss( arraySizes, settings, to );
            }
        );
    }
);

/**
 * Live refresh for font size for:
 * headings ( h1 - h6 ) on pages and single post pages
 */
wp.customize(
	'hestia_post_page_headings_fs', function (value) {
		'use strict';
		value.bind(
			function( to ) {
				var settings = {
					cssProperty: 'font-size',
					propertyUnit: 'px',
					styleClass: 'hestia-post-page-headings-fs'
				};

				var arraySizes = {
					size1: { selectors: '.single-post-wrap h1:not(.title-in-content), .page-content-wrap h1:not(.title-in-content), .page-template-template-fullwidth article h1:not(.title-in-content)', values: [42,36,36] },
					size2: { selectors: '.single-post-wrap h2, .page-content-wrap h2, .page-template-template-fullwidth article h2', values: [37,32,32] },
					size3: { selectors: '.single-post-wrap h3, .page-content-wrap h3, .page-template-template-fullwidth article h3', values: [32,28,28] },
					size4: { selectors: '.single-post-wrap h4, .page-content-wrap h4, .page-template-template-fullwidth article h4', values: [27,24,24] },
					size5: { selectors: '.single-post-wrap h5, .page-content-wrap h5, .page-template-template-fullwidth article h5', values: [23,21,21] },
					size6: { selectors: '.single-post-wrap h6, .page-content-wrap h6, .page-template-template-fullwidth article h6', values: [18,18,18] }
				};

                hestiaGetCss( arraySizes, settings, to );
			}
		);
	}
);

/**
 * Live refresh for font size for:
 * content ( p ) on pages
 * single post pages
 */
wp.customize(
	'hestia_post_page_content_fs', function (value) {
		'use strict';
		value.bind(
			function( to ) {
				var settings = {
					cssProperty: 'font-size',
					propertyUnit: 'px',
					styleClass: 'hestia-post-page-content-fs'
				};

				var arraySizes = {
					size1: { selectors: '.single-post-wrap, .page-content-wrap, .single-post-wrap ul, .page-content-wrap ul, .single-post-wrap ol, .page-content-wrap ol, .single-post-wrap dl, .page-content-wrap dl, .single-post-wrap table, .page-content-wrap table, .page-template-template-fullwidth article, .page-template-template-fullwidth article ol, .page-template-template-fullwidth article ul, .page-template-template-fullwidth article dl, .page-template-template-fullwidth article table', values: [18,18,18] },
				};

                hestiaGetCss( arraySizes, settings, to );
			}
		);
	}
);


/**
 * -------------------
 * Frontpage Sections
 * -------------------
 */

/**
 * Big Title Section / Header Slider
 * Controls all elements from the big title section.
 */
wp.customize(
    'hestia_big_title_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-big-title-fs'
                };

                var arraySizes = {
                    size1: { selectors: '#carousel-hestia-generic .hestia-title', values: [67,36,36], correlation: [1,4,4] },
					size2: { selectors: '#carousel-hestia-generic span.sub-title', values: [18,18,18], correlation: [8,4,4] },
					size3: { selectors: '#carousel-hestia-generic .btn', values: [14,14,14], correlation: [12,6,6] },
                };

                hestiaGetCss( arraySizes, settings, to );
            }
        );
    }
);

/**
 * Live refresh for font size for:
 * all frontpage sections titles and small headings ( Feature box title, Shop box title, Team box title, Testimonial box title, Blog box title )
 */
wp.customize(
    'hestia_section_primary_headings_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-section-primary-headings-fs'
                };

                var arraySizes = {
                    size1: { selectors: 'section.hestia-features .hestia-title, section.hestia-shop .hestia-title, section.hestia-work .hestia-title, section.hestia-team .hestia-title, section.hestia-pricing .hestia-title, section.hestia-ribbon .hestia-title, section.hestia-testimonials .hestia-title, section.hestia-subscribe h2.title, section.hestia-blogs .hestia-title, .section.related-posts .hestia-title, section.hestia-contact .hestia-title', values: [37,24,24], limit: 18 },
                    size2: { selectors: 'section.hestia-features .hestia-info h4.info-title, section.hestia-shop h4.card-title, section.hestia-team h4.card-title, section.hestia-testimonials h4.card-title, section.hestia-blogs h4.card-title, .section.related-posts h4.card-title, section.hestia-contact h4.card-title, section.hestia-contact .hestia-description h6', values: [18,18,18], correlation: [3,3,3], limit: 14},
                    size3: { selectors: 'section.hestia-work h4.card-title, section.hestia-contact .hestia-description h5', values: [23,23,23], correlation: [3,3,3] },
                    size4: { selectors: 'section.hestia-contact .hestia-description h1', values: [42,42,42], correlation: [3,3,3] },
                    size5: { selectors: 'section.hestia-contact .hestia-description h2', values: [37,24,24], correlation: [3,3,3] },
                    size6: { selectors: 'section.hestia-contact .hestia-description h3', values: [32,32,32], correlation: [3,3,3] },
                    size7: { selectors: 'section.hestia-contact .hestia-description h4', values: [27,27,27], correlation: [3,3,3] },
                };

                hestiaGetCss( arraySizes, settings, to );
            }
        );
    }
);


/**
 * Live refresh for font size for:
 * all frontpage sections subtitles
 * WooCommerce pages subtitles ( Single product page price, Cart and Checkout pages subtitles )
 */
wp.customize(
    'hestia_section_secondary_headings_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-section-secondary-headings-fs'
                };

                var arraySizes = {
                    size1: { selectors: 'section.hestia-features h5.description, section.hestia-shop h5.description, section.hestia-work h5.description, section.hestia-team h5.description, section.hestia-testimonials h5.description, section.hestia-subscribe h5.subscribe-description, section.hestia-blogs h5.description, section.hestia-contact h5.description', values: [18,18,18], limit: 12, correlation: [3,3,3] },
                };

                hestiaGetCss( arraySizes, settings, to );
            }
        );
    }
);

/**
 * Live refresh for font size for:
 * all frontpage sections box content
 */
wp.customize(
    'hestia_section_content_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-section-content-fs'
                };

                var arraySizes = {
                    size2: { selectors: 'section.hestia-features .hestia-info p, section.hestia-shop .card-description p, section.hestia-team p.card-description, section.hestia-pricing p.text-gray, section.hestia-testimonials p.card-description, section.hestia-blogs p.card-description, .section.related-posts p.card-description, .hestia-contact p', values: [14,14,14], limit: 12, correlation: [3,3,3] },
                    size1: { selectors: 'section.hestia-shop h6.category, section.hestia-work .label-primary, section.hestia-team h6.category, section.hestia-pricing .card-pricing h6.category, section.hestia-testimonials h6.category, section.hestia-blogs h6.category, .section.related-posts h6.category', values: [12,12,12], limit: 12, correlation: [3,3,3] },
                };

                hestiaGetCss( arraySizes, settings, to );
            }
        );
    }
);

/**
 * -------------------
 * Generic options
 * -------------------
 */

/**
 * Live refresh for font size for:
 * Primary menu
 * Footer menu
 */
wp.customize(
    'hestia_menu_fs', function (value) {
        'use strict';
        value.bind(
            function( to ) {
                var settings = {
                    cssProperty: 'font-size',
                    propertyUnit: 'px',
                    styleClass: 'hestia-menu-fs'
                };
                var arraySizes = {
                    size1: { selectors: '.navbar #main-navigation a, .footer .footer-menu li a', values: [12,12,12], limit: 10 }
                };
                hestiaGetCss( arraySizes, settings, to );

                settings.cssProperty = 'width';
                arraySizes.size1.selectors = '.footer-big .footer-menu li a[href*="mailto:"]:before, .footer-big .footer-menu li a[href*="tel:"]:before';
	            hestiaGetCss( arraySizes, settings, to );

	            settings.cssProperty = 'height';
	            hestiaGetCss( arraySizes, settings, to );

            }
        );
    }
);
