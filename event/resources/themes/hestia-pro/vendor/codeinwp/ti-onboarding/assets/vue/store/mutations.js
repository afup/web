/* jshint esversion: 6 */
const setAjaxState = (state, data) => {
  state.ajaxLoader = data
}
const setImportingState = (state, data) => {
  state.importing = data
}
const saveSitesData = (state, data) => {
  state.sitesData = data
}
const showPreview = (state, data) => {
  state.previewOpen = data
}
const showImportModal = (state, data) => {
  state.importModalState = data
}
const populatePreview = (state, data) => {
  state.previewData = data.siteData
  state.currentPreviewSlug = data.currentItem ? data.currentItem : ''
}
const updateSteps = (state, data) => {
  state.currentStep = data
}
const updatePlugins = (state, data) => {
  state.importOptions.installablePlugins = data
}
const updateImportOptions = (state, data) => {
  state.importOptions = data
}
const updateEditor = (state, data) => {
  state.editor = data
}
const setFrontPageId = (state, data) => {
  state.frontPageId = data
}

const resetStates = (state) => {
  state.previewOpen = false
  state.importModalState = false
  state.previewData = {}
  state.currentStep = 'inactive'
  state.importOptions = {
    content: true,
    customizer: true,
    widgets: true
  }

  state.importSteps.content.done = 'no'
  state.importSteps.plugins.done = 'no'
  state.importSteps.theme_mods.done = 'no'
  state.importSteps.widgets.done = 'no'
  state.errorToast = ''
}

const migrationComplete = (state) => {
  state.migration = 'complete'
}

export default {
  setAjaxState,
  saveSitesData,
  showPreview,
  showImportModal,
  populatePreview,
  setImportingState,
  updateSteps,
  updatePlugins,
  updateImportOptions,
  resetStates,
  migrationComplete,
  updateEditor,
  setFrontPageId
}
