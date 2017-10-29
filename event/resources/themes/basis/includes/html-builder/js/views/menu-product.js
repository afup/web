/* global Backbone, jQuery, _, BasisBuilderApp, setUserSetting, deleteUserSetting, basisHTMLBuilderData */
(function (window, Backbone, $, _, BasisBuilderApp) {
	'use strict';

	BasisBuilderApp.Views.MenuProduct = Backbone.View.extend({
		el: '#basis-menu-product',

		$stage: $('#basis-stage-product'),

		$document: $(window.document),

		$scrollHandle: $('html, body'),

		$pane: $('.basis-menu-product-pane'),

		initialize: function () {
			// Back compat for WP 3.5
			if (this.listenTo) {
				this.listenTo(BasisBuilderApp.sectionsProduct, 'add', this.addOne);
			} else {
				BasisBuilderApp.sectionsProduct.on('add', this.addOne, this);
			}
		},

		events: {
			'click .basis-menu-product-list-item-link': 'addSection',
			'click .basis-menu-product-tab-link': 'menuToggle'
		},

		addSection: function (evt) {
			evt.preventDefault();

			var $evt = $(evt),
				$target = $($evt.get(0).currentTarget),
				sectionType = $target.attr('data-section').replace(/\W/g, ''); // Get and sanitize section

			// Add a new model to the collection with the specified section type
			BasisBuilderApp.sectionsProduct.create({
				sectionType: sectionType,
				iterator: BasisBuilderApp.incrementIterator(),
				builder: 'product'
			});
		},

		addOne: function (section) {
			var sectionType = section.get('sectionType'),
				iterator = section.get('iterator');

			// Create view
			var view = new BasisBuilderApp.Views.Section({
				model: section,
				collection: 'product'
			});

			// Append view
			var html = view.render().el;
			this.$stage.append(html);

			// Scroll to the new section
			this.$scrollHandle.animate({
				scrollTop: parseInt($('#' + view.id).offset().top, 10) - 32 - 9 // Offset + admin bar height + margin
			}, 800, 'easeOutQuad');

			// Register the section with the sortable
			BasisBuilderApp.addSectionsOrder('basis-section-' + iterator, 'product');

			if ('profile' !== sectionType) {
				var editorID = 'basiseditor' + sectionType + BasisBuilderApp.getIterator();
				BasisBuilderApp.initEditor(editorID, sectionType, '');
			} else {
				_.each(['left', 'middle', 'right'], function (element) {
					editorID = 'basiseditor' + sectionType + BasisBuilderApp.getIterator() + element;
					BasisBuilderApp.initEditor(editorID, sectionType, element);
				});
			}

			BasisBuilderApp.sectionsProduct.toggleStageClass();
		},

		menuToggle: function(evt) {
			evt.preventDefault();
			var id = basisHTMLBuilderData.pageID,
				key = 'basismt' + parseInt(id, 10);

			// Open it down
			if (this.$pane.is(':hidden')) {
				this.$pane.slideDown({
					duration: BasisBuilderApp.openSpeed,
					easing: 'easeInOutQuad',
					complete: function() {
						deleteUserSetting( key );
						this.$el.addClass('basis-menu-product-opened').removeClass('basis-menu-product-closed');
					}.bind(this)
				});
			// Close it up
			} else {
				this.$pane.slideUp({
					duration: BasisBuilderApp.closeSpeed,
					easing: 'easeInOutQuad',
					complete: function() {
						setUserSetting( key, 'c' );
						this.$el.addClass('basis-menu-product-closed').removeClass('basis-menu-product-opened');
					}.bind(this)
				});
			}
		},

		validateViewName: function (viewName) {
			return viewName in BasisBuilderApp.Views;
		}
	});
})(window, Backbone, jQuery, _, BasisBuilderApp);