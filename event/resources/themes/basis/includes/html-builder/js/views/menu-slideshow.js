/* global Backbone, jQuery, _, BasisBuilderApp */
(function (window, Backbone, $, _, BasisBuilderApp) {
	'use strict';

	BasisBuilderApp.Views.MenuSlideshow = Backbone.View.extend({
		el: '#basis-menu-slideshow',

		$stage: $('#basis-stage-slideshow'),

		$document: $(window.document),

		$scrollHandle: $('html, body'),

		initialize: function () {
			// WP 3.5 backcompat
			if (this.listenTo) {
				this.listenTo(BasisBuilderApp.sectionsSlideshow, 'add', this.addOne);
			} else {
				BasisBuilderApp.sectionsSlideshow.on('add', this.addOne, this);
			}
		},

		events: {
			'click .basis-menu-slideshow-add': 'addSection'
		},

		addSection: function (evt) {
			evt.preventDefault();

			// Add a new model to the collection with the specified section type
			BasisBuilderApp.sectionsSlideshow.create({
				sectionType: 'slide',
				iterator: BasisBuilderApp.incrementIterator(),
				builder: 'slideshow'
			});
		},

		addOne: function (section) {
			var sectionType = section.get('sectionType'),
				iterator = section.get('iterator');

			// Create view
			var view = new BasisBuilderApp.Views.Section({
				model: section
			});

			// Append view
			var html = view.render().el;
			this.$stage.append(html);

			// Scroll to the new section
			this.$scrollHandle.animate({
				scrollTop: parseInt($('#' + view.id).offset().top, 10) - 32 - 9 // Offset + admin bar height + margin
			}, 800, 'easeOutQuad');

			// Register the section with the sortable
			BasisBuilderApp.addSectionsOrder('basis-section-' + iterator, 'slideshow');

			var editorID = 'basiseditor' + sectionType + BasisBuilderApp.getIterator();
			BasisBuilderApp.initEditor(editorID, sectionType, '');
			BasisBuilderApp.sectionsSlideshow.toggleStageClass();
		}
	});
})(window, Backbone, jQuery, _, BasisBuilderApp);