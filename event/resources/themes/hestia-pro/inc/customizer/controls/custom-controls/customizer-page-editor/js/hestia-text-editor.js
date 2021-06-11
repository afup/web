/**
 * Text editor
 *
 * @package Hestia
 */

/* global tinyMCE */
/* global wp */

/* exported WPEditorWidget */
var WPEditorWidget = {

	/**
	 * Current content id
	 *
	 * @var string Current content id.
	 */
    contentId: '',

	/**
	 * Z index for Overlay
	 *
	 * @var int Z index for Overlay.
	 */
	wpFullOverlayOriginalZIndex: 0,

	/**
	 * Visible or not
	 *
	 * @var bool Visible or not.
	 */
	isVisible: false,


	init: function ( contentId ) {
		this.contentId = contentId;
		return this;
    },

	run: function ( editorWidget ) {
        editorWidget.toggleEditor();
        editorWidget.updateTinyMCE();
        editorWidget.updateWPEditor();
    },

	/**
	 * Show/Hide editor
	 */
	toggleEditor: function(){
		if ( this.isVisible === true ) {
			this.hideEditor();
		} else {
			this.showEditor( this.contentId );
		}
	},

	/**
	 * Show the editor
	 *
	 * @param contentId
	 */
	showEditor: function(contentId) {
		this.isVisible = true;
		var overlay    = jQuery( '.wp-full-overlay' );

		jQuery( 'body.wp-customizer #wp-editor-widget-container' ).fadeIn( 100 ).animate( {'bottom':'0'} );

		this.wpFullOverlayOriginalZIndex = parseInt( overlay.css( 'zIndex' ) );
		overlay.css( { zIndex: 49000 } );

		this.setEditorContent( contentId );
	},

	/**
	 * Hide editor
	 */
	hideEditor: function() {
		this.isVisible = false;
		jQuery( 'body.wp-customizer #wp-editor-widget-container' ).animate( {'bottom':'-650px'} ).fadeOut();
		jQuery( '.wp-full-overlay' ).css( { zIndex: this.wpFullOverlayOriginalZIndex } );
	},

	/**
	 * Set editor content
	 */
	setEditorContent: function(contentId) {
		var editor  = tinyMCE.get( 'wpeditorwidget' );
		var content = jQuery( '#' + contentId ).val();

		if (typeof editor === 'object' && editor !== null) {
			editor.setContent( content );
		}
		jQuery( '#wpeditorwidget' ).val( content );
	},

    updateTinyMCE: function () {
        var editor  = tinyMCE.get( 'wpeditorwidget' );
		var th = this;
		if( typeof editor !== 'undefined' && editor) {
            editor.on('NodeChange KeyUp', function () {
				th.doUpdate(editor);
            });
        }
    },

    updateWPEditor: function () {
        var editorWidget = document.getElementById( 'wpeditorwidget' );
        var th = this;

        jQuery(editorWidget).on('keyup', function () {
        	var newContent = this.value;
            var contentField = jQuery( '#' + th.contentId );
			contentField.val(newContent);
			contentField.trigger('change');
        });
    },

	doUpdate: function ( editor ) {
        var content = editor.getContent();
        var contentField = jQuery( '#' + this.contentId );
		contentField.val( content );
        contentField.trigger('change');
    }
};

jQuery( window ).on( 'load', function () {

	var editor;

	/**
	 * This handles the click form customizer control.
	 */
	jQuery(document).on('click','.edit-content-button',function (event) {
		event.preventDefault();
		var editorId = jQuery(this).data('editor-id');
		if( typeof editorId !== 'undefined' ) {
			editor = WPEditorWidget.init(editorId);
			WPEditorWidget.run(editor);
		}
	});


	var customize = wp.customize;
	if( typeof customize !== 'undefined' && customize.hasOwnProperty('previewer') ) {

        /**
         * Toggle editor when the user clicks on customizer shortcut.
         */
        customize.previewer.bind(
            'trigger-open-editor', function (data) {
                if (typeof data !== 'undefined') {
                    editor = WPEditorWidget.init(data);
                    WPEditorWidget.run(editor);
                }
            }
        );
    }

	/**
	 * Hide the editor if the user clicks on back button to exit about panel.
	 */
	jQuery( '.customize-section-back' ).on(
		'click',function(){
			if( typeof editor !== 'undefined' ){
				editor.hideEditor();
			}
		}
	);
});
