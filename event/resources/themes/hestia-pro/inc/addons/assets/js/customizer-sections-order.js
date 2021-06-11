/**
 * File customizer_sections_order.js
 *
 * The main file for sections order.
 *
 * @package Hestia
 */

/* global control_settings */
/* global wp */

jQuery( document ).ready(
	function(){
		'use strict';

		var sections_container = control_settings.sections_container;
		var blocked_items      = control_settings.blocked_items;
		var saved_data_input   = control_settings.saved_data_input;
		update_order(); // Do this at the beginning to display sections in customizer that were just added

		jQuery( sections_container ).sortable(
			{
				axis: 'y',
				items: '> li:not(.panel-meta, ' + blocked_items + ')',
				handle: '> h3',
				update: function() {
					update_order();
				},
				helper : 'clone',
				placeholder: 'ui-state-highlight'
			}
		);

		function update_order(){
			var values     = {};
			var idsInOrder = jQuery( sections_container ).sortable(
				{
					axis: 'y',
					items: '> li:not(.panel-meta, ' + blocked_items + ')',
					handle: '> h3',
					helper : 'clone',
					placeholder: 'ui-state-highlight'
				}
			);

			var sections   = idsInOrder.sortable( 'toArray' );
			for (var i = 0; i < sections.length; i++) {
				var section_id     = sections[i].replace( 'accordion-section-','' );
				var section_priority = (i + 2) * 5;
				wp.customize.section(section_id).priority(section_priority);
				values[section_id] = section_priority;
			}
			var data_to_send = JSON.stringify( values );
			jQuery( saved_data_input ).val( data_to_send );
			jQuery( saved_data_input ).trigger( 'change' );
		}
	}
);
