/*jshint esversion: 6 */

import Vue from 'vue';
import App from './components/main.vue';
import store from './store/store.js';
import VueForm from 'vue-form';
import ToggleButton from 'vue-js-toggle-button'

Vue.use(VueForm);
Vue.use(ToggleButton);

window.onload = function () {
	new Vue( {
		el: '#ti-lib-app',
		store,
		components: {
			App
		},
		created() {
			// store.dispatch( 'initialize', { req: 'Init Sites Library', data: {} } );
		}
	} );
};