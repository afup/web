/* jshint esversion: 6 */
/* global tiWhiteLabelLib, console */
import axios from 'axios';

global.getVueObject = obj => {
	return JSON.parse(JSON.stringify( obj ));
};

const updateFields = function ( { commit, state }, data ) {

	let decodedData = getVueObject(data);

	let requestUrl = tiWhiteLabelLib.root + '/input_save';
	let config     = {
		headers: {
			'X-WP-Nonce': tiWhiteLabelLib.nonce,
			'Content-Type': 'application/json; charset=UTF-8',
		}
	};

	axios.post( requestUrl, decodedData, config ).then( response => {
		if( response.status >= 200 && response.status < 300 ){
			console.log( '%c Form Saved.', 'color: #4B9BE7' );
			state.toast = {
				'message': response.data.message,
				'type': 'success'
			};
		}
	}).catch( error => {

		let response = error.response.data;
		let displayMessage = response.message;
		console.error( displayMessage );
		if( response.hasOwnProperty('markup' ) ){
			displayMessage = response.markup;
		}
		state.toast = {
			'message': displayMessage,
			'type': 'error-toast'
		};
	});
};

export default {
	updateFields
};