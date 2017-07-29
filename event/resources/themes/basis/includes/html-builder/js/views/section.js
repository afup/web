/* global Backbone, jQuery, _, BasisBuilderApp, wp:true, tinyMCE, switchEditors */
(function (window, Backbone, $, _, BasisBuilderApp) {
	'use strict';

	BasisBuilderApp.Views.Section = Backbone.View.extend({
		template: '',

		className: 'basis-section basis-section-open',

		$headerTitle: '',

		$titleInput: '',

		$titlePipe: '',

		serverRendered: false,

		collectionHolder: '',

		$document: $(window.document),

		events: {
			'click .basis-section-toggle': 'toggleSection',
			'click .basis-section-remove': 'removeSection',
			'keyup .basis-section-header-title-input': 'constructHeader',
			'click .basis-media-uploader-add': 'initUploader',
			'click .basis-media-uploader-remove': 'removeImage',
			'click .wp-switch-editor': 'adjustEditorHeightOnClick'
		},

		initialize: function (options) {
			this.model = options.model;
			this.id = 'basis-section-' + this.model.get('iterator');
			this.serverRendered = ( options.serverRendered ) ? options.serverRendered : false;
			this.collectionHolder = ( 'product' === this.model.get('builder') ) ? 'sectionsProduct' : 'sectionsSlideshow';

			_.templateSettings = BasisBuilderApp.templateOptions;
			this.template = _.template($('#tmpl-basis-' + this.model.get('sectionType')).html());

			if (true === this.serverRendered && _.contains(['feature', 'profile'], this.model.get('sectionType'))) {
				this.initSortableColumns();
			}
		},

		render: function () {
			this.$el.html(this.template(this.model.toJSON())).addClass('basis-section-' + this.model.get('sectionType')).attr('id', this.id);

			if (_.contains(['feature', 'profile'], this.model.get('sectionType'))) {
				this.initSortableColumns();
			}

			return this;
		},

		toggleSection: function (evt) {
			evt.preventDefault();

			var $this = $(evt.target),
				$section = $this.parents('.basis-section'),
				$sectionBody = $('.basis-section-body', $section),
				$input = $('.basis-section-state', this.$el);

			if ($section.hasClass('basis-section-open')) {
				$sectionBody.slideUp(BasisBuilderApp.closeSpeed, function() {
					$section.removeClass('basis-section-open');
					$input.val('closed');
				});
			} else {
				$sectionBody.slideDown(BasisBuilderApp.openSpeed, function() {
					$section.addClass('basis-section-open');
					$input.val('open');
				});
			}
		},

		removeSection: function (evt) {
			evt.preventDefault();
			BasisBuilderApp.removeSectionsOrder('basis-section-' + this.model.get('iterator'), this.model.get('builder'));

			// Fade and slide out the section, then cleanup view and reset stage on complete
			this.$el.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, BasisBuilderApp.closeSpeed, function() {
				this.remove();
				BasisBuilderApp[this.collectionHolder].toggleStageClass();
			}.bind(this));
		},

		constructHeader: function () {
			if ('' === this.$headerTitle) {
				this.$headerTitle = $('.basis-section-header-title', this.$el);
			}

			if ('' === this.$titleInput) {
				this.$titleInput = $('.basis-section-header-title-input', this.$el);
			}

			if ('' === this.$titlePipe) {
				this.$titlePipe = $('.basis-section-header-pipe', this.$el);
			}

			var input = this.$titleInput.val();

			// Set the input
			this.$headerTitle.html(_.escape(input));

			// Hide or show the pipe depending on what content is available
			if ('' === input) {
				this.$titlePipe.addClass('basis-section-header-pipe-hidden');
			} else {
				this.$titlePipe.removeClass('basis-section-header-pipe-hidden');
			}
		},

		initUploader: function (evt) {
			evt.preventDefault();

			var $this = $(evt.target),
				$parent = $this.parents('.basis-uploader'),
				$placeholder = $('.basis-media-uploader-placeholder', $parent),
				$input = $('.basis-media-uploader-value', $parent),
				$remove = $('.basis-media-uploader-remove', $parent),
				$add = $('.basis-media-uploader-set-link', $parent);

			var frame = frame || {};

			// If the media frame already exists, reopen it.
			if ('function' === typeof frame.open) {
				frame.open();
				return;
			}

			// Create the media frame.
			frame = wp.media.frames.frame = wp.media({
				title: $this.data('title'),
				button: {
					text: $this.data('buttonText')
				},
				multiple: false
			});

			// When an image is selected, run a callback.
			frame.on('select', function () {
				// We set multiple to false so only get one image from the uploader
				var attachment = frame.state().get('selection').first().toJSON();

				// Remove the attachment caption
				attachment.caption = '';

				// Build the image
				var props = wp.media.string.props(
					{},
					attachment
				);

				// The URL property is blank, so complete it
				props.url = attachment.url;

				var image = wp.media.string.image( props );

				// Show the image
				$placeholder.html(image);

				// Record the chosen value
				$input.val(attachment.id);

				// Hide the link to set the image
				$add.hide();

				// Show the remove link
				$remove.show();
			});

			// Finally, open the modal
			frame.open();
		},

		removeImage: function (evt) {
			evt.preventDefault();

			var $this = $(evt.target),
				$parent = $this.parents('.basis-uploader'),
				$placeholder = $('.basis-media-uploader-placeholder', $parent),
				$input = $('.basis-media-uploader-value', $parent),
				$set = $('.basis-media-uploader-add', $parent);

			// Remove the image
			$placeholder.empty();

			// Remove the value from the input
			$input.removeAttr('value');

			// Hide the remove link
			$this.hide();

			// Show the set link
			$set.show();
		},

		initSortableColumns: function() {
			var self = this;

			$('.basis-section-sortable-stage', this.$el).sortable({
				handle: '.basis-sortable-handle',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSizeType: true,
				distance: 2,
				tolerance: 'pointer',
				start: function (event, ui) {
					// Set the height of the placeholder to that of the sorted item
					var $items = $('.basis-sortable', self.$el),
						$item = $(ui.item.get(0)),
						tallest = $(ui.item.get(0)).height();

					// Get the tallest of the elements
					$.each($items, function() {
						var height = $(this).height();
						tallest = ( height > tallest ) ? height : tallest;
					});

					$('.sortable-placeholder', self.$el).height(tallest);

					var id = $('.wp-editor-area', $item).attr('id');

					if (undefined !== id) {
						tinyMCE.execCommand( BasisBuilderApp.removeCommand(), false, id );
						delete tinyMCE.editors.id;
					}
				},
				stop : function (event, ui) {
					var $item = $(ui.item.get(0)),
						data = $(this).sortable('toArray');

					$('.basis-section-order', self.$el).val(data);

					var id = $('.wp-editor-area', $item).attr('id'),
						$wrap = $('.wp-editor-wrap', $item),
						el = tinyMCE.DOM.get(id);

					if (undefined !== id) {
						// If the text area (i.e., non-tinyMCE) is showing, do not init the editor
						if ($wrap.hasClass('tmce-active')) {
							// Restore the content, with pee
							el.value = switchEditors.wpautop(el.value);

							// Activate tinyMCE
							tinyMCE.execCommand( BasisBuilderApp.addCommand(), false, id );
						}
					}
				}
			});
		},

		syncEditorHeight: function(evt, baseEl) {
			baseEl = baseEl || 'iframe';

			var $this = $(evt.target),
				$parent = $this.parents('.wp-editor-wrap'),
				$iframe = $('.mceIframeContainer iframe', $parent),
				iframeHeight = $iframe.height(),
				$textarea = $('textarea', $parent),
				textareaHeight = $textarea.height();

			if ('iframe' === baseEl) {
				$textarea.height(parseInt(iframeHeight, 10) + 1);
			} else {
				$iframe.height(parseInt(textareaHeight, 10) - 1);
			}
		},

		adjustEditorHeightOnClick: function(evt) {
			evt.preventDefault();

			var $this = $(evt.target),
				baseEl = ($this.hasClass('switch-html')) ? 'iframe' : 'textarea';

			this.syncEditorHeight(evt, baseEl);
		}
	});
})(window, Backbone, jQuery, _, BasisBuilderApp);