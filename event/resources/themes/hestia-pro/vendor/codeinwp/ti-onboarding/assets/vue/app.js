/* jshint esversion: 6 */

import Vue from 'vue'
import App from './components/main.vue'
import store from './store/store.js'
import ToggleButton from 'vue-js-toggle-button'
import Clipboard from 'v-clipboard'

Vue.use(ToggleButton)
Vue.use(Clipboard)

window.addEventListener('load', function () {
  new Vue({ // eslint-disable-line no-new
    el: '#ti-sites-library',
    store,
    components: {
      App
    },
    created () {
      store.dispatch('initialize')
    }
  })
})

