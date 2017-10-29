/* global Backbone, jQuery, _, BasisBuilderApp */
(function (window, Backbone, $, _, BasisBuilderApp) {
	'use strict';

	BasisBuilderApp.Collections.Sections = Backbone.Collection.extend({
		model: BasisBuilderApp.Models.Section,

		initialize: function (options) {
			this.builder = options.builder;
			this.$stage = $('#basis-stage-' + this.builder);
		},

		toggleStageClass: function() {
			var sections = $('.basis-section', this.$stage).length;

			if (sections > 0) {
				this.$stage.removeClass('basis-stage-closed');
			} else {
				this.$stage.addClass('basis-stage-closed');
			}
		}
	});

	BasisBuilderApp.sectionsProduct = new BasisBuilderApp.Collections.Sections({
		builder: 'product'
	});

	BasisBuilderApp.sectionsSlideshow = new BasisBuilderApp.Collections.Sections({
		builder: 'slideshow'
	});
})(window, Backbone, jQuery, _, BasisBuilderApp);