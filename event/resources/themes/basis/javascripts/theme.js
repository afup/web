/**
 * Add Immediately-Invoked Function Expression that initiates all of the general purpose theme JS.
 *
 * @since  1.0.
 *
 * @param  object    window    The window object.
 * @param  object    $         The jQuery object.
 * @return void
 */
/* global jQuery, basisResponsiveNavOptions:true, responsiveNav, BasisFitvidsCustomSelectors */

(function (window, $) {
	'use strict';

	// Cache document for fast access.
	var document = window.document;

	/**
	 * The faux "class" constructor.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	var Basis = function () {

		/**
		 * Holds reusable elements.
		 *
		 * @since 1.0.
		 *
		 * @type  {{}}
		 */
		var cache = {};

		/**
		 * Initiate all actions for this class.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function init() {
			// Cache the reusable elements
			cacheElements();

			// Bind events
			bindEvents();
		}

		/**
		 * Caches elements that are used in this scope.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function cacheElements() {
			cache.$window = $(window);
			cache.$document = $(document);

			cache.$basisheadernav = $('#basis-header-nav');

			cache.isSingular = ($('body.single, body.page').not('body.page-template-slideshow-php, body.page-template-product-php').length > 0);
			if (cache.isSingular) {
				cache.$fullimg = $('.size-basis-featured-page');
			}

			cache.isSlideshowPage = ($('body.page-template-slideshow-php').length > 0);
			if (cache.isSlideshowPage) {
				cache.$others = [$('#header')];
				cache.$slideContainer = $('.cycle-slideshow');
				cache.$slideContent = $('.slide-content');
				cache.$footer = $('#footer');
			}
		}

		/**
		 * Setup event binding.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function bindEvents() {
			// Enable the mobile menu
			cache.$document.on('ready', setupMenu);

			// 'Full Width' image alignment
			if (cache.isSingular) {
				cache.$document.on('ready', setFullWidth);
				cache.$document.on('post-load', setFullWidth);
			}

			// Run FitVids
			cache.$document.on('ready', setupFitVids);
			cache.$document.on('post-load', setupFitVids);

			// Slideshow
			if (cache.isSlideshowPage) {
				// Test for iPod and Safari
				cache.isiPod = isiPod();
				cache.isSafari = isSafari();

				// Reset the heights on resize with a nicely, responsible debounce
				var lazyResize = debounce(resetHeights, 200, false);
				cache.$window.resize(lazyResize);

				// Resize and setup keyboard nav when the page loads
				cache.$window.on('load', function() {
					resetHeights();
					keyboardNav();
				});

				var duration = 1200,
					easing = 'easeOutCirc';

				// Reveal the slideshow
				cache.$slideContainer.on('cycle-post-initialize', function() {
					$(this).delay(800).animate({ opacity: 1 }, duration, easing, function() {
						cache.$footer.css('visibility', 'visible');
					});
				});

				// Pause slideshow on manual nav event
				cache.$slideContainer.on('cycle-next cycle-prev cycle-pager-activated', function() {
					cache.$slideContainer.cycle('pause');
				});
			}
		}

		/**
		 * Activate the mobile menu
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function setupMenu() {
			// Make sure the menu markup exists
			if ( cache.$basisheadernav.length < 1 ) {
				return;
			}

			// Be safe and check that the resource is loaded
			if ('function' !== typeof responsiveNav) {
				return;
			}

			// Verify that we have a legit object
			if ('object' !== typeof basisResponsiveNavOptions) {
				/**
				 * Looks like the expected object does not exist, which likely means someone did something to
				 * "unlocalize" the script. To try to correct for this situation, define the defaults so the menu will
				 * still work.
				 */
				basisResponsiveNavOptions = {
					animate     : true,
					transition  : 400,
					label       : 'Show Menu',
					insert      : 'before',
					customToggle: 'mobile-toggle',
					openPos     : 'relative',
					jsClass     : 'js',

					// This is not a part of Responsive Nav, but is needed for changing the menu label
					closedLabel : 'Hide Menu'
				};
			}

			// Guarantee the transition value is a an int
			basisResponsiveNavOptions.transition = parseInt(basisResponsiveNavOptions.transition, 10);

			// Cache the object for use in other methods
			cache.responsiveNavOptions = basisResponsiveNavOptions;

			// Add callback function to change the label when the menu is opened
			cache.responsiveNavOptions.open = function () {
				toggleLabel(cache.responsiveNavOptions.closedLabel);
			};

			// Add callback function to change the label when the menu is closed
			cache.responsiveNavOptions.close = function () {
				toggleLabel(cache.responsiveNavOptions.label);
				$('html, body').scrollTop(0);
			};

			// Initiate the Responsive Nav menu
			cache.nav = responsiveNav('#basis-header-nav', cache.responsiveNavOptions);
		}

		/**
		 * Change the label in the menu.
		 *
		 * @since  1.0
		 *
		 * @param  label    Value to change the menu to.
		 * @return void
		 */
		function toggleLabel(label) {
			// If the element is not cached, cache it
			if ('object' !== cache.$navMenuLabel) {
				cache.$navMenuLabel = $('span', '#' + cache.responsiveNavOptions.customToggle);
			}

			// Set the label
			cache.$navMenuLabel.text(label);
		}

		/**
		 * Adjust the height of the slideshow div so that the footer is always below the fold.
		 * Also center the content within each slide.
		 *
		 * @since 1.0.
		 *
		 * @return void
		 */
		function resetHeights() {
			// Ignore header on smaller screens
			if (cache.$window.width() >= 800) {
				setDivHeight(cache.$slideContainer, cache.$others);
			} else {
				// Otherwise, reset to natural height
				setDivHeight(cache.$slideContainer, []);
			}
			var containerHeight = cache.$slideContainer.height();
			setContentPos({elements: cache.$slideContent, containerHeight: containerHeight});
		}

		/**
		 * Calculate and set the new height of an element
		 *
		 * @param string element   The div to set the height on
		 * @param array  others    An array of other elements to use to calculate the new height
		 *
		 * @return void
		 */
		function setDivHeight(element, others) {
			// iOS devices return an incorrect value with height() so availHeight is used instead.
			var windowHeight = (true === cache.isiPod && true === cache.isSafari) ? window.screen.availHeight : cache.$window.height();
			var offsetHeight = 0;

			// Add up the heights of other elements
			for (var i = 0; i < others.length; i++) {
				offsetHeight += $(others[i]).outerHeight();
			}

			var newHeight = windowHeight - offsetHeight;
			// Only set the height if the new height is greater than the original
			if (newHeight > 0) {
				$(element).outerHeight(newHeight);
			}
		}

		/**
		 * Vertically position one or more elements within a container
		 *
		 * @since 1.0.
		 *
		 * @param args
		 */
		function setContentPos(args) {
			var opts = $.extend({
				elements: '',
				containerHeight: 0,
				reset: false
			}, args);

			var content = $(opts.elements);
			content.each(function() {
				if (true === opts.reset) {
					$(this).css({ marginTop: '' });
				} else {
					var contentHeight = $(this).outerHeight();
					var offset = ((opts.containerHeight - contentHeight) / 3);
					if (offset > 0) {
						$(this).css({ marginTop: offset });
					}
				}
			});
		}

		/**
		 * Add alignment class to 'Full Width' images in the single view
		 *
		 * @return void
		 */
		function setFullWidth() {
			if (cache.$fullimg.length >= 1) {
				cache.$fullimg.each(function() {
					var parent = $(this).parents('p, div.wp-caption');
					if (parent.length >= 1) {
						parent.addClass('size-basis-featured-page');
					}
				});
			}
		}

		/**
		 * Run FitVids.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function setupFitVids() {
			// FitVids is only loaded on the pages and single post pages. Check for it before doing anything.
			if (!$.fn.fitVids) {
				return;
			}

			// Get the selectors
			var selectors;
			if ('object' === typeof BasisFitvidsCustomSelectors) {
				selectors = BasisFitvidsCustomSelectors.customSelector;
			}

			$('.hentry').fitVids({ customSelector: selectors });

			// Fix padding issue with Blip.tv issues; note that this *must* happen after Fitvids runs
			// The selector finds the Blip.tv iFrame then grabs the .fluid-width-video-wrapper div sibling
			$('.fluid-width-video-wrapper:nth-child(2)', '.video-container')
				.css({ 'paddingTop': 0 });
		}

		/**
		 * Bind keys for slideshow navigation
		 *
		 * @since 1.0.
		 *
		 * @return void
		 */
		function keyboardNav() {
			if ('undefined' === typeof cache.$left) {
				cache.$left = $('.cycle-prev');
			}

			if ('undefined' === typeof cache.$right) {
				cache.$right = $('.cycle-next');
			}

			var key;

			if (cache.$left.length >= 1 || cache.$right.length >= 1) {
				cache.$window.on('keyup', function(evt) {
					key = evt.which || evt.keyCode;

					switch(key) {
					case 37 :
						cache.$left.trigger('click');
						break;
					case 39 :
						cache.$right.trigger('click');
						break;
					}
				});
			}
		}

		/**
		 * Throttles an action.
		 *
		 * Taken from Underscore.js.
		 *
		 * @link    http://underscorejs.org/#debounce
		 *
		 * @param   func
		 * @param   wait
		 * @param   immediate
		 * @returns {Function}
		 */
		function debounce (func, wait, immediate) {
			var timeout, args, context, timestamp, result;
			return function() {
				context = this;
				args = arguments;
				timestamp = new Date();
				var later = function() {
					var last = (new Date()) - timestamp;
					if (last < wait) {
						timeout = setTimeout(later, wait - last);
					} else {
						timeout = null;
						if (!immediate) {
							result = func.apply(context, args);
						}
					}
				};
				var callNow = immediate && !timeout;
				if (!timeout) {
					timeout = setTimeout(later, wait);
				}
				if (callNow) {
					result = func.apply(context, args);
				}
				return result;
			};
		}

		/**
		 * Check if device is an iPhone or iPod
		 *
		 * @since 1.0.
		 *
		 * @returns {boolean}
		 */
		function isiPod() {
			return (/(iPhone|iPod)/g).test(navigator.userAgent);
		}

		/**
		 * Check if browser is Safari
		 *
		 * @since 1.0.
		 *
		 * @returns {boolean}
		 */
		function isSafari() {
			return (-1 !== navigator.userAgent.indexOf('Safari') && -1 === navigator.userAgent.indexOf('Chrome'));
		}

		// Initiate the actions.
		init();
	};

	// Instantiate the "class".
	window.Basis = new Basis();
})(window, jQuery);
