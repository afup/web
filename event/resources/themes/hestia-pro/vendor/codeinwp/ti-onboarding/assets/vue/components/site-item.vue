<template>
    <div class="site-box" :class="siteData.pricing">
        <div class="preview-image" :class="{ 'demo-pro' : siteData.in_pro }">
            <div class="preview-action" @click="showPreview()">
                <span class="previewButton">
                {{this.$store.state.strings.preview_btn}}
                </span>
            </div>
            <img :src="siteData.screenshot" :alt="siteData.title">
        </div>
        <div class="footer">
            <h4>{{siteData.title}}</h4>
            <div class="theme-actions">
                <button class="button button-secondary" v-on:click="showPreview()">
                    {{this.$store.state.strings.preview_btn}}
                </button>
                <button class="button button-primary" v-if="! siteData.in_pro" v-on:click="importSite()">
                    {{strings.import_btn}}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
  /* jshint esversion: 6 */

  import { getInstallablePlugins } from '../common/common.js'

  export default {
    name: 'site-item',
    data: function () {
      return {
        strings: this.$store.state.strings
      }
    },
    props: {
      siteData: {
        default: {},
        type: Object,
        required: true
      },
      siteSlug: {
        default: '',
        type: String,
        required: true
      }
    },
    methods: {
      setupImportData: function () {
        let recommended = this.siteData.recommended_plugins ? this.siteData.recommended_plugins : {}
        let mandatory = this.siteData.mandatory_plugins ? this.siteData.mandatory_plugins : {}
        let defaultOff = this.siteData.default_off_recommended_plugins ? this.siteData.default_off_recommended_plugins : []
        let plugins = getInstallablePlugins(mandatory, recommended, defaultOff)
        this.$store.commit('updatePlugins', plugins)
      },
      importSite: function () {
        this.setupImportData()
        this.$store.commit('populatePreview', { siteData: this.siteData, currentItem: this.siteSlug })
        this.$store.commit('showImportModal', true)
      },
      showPreview: function () {
        document.body.classList.add( 'ti-ob--preview-open' )
        this.setupImportData()
        this.$store.commit('populatePreview', { siteData : this.siteData, currentItem: this.siteSlug})
        this.$store.commit('showPreview', true)
      }
    },
    created() {
      if( this.$store.state.readyImport && this.$store.state.readyImport === this.$props.siteSlug) {
        this.setupImportData()
        this.importSite()
      }
    }
  }
</script>
