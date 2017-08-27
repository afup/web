/* global BasisBuilderApp, jQuery, _, postboxes, pagenow, tinyMCE, basisHTMLBuilderData, switchEditors */
(function (BasisBuilderApp, $, _) {
	'use strict';

	/**
	 * The faux "class" constructor.
	 *
	 * @since  1.0.
	 *
	 * @return void
	 */
	var BasisAdmin = function () {

		var cache = {};

		/**
		 * Initiate all actions for this class.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function init() {
			// Init the cache
			cacheElements();

			// Run any initial code
			initApp();

			// Setup events
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
			cache.$sortInput = $('#basis-section-order-product');
			cache.$pageParentDiv = $('#pageparentdiv');
			cache.$pageTemplate = $('#page_template');
			cache.$builderProduct = $('#basis_builder_product');
			cache.$productHide = $('#basis_builder_product-hide');
			cache.$builderSlideshow = $('#basis_builder_slideshow');
			cache.$slideshowHide = $('#basis_builder_slideshow-hide');
			cache.$mainEditor = $('#postdivrich');
			cache.$postImageDiv = $('#postimagediv');
			cache.$commentStatusDiv = $('#commentstatusdiv');
			cache.$commentsDiv = $('#commentsdiv');
			cache.$revisionsInfo = $('.misc-pub-revisions');
		}

		/**
		 * Setup event binding.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function bindEvents() {
			// Setup the event for toggling the HTML Builder when the page template input changes
			cache.$pageTemplate.on('change', toggleBuilder);
		}

		/**
		 * Start the Backbone.js app
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function initApp() {
			// Create the checkbox to hide the site header
			hideHeaderOption();

			// Kick off the Backbone apps
			new BasisBuilderApp.Views.MenuProduct();
			new BasisBuilderApp.Views.MenuSlideshow();

			// Init the sortables
			initSortables();

			// Initialize the prerendered views
			initViews();
		}

		/**
		 * Initiate the sortable sections.
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function initSortables() {
			$('.basis-stage').sortable({
				handle: '.basis-section-header',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSizeType: true,
				distance: 2,
				tolerance: 'pointer',
				start: function (event, ui) {
					// Set the height of the placeholder to that of the sorted item
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.basis-stage');

					$('.sortable-placeholder', $stage).height($item.height());

					/**
					 * When moving the section, the TinyMCE instance must be removed. If it is not removed, it will be
					 * unresponsive once placed. It is reinstated when the section is placed
					 */
					$('.wp-editor-area', $item).each(function() {
						var $this = $(this),
							id = $this.attr('id');

						tinyMCE.execCommand( BasisBuilderApp.removeCommand(), false, id );
						delete tinyMCE.editors.id;
					});
				},
				stop: function (event, ui) {
					var $item = $(ui.item.get(0)),
						$stage = $item.parents('.basis-stage'),
						data = $(this).sortable('toArray');

					BasisBuilderApp.setSectionOrder(data, $stage.attr('id').replace('basis-stage-', ''));

					/**
					 * Reinstate the TinyMCE editor now that is it placed. This is a critical step in order to make sure
					 * that the TinyMCE editor is operable after a sort.
					 */
					$('.wp-editor-area', $item).each(function() {
						var $this = $(this),
							id = $this.attr('id'),
							$wrap = $this.parents('.wp-editor-wrap'),
							el = tinyMCE.DOM.get(id);

						// If the text area (i.e., non-tinyMCE) is showing, do not init the editor.
						if ($wrap.hasClass('tmce-active')) {
							// Restore the content, with pee
							el.value = switchEditors.wpautop(el.value);
							console.log(el.value);

							// Activate tinyMCE
							tinyMCE.execCommand( BasisBuilderApp.addCommand(), false, id );
						}
					});
				}
			});
		}

		/**
		 * Turn DOM elements into proper views if they exist
		 *
		 * @since  1.0.
		 *
		 * @return void
		 */
		function initViews() {
			$('.basis-section').each(function () {
				var $section = $(this),
					id = $section.attr('id'),
					iterator = $section.attr('data-iterator'),
					sectionType = $section.attr('data-section-type'),
					builder = $section.parents('.basis-stage').attr('id').replace('basis-stage-', '');

				// Build the model
				var model = new BasisBuilderApp.Models.Section({
					sectionType: sectionType,
					iterator: iterator,
					builder: builder
				});

				// Build the view
				new BasisBuilderApp.Views.Section({
					model: model,
					el: $('#' + id),
					serverRendered: true
				});
			});
		}

		/**
		 * Hide and show the HTML builder depending on the page template.
		 *
		 * @since  1.0.
		 *
		 * @param  evt
		 * @return void
		 */
		function toggleBuilder(evt) {
			var val = $(evt.target).val();

			if ('product.php' === val) {
				// Hide/show boxes
				cache.$builderProduct.show();
				cache.$builderSlideshow.hide();

				// Handle the editor
				cache.$mainEditor.hide();

				// Handle the metaboxes
				cache.$postImageDiv.hide();
				cache.$commentStatusDiv.hide();
				cache.$commentsDiv.hide();

				// Check/uncheck screen options checkboxes
				cache.$productHide.prop('checked', true).parent().show();
				cache.$slideshowHide.prop('checked', false).parent().hide();

				// Add Hide Header option
				cache.$basishideheader.show();

				// Hide the revisions
				cache.$revisionsInfo.hide();
			} else if ('slideshow.php' === val) {
				// Hide/show boxes
				cache.$builderSlideshow.show();
				cache.$builderProduct.hide();

				// Handle the editor
				cache.$mainEditor.hide();

				// Handle the metaboxes
				cache.$postImageDiv.hide();
				cache.$commentStatusDiv.hide();
				cache.$commentsDiv.hide();

				// Check/uncheck screen options checkboxes
				cache.$slideshowHide.prop('checked', true).parent().show();
				cache.$productHide.prop('checked', false).parent().hide();

				// Add Hide Header option
				cache.$basishideheader.show();

				// Hide the revisions
				cache.$revisionsInfo.hide();
			} else {
				// Hide/show boxes
				cache.$builderProduct.hide();
				cache.$builderSlideshow.hide();

				// Handle the editor
				cache.$mainEditor.show();

				// Handle the metaboxes
				cache.$postImageDiv.show();
				cache.$commentStatusDiv.show();
				cache.$commentsDiv.show();

				// Check/uncheck screen options checkboxes
				cache.$productHide.prop('checked', false).parent().hide();
				cache.$slideshowHide.prop('checked', false).parent().hide();

				// Remove Hide Header option
				cache.$basishideheader.hide();

				// Show the revisions
				cache.$revisionsInfo.show();
			}

			/*jshint -W106 */
			postboxes.save_state(pagenow);
			/*jshint +W106 */
		}

		/**
		 * Create a checkbox for hiding the site header on certain page templates.
		 *
		 * @since 1.0.
		 */
		function hideHeaderOption() {
			var html = _.template('<p><strong>Minimal Mode</strong></p><label id="basis-hide-header-option" for="basis-hide-header" style="display:none;"><input type="checkbox" id="basis-hide-header" name="basis-hide-header" value="1" <%= hideHeaderChecked %> /> <%- hideHeaderLabel %></label>', basisHTMLBuilderData),
				$loadAfter = $('#menu_order', cache.$pageParentDiv).parent(); // Grabs the "Order" input bounding "p" element

			$loadAfter.after(html);
			cache.$basishideheader = $('#basis-hide-header-option');
		}

		// Initiate the actions.
		init();
	};

	// Instantiate the "class".
	window.BasisAdmin = new BasisAdmin();
})(BasisBuilderApp, jQuery, _);
