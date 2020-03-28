var TableFilter = require('tablefilter');

window.TableFilter = TableFilter;

/* Deprécié, utiliser fup-tab-filterable */
var tf = new TableFilter(document.querySelector('.tab--filterable'), {
	base_path: '/js_dist/tablefilter/',
	filters_row_index: 1,
	headers_row_index: 0,
	col_1: 'select',
	col_2: 'select',
	col_3: 'select',
	col_7: 'select',
	col_8: 'none',
	col_9: 'none',
	themes: [{
		name: 'transparent'
	}],
	alternate_rows: true,
	rows_counter: true,
	btn_reset: true,
	loader: true,
	status_bar: true,
	col_types: [
		{ type: 'date', locale: 'fr' },
		'string',
		'string',
		'string',
		'string',
		{ type: 'formatted-number', decimal: ',', thousands: ' ' },
		{ type: 'formatted-number', decimal: ',', thousands: ' ' },
		'string'
	],
});
tf.init();
