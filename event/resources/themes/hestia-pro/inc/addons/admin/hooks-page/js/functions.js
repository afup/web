/**
 *  Exported hestia_filter_hooks
 *
 *  @package Hestia
 */

/* exported hestia_filter_hooks */
/* globals hestia_hook_var */

/**
 * Filter hooks
 */
function hestia_filter_hooks() {

	var search_val = '';

	if ( typeof jQuery( '#hestia_search_hooks' ) !== 'undefined' ) {

		if ( typeof jQuery( '#hestia_search_hooks' ).val() !== 'undefined' ) {

			search_val = jQuery( '#hestia_search_hooks' ).val().toUpperCase();

			if ( typeof search_val !== 'undefined' ) {

				jQuery( '#hestia_hooks_settings th' ).each(
					function () {

						if ( jQuery( this ).text().toUpperCase().indexOf( search_val ) > -1 ) {
							jQuery( this ).parent().removeClass( 'hooks-none' );
						} else {
							jQuery( this ).parent().addClass( 'hooks-none' );
						}
					}
				);

			}

		}

	}
}

// An object of kyes to use for CodeMirror auto-complete
var ExcludedIntelliSenseTriggerKeys = {
	'8': 'backspace',
	'16': 'shift',
	'17': 'ctrl',
	'18': 'alt',
	'19': 'pause',
	'20': 'capslock',
	'27': 'escape',
	'33': 'pageup',
	'34': 'pagedown',
	'35': 'end',
	'36': 'home',
	'37': 'left',
	'38': 'up',
	'39': 'right',
	'40': 'down',
	'45': 'insert',
	'46': 'delete',
	'91': 'left window key',
	'92': 'right window key',
	'93': 'select',
	'107': 'add',
	'109': 'subtract',
	'110': 'decimal point',
	'111': 'divide',
	'112': 'f1',
	'113': 'f2',
	'114': 'f3',
	'115': 'f4',
	'116': 'f5',
	'117': 'f6',
	'118': 'f7',
	'119': 'f8',
	'120': 'f9',
	'121': 'f10',
	'122': 'f11',
	'123': 'f12',
	'144': 'numlock',
	'145': 'scrolllock',
	'186': 'semicolon',
	'187': 'equalsign',
	'188': 'comma',
	'189': 'dash',
	'190': 'period',
	'191': 'slash',
	'192': 'graveaccent',
	'220': 'backslash',
	'222': 'quote'
};

// An arroy of CodeMirro editors
var hookEditor = [];

// Initialize CodeMirror to text areas
function runCodeMirror( contentArea, isPHP, refresh ) {
	var regex         = /\[(.*?)\]/;
	var contentAreaID = contentArea.id;
	var match         = regex.exec( contentAreaID );
		match         = match[ 1 ];
	if ( refresh !== undefined && refresh === true ) {
		// Turn Editors to Textareas before switching language
		hookEditor[ match ].toTextArea();
	}
	// Turn Textareas to CodeMirror editors
	hookEditor[ match ] = wp.CodeMirror.fromTextArea(
		document.getElementById( contentAreaID ), {
			lineNumbers: true,
			mode: isPHP,
			lint: true,
			gutters: [ 'CodeMirror-lint-markers' ],
			styleActiveLine: true,
			matchBrackets: true,
		}
	);
	// Auto-completion on keyup
	hookEditor[ match ].on(
		'keyup', function( hookEditor, event ) {
			var __Cursor = hookEditor.getDoc().getCursor();
			var __Token  = hookEditor.getTokenAt( __Cursor );
			if ( ! hookEditor.state.completionActive && ! ExcludedIntelliSenseTriggerKeys[ ( event.keyCode || event.which ).toString() ] && (__Token.type === 'tag' || __Token.string.includes( ' ' ) | __Token.string.includes( '	' ) || __Token.string === '<' || __Token.string === '/')) {
				wp.CodeMirror.commands.autocomplete( hookEditor, null, { completeSingle: false } );
			}
		}
	);
}

// Adding linting on PageLoad
function addLinter() {
	var tableColumn = document.querySelectorAll( 'table.form-table tr' );
	tableColumn.forEach(
		function( table ) {
				var contentArea = table.querySelector( '.hestia_hook_field_textarea' );
				var checkbox    = table.querySelector( 'input[type="checkbox"]' );
				var isPHP;
			if ( checkbox.checked ) {
				isPHP = 'application/x-httpd-php-open';
			} else {
				isPHP = 'htmlmixed';
			}
				runCodeMirror( contentArea, isPHP );
		}
	);
}

// Changing linting language
function toggleLinter( e ) {
	var refresh     = true;
	var target      = e.target;
	var checkboxID  = target.id;
	var textareaID  = checkboxID.replace( '_php', '' );
	var contentArea = document.getElementById( textareaID );
	var isPHP;
	if ( target.checked ) {
		isPHP = 'application/x-httpd-php-open';
	} else {
		isPHP = 'htmlmixed';
	}
	runCodeMirror( contentArea, isPHP, refresh );
}

// Check linting Errors
function checkLintingError( e ) {
	var container = document.getElementById( 'poststuff' );
	var error     = '<div class="notice notice-error"><p>' + hestia_hook_var.php_error + '</p></div>';
	var isError   = 0;
	for ( var editor in hookEditor ) {
		if ( hookEditor[ editor ].options.mode === 'application/x-httpd-php-open' ) {
			if ( hookEditor[ editor ].state.lint.marked.length > 0 ) {
				isError = isError + 1;
			}
		}
	}
	if ( isError > 0 ) {
		e.preventDefault();
		container.insertAdjacentHTML( 'afterbegin', error );
	}
}

// Only load the code if wp.CodeMirror object is available, hence WordPress version is 4.9 or above.
if ( typeof wp.CodeMirror !== 'undefined' ) {
	document.addEventListener( 'onload', addLinter() );

	var checkboxes = document.querySelectorAll( '.execute input[type="checkbox"' );
	checkboxes.forEach(
		function ( checkbox ) {
				checkbox.addEventListener( 'change', toggleLinter );
		}
	);

	var button = document.querySelector( '.submitbox input.button' );
	button.addEventListener( 'click', checkLintingError );
}
