/* global Backbone, jQuery, _, BasisBuilderApp */
(function (window, Backbone, $, _, BasisBuilderApp) {
	'use strict';

	BasisBuilderApp.Models.Section = Backbone.Model.extend({
		defaults: {
			sectionType: '',
			viewName: '',
			iterator: 0,
			builder: ''
		},

		initialize: function () {
			// Capitalize the name
			var viewName = this.get('sectionType').charAt(0).toUpperCase() + this.get('sectionType').slice(1);
			this.set('viewName', viewName);
		}
	});

	// Set up this model as a "no URL model" where data is not synced with the server
	BasisBuilderApp.Models.Section.prototype.sync = function () {
		return null;
	};

	BasisBuilderApp.Models.Section.prototype.fetch = function () {
		return null;
	};

	BasisBuilderApp.Models.Section.prototype.save = function () {
		return null;
	};
})(window, Backbone, jQuery, _, BasisBuilderApp);