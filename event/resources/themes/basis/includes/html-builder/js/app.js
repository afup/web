/* global jQuery, _, wpActiveEditor:true, tinyMCE, tinyMCEPreInit, basisHTMLBuilderData, basisMCE, QTags, quicktags */
(function (window, $, _) {
	'use strict';

	/**
	 * Function that produces the main global app object.
	 *
	 * @since 1.0.
	 */
	function BasisBuilderApp() {
		/**
		 * Holds the Backbone collection objects.
		 *
		 * @since 1.0.
		 *
		 * @private
		 */
		var _Collections = {};

		/**
		 * Holds the Backbone model objects.
		 *
		 * @since 1.0.
		 *
		 * @private
		 */
		var _Models = {};

		/**
		 * Holds the Backbone view objects.
		 *
		 * @since 1.0.
		 *
		 * @private
		 */
		var _Views = {};

		/**
		 * Add different interpreters for underscore templates.
		 *
		 * @since 1.0.
		 *
		 * @type {{evaluate: RegExp, interpolate: RegExp, escape: RegExp}}
		 */
		var _templateOptions = {
			evaluate   : /<#([\s\S]+?)#>/g,
			interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
			escape     : /\{\{([^\}]+?)\}\}(?!\})/g
		};

		/**
		 * Get the current value of the iterator.
		 *
		 * @since  1.0.
		 *
		 * @return {Number}
		 */
		var _getIterator = function () {
			return basisHTMLBuilderData.iterator;
		};

		/**
		 * Incrementer the iterator value.
		 *
		 * @since  1.0.
		 *
		 * @return {Number}
		 */
		var _incrementIterator = function () {
			basisHTMLBuilderData.iterator++;
			return _getIterator();
		};

		/**
		 * Initiate a WP Editor.
		 *
		 * @since  1.0.
		 *
		 * @param  id
		 * @param  type
		 * @param  extra
		 * @return void
		 */
		var _initEditor = function (id, type, extra) {
			// Set default if not set
			extra = extra || '';

			var mceInit = {},
				qtInit = {},
				tempName = 'basiseditortemp' + type + extra;

			/**
			 * Get the default values for this section type from the pre init object. Store them in a new object with
			 * the id of the section as the key.
			 */
			mceInit[id] = tinyMCEPreInit.mceInit[tempName];
			qtInit[id] = tinyMCEPreInit.qtInit[tempName];

			/**
			 * Append the new object to the pre init object. Doing so will provide the TinyMCE and quicktags code with
			 * the proper configuration information that is needed to init the editor.
			 */
			tinyMCEPreInit.mceInit = $.extend(tinyMCEPreInit.mceInit, mceInit);
			tinyMCEPreInit.qtInit = $.extend(tinyMCEPreInit.qtInit, qtInit);

			// Change the ID within the settings to correspond to the section ID
			tinyMCEPreInit.mceInit[id].elements = id;
			tinyMCEPreInit.qtInit[id].id = id;

			// Update the selector as well
			if (parseInt(tinyMCE.majorVersion, 10) >= 4) {
				tinyMCEPreInit.mceInit[id].selector = '#' + id;
			}

			// Only display the tinyMCE instance if in that mode. Else, the buttons will display incorrectly.
			if ('tinymce' === basisMCE) {
				tinyMCE.init(tinyMCEPreInit.mceInit[id]);
			}

			/**
			 * This is a bit of a back. In the quicktags.js script, the buttons are only added when this variable is
			 * set to false. It is unclear exactly why this is the case. By setting this variable, the editors are
			 * properly initialized. Not taking this set will cause the quicktags to be missing.
			 */
			QTags.instances[0] = false;

			// Init the quicktags
			quicktags(tinyMCEPreInit.qtInit[id]);

			/**
			 * When using the different editors, the wpActiveEditor variables needs to be set. If it is not set, the
			 * Add Media buttons, as well as some other buttons will add content to the wrong editors. This strategy
			 * assumes that if you are clicking on the editor, it is the active editor.
			 */
			var $wrapper = $('#wp-' + id.replace(/\[/g, '\\[').replace(/]/g, '\\]') + '-wrap');

			$wrapper.on('click', '.add_media', {id: id}, function (evt) {
				wpActiveEditor = evt.data.id;
			});

			$wrapper.on('click', {id: id}, function (evt) {
				wpActiveEditor = evt.data.id;
			});
		};

		/**
		 * Set the sort order input.
		 *
		 * @since  1.0.
		 *
		 * @param  order
		 * @param  builder
		 *
		 * @return void
		 */
		var _setSectionOrder = function (order, builder) {
			// Turn list into comma separated list
			order = order.join();

			// Set the val of the input
			$('#basis-section-order-' + builder).val(order);
		};

		/**
		 * Register the order of a section in the order value.
		 *
		 * @since  1.0.
		 *
		 * @param  id
		 * @param  builder
		 *
		 * @return void
		 */
		var _addSectionsOrder = function (id, builder) {
			var currentOrder = $('#basis-section-order-' + builder).val(),
				currentOrderArray;

			if ('' === currentOrder) {
				currentOrderArray = [id];
			} else {
				currentOrderArray = currentOrder.split(',');
				currentOrderArray.push(id);
			}

			_setSectionOrder(currentOrderArray, builder);
		};

		/**
		 * Remove an item from the sort order.
		 *
		 * @since  1.0.
		 *
		 * @param  id
		 * @param  builder
		 *
		 * @return void
		 */
		var _removeSectionsOrder = function (id, builder) {
			var currentOrder = $('#basis-section-order-' + builder).val(),
				currentOrderArray;

			if ('' === currentOrder) {
				currentOrderArray = [];
			} else {
				currentOrderArray = currentOrder.split(',');
				currentOrderArray = _.reject(currentOrderArray, function (item) {
					return id === item;
				});
			}

			_setSectionOrder(currentOrderArray, builder);
		};

		/**
		 * Default speed for open/show animations.
		 *
		 * @since 1.0.
		 *
		 * @type  {number}
		 */
		var _openSpeed = 400;

		/**
		 * Default speed for close/hide animations.
		 *
		 * @since 1.0.
		 *
		 * @type  {number}
		 */
		var _closeSpeed = 250;

		/**
		 * Produce the proper exeCommand for removing a TinyMCE editor.
		 *
		 * TinyMCE 4.0 change the command name so a different command is needed for each version of TinyMCE. This
		 * function detects the version of TinyMCE and returns the proper command.
		 *
		 * @since   1.0.9
		 *
		 * @returns {string}
		 * @private
		 */
		var _removeCommand = function() {
			if (parseInt(tinyMCE.majorVersion, 10) >= 4) {
				return 'mceRemoveEditor';
			} else {
				return 'mceRemoveControl';
			}
		};

		/**
		 * Produce the proper exeCommand for adding a TinyMCE editor.
		 *
		 * TinyMCE 4.0 change the command name so a different command is needed for each version of TinyMCE. This
		 * function detects the version of TinyMCE and returns the proper command.
		 *
		 * @since   1.0.9
		 *
		 * @returns {string}
		 * @private
		 */
		var _addCommand = function() {
			if (parseInt(tinyMCE.majorVersion, 10) >= 4) {
				return 'mceAddEditor';
			} else {
				return 'mceAddControl';
			}
		};

		// Return accessible private vars
		return {
			Collections        : _Collections,
			Models             : _Models,
			Views              : _Views,
			templateOptions    : _templateOptions,
			getIterator        : _getIterator,
			incrementIterator  : _incrementIterator,
			initEditor         : _initEditor,
			setSectionOrder    : _setSectionOrder,
			addSectionsOrder   : _addSectionsOrder,
			removeSectionsOrder: _removeSectionsOrder,
			openSpeed          : _openSpeed,
			closeSpeed         : _closeSpeed,
			removeCommand      : _removeCommand,
			addCommand         : _addCommand
		};
	}

	// Kick off the app and add it to the window object
	window.BasisBuilderApp = new BasisBuilderApp();
})(window, jQuery, _);