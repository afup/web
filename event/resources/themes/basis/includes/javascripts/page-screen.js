/* global jQuery, BasisPageScreenVars */
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
	var BasisPageScreen = function () {

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

			// Add help link
			addTemplateHelpLink();

			// Bind help link toggle
			cache.$helplink = $('#basis-template-help-link');
			if ('object' === typeof cache.$helplink) {
				cache.$helplink.on('click', function (evt) {
					evt.preventDefault();
					helpTrigger();
				});
			}

			// Toggle screen elements depending on the chosen page template
			if ('object' === typeof cache.$template) {
				var template;

				cache.$template.on('change', function () {
					template = $(this).val();
					templateToggle(template);
				});

				cache.$document.ready(function () {
					cache.$template.trigger('change');
				});
			}
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

			cache.$template = $('#page_template');

			cache.$help = $('#contextual-help-link');
			cache.$pagehelp = $('a', '#tab-link-basis-page-help-tab');

			cache.$featimgmeta = $('#postimagediv');
			cache.$discussionmeta = $('#commentstatusdiv');
			cache.$commentsmeta = $('#commentsdiv');
		}

		function addTemplateHelpLink() {
			var helplink;

			if ('undefined' !== typeof BasisPageScreenVars) {
				helplink = BasisPageScreenVars.helplink;
			} else {
				helplink = 'Help';
			}

			cache.$template.after('<a id="basis-template-help-link" href="#">'+helplink+'</a>');
		}

		function helpTrigger() {
			cache.$window.scrollTop(0);
			cache.$help.trigger('click');
			cache.$pagehelp.trigger('click');
		}

		function templateToggle(template) {
			switch (template) {
			case 'product.php' :
				metaboxContentToggle([cache.$featimgmeta, cache.$discussionmeta, cache.$commentsmeta], 'hide');
				break;
			case 'slideshow.php' :
				metaboxContentToggle([cache.$featimgmeta, cache.$discussionmeta, cache.$commentsmeta], 'hide');
				break;
			default :
				metaboxContentToggle([cache.$featimgmeta, cache.$discussionmeta, cache.$commentsmeta], 'show');
				break;
			}
		}

		function metaboxContentToggle(elements, state) {
			$('.basis-metabox-message').remove();

			var unavailable;

			if ('undefined' !== typeof BasisPageScreenVars) {
				unavailable = BasisPageScreenVars.unavailable;
			} else {
				unavailable = 'This feature is unavailable for this page while using the current page template.';
			}

			unavailable = '<div class="basis-metabox-message inside"><p class="hide-if-no-js">'+unavailable+'</p></div>';

			for (var i = 0; i < elements.length; i++) {
				if ('show' === state) {
					elements[i].find('.inside').show();
				} else {
					elements[i].find('.inside').before(unavailable).hide();
				}
			}
		}

		// Initiate the actions.
		init();
	};

	// Instantiate the "class".
	window.BasisPageScreen = new BasisPageScreen();
})(window, jQuery);