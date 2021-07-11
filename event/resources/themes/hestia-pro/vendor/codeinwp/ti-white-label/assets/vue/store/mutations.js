const updateThemeFields = ( state, data ) => {
	state.formsData = data;
};

const clearToast = ( state ) => {
	state.toast = {};
};

export default {
	updateThemeFields,
	clearToast
}