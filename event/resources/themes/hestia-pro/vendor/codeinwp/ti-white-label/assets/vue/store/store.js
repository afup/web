/* global tiWhiteLabelLib */
/* exported tiWhiteLabelLib */
import Vue from 'vue'
import Vuex from 'vuex'
import actions from './actions'
import mutations from './mutations'

Vue.use( Vuex );

let data = {
	agency_form: {
		author_name: tiWhiteLabelLib.fields.author_name ? tiWhiteLabelLib.fields.author_name : '',
		author_url: tiWhiteLabelLib.fields.author_url ? tiWhiteLabelLib.fields.author_url : '',
		starter_sites: tiWhiteLabelLib.fields.starter_sites ? tiWhiteLabelLib.fields.starter_sites : false,
	},
	theme_form: {
		theme_name: tiWhiteLabelLib.fields.theme_name ? tiWhiteLabelLib.fields.theme_name : '',
		theme_description:  tiWhiteLabelLib.fields.theme_description ? tiWhiteLabelLib.fields.theme_description : '',
		screenshot_url: tiWhiteLabelLib.fields.screenshot_url ? tiWhiteLabelLib.fields.screenshot_url : '',
	},
	white_label_form: {
		white_label: tiWhiteLabelLib.fields.white_label ? tiWhiteLabelLib.fields.white_label : false,
		license: tiWhiteLabelLib.fields.license ? tiWhiteLabelLib.fields.license : false,
	}
};

if( tiWhiteLabelLib.settings.type === 'plugin' ){
	data.plugin_form = {
		plugin_name: tiWhiteLabelLib.fields.plugin_name ? tiWhiteLabelLib.fields.plugin_name : '' ,
		plugin_description: tiWhiteLabelLib.fields.plugin_description ? tiWhiteLabelLib.fields.plugin_description : '',
	}
}


export default new Vuex.Store( {
	state: {
		strings: tiWhiteLabelLib.i18ln,
		formsData: data,
		toast: {}
	},
	actions,
	mutations
} )
