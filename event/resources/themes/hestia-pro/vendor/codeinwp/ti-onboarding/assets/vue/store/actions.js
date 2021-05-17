/* jshint esversion: 6 */
/* global themeisleSitesLibApi, console */
import Vue from 'vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

const initialize = function ({commit, state}) {
  commit('setAjaxState', true)
  console.log('%c Fetching sites.', 'color: #FADD6E')
  Vue.http({
    url: themeisleSitesLibApi.root + '/initialize_sites_library',
    method: 'GET',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      commit('setAjaxState', false)
      commit('saveSitesData', response.body.data)
    } else {
      toastError(response, state, 'initialization')
    }
  }).catch(function (error) {
    toastError(error, state, 'initialization')
  })
}

const importSite = function ({commit, state}, data) {
  commit('setImportingState', true)
  installPlugins({commit, state}, data)
}

const doneImport = function ({commit}) {
  commit('updateSteps', 'done')
  commit('setImportingState', false)
  console.log('%c Import Done.', 'color: #64cd6d')
}

const installPlugins = function ({commit, state}, data) {
  let result = false
  if (Object.values(state.importOptions.installablePlugins).indexOf(true) > -1) {
    result = true
  }

  if (result === false) {
    state.importSteps.plugins.done = 'skip'
    importContent({commit, state}, data)
    return false
  }
  commit('updateSteps', 'plugins')
  Vue.http({
    url: themeisleSitesLibApi.root + '/install_plugins',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'data': state.importOptions.installablePlugins
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      state.importSteps.plugins.done = 'yes'
      console.log('%c Installed Plugins.', 'color: #4B9BE7')
      if (state.importOptions.isMigration === true) {
        state.currentStep = 'content'
        migrateTemplate({commit, state}, data)
        return false
      }
      importContent({commit, state}, data)
    } else {
      toastError(response, state, 'plugins')
    }
  }).catch(function (error) {
    toastError(error, state, 'plugins')
  })
}

const importContent = function ({commit, state}, data) {
  if (state.importOptions.content === false) {
    state.importSteps.content.done = 'skip'
    importThemeMods({commit, state}, data)
    return false
  }
  commit('updateSteps', 'content')
  Vue.http({
    url: themeisleSitesLibApi.root + '/import_content',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'data': {
        'contentFile': data.content.content_file,
        'frontPage': data.content.front_page,
        'shopPages': data.content.shop_pages ? data.content.shop_pages : null,
        'source': data.source,
        'editor': state.editor ? state.editor : '',
        'demoSlug': state.currentPreviewSlug,
      }
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      state.importSteps.content.done = 'yes'
      if (response.body.frontpage_id) {
        commit('setFrontPageId', response.body.frontpage_id)
      }
      console.log('%c Imported Content.', 'color: #4B9BE7')
      importThemeMods({commit, state}, data)
    } else {
      toastError(response, state, 'content')
    }
  }).catch(function (error) {
    toastError(error, state, 'content')
  })
}

const toastError = function (errorObj, state, step) {
  state.errorToast = errorObj.body ? errorObj.body : errorObj
  if (step === 'initialization') {
    state.ajaxLoader = false
    return
  }

  state.currentStep = 'error'
  state.importing = false
  switch (step) {
    case 'plugins':
      state.importSteps.plugins.done = 'error'
      state.importSteps.content.done = 'skip'
      state.importSteps.theme_mods.done = 'skip'
      state.importSteps.widgets.done = 'skip'
      break
    case 'content':
      state.importSteps.content.done = 'error'
      state.importSteps.theme_mods.done = 'skip'
      state.importSteps.widgets.done = 'skip'
      break
    case 'theme_mods':
      state.importSteps.theme_mods.done = 'error'
      state.importSteps.widgets.done = 'skip'
      break
    case 'widgets':
      state.importSteps.widgets.done = 'error'
      break
  }
  console.log(errorObj)
}

const importThemeMods = function ({commit, state}, data) {
  if (state.importOptions.customizer === false) {
    state.importSteps.theme_mods.done = 'skip'
    importWidgets({commit, state}, data)
    return false
  }
  commit('updateSteps', 'theme_mods')
  Vue.http({
    url: themeisleSitesLibApi.root + '/import_theme_mods',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'data': data.themeMods,
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      state.importSteps.theme_mods.done = 'yes'
      console.log('%c Imported Customizer.', 'color: #4B9BE7')
      importWidgets({commit, state}, data)
    } else {
      toastError(response, state, 'theme_mods')
    }
  }).catch(function (error) {
    toastError(error, state, 'theme_mods')
  })
}

const importWidgets = function ({commit, state}, data) {
  if (state.importOptions.widgets === false) {
    state.importSteps.widgets.done = 'skip'
    doneImport({commit})
    return false
  }
  commit('updateSteps', 'widgets')
  Vue.http({
    url: themeisleSitesLibApi.root + '/import_widgets',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'data': data.widgets
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      state.importSteps.widgets.done = 'yes'
      console.log('%c Imported Widgets.', 'color: #4B9BE7')
      doneImport({commit})
    } else {
      toastError(response, state, 'widgets')
    }
  }).catch(function (error) {
    toastError(error, state, 'widgets')
  })
}

const migrateTemplate = function ({commit, state}, data) {
  Vue.http({
    url: themeisleSitesLibApi.root + '/migrate_frontpage',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'template': data.template,
      'template_name': data.template_name
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.body.success) {
      console.log('%c Imported front page.', 'color: #4B9BE7')
      state.currentStep = 'done'
      state.importSteps.content.done = 'yes'
      commit('migrationComplete', 'done')
      if (response.body.data) {
        commit('setFrontPageId', response.body.data)
      }
      doneImport({commit})
    } else {
      toastError(response, data, 'migration')
    }
  }).catch(function (error) {
    toastError(error, state, 'migration')
  })
}

const dismissMigration = function ({commit, state}, data) {
  Vue.http({
    url: themeisleSitesLibApi.root + '/dismiss_migration',
    method: 'POST',
    headers: {'X-WP-Nonce': themeisleSitesLibApi.nonce},
    body: {
      'theme_mod': data.theme_mod
    },
    responseType: 'json',
    emulateJSON: true
  }).then(function (response) {
    if (response.ok) {
      console.log('%c Notice was dismissed.', 'color: #4B9BE7')
    }
  }).catch(function (error) {
    console.log(error)
  })
}

export default {
  initialize,
  importSite,
  migrateTemplate,
  dismissMigration
}
