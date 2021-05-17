<template>
	<div class="preview-sidebar">
		<div class="preview-sidebar__container">
			<div class="nav-buttons">
				<button class="back" @click="cancelPreview()"><i class="dashicons dashicons-no"></i>
				</button>
				<div class="prev-next">
				<button class="back" @click="changeTemplate(false)"><i class="dashicons dashicons-arrow-left-alt2"></i>
				</button>
				<button class="next" @click="changeTemplate(true)"><i class="dashicons dashicons-arrow-right-alt2"></i>
				</button>
				</div>
			</div>
			<h5 class="site-title ellipsis">{{site_data.title}}</h5>
			<div class="buttons-wrap">
				<button class="button button-primary" v-on:click="site_data.in_pro ? buyPro() : showModal()">
					{{ site_data.in_pro ? strings.pro_btn : strings.import_btn}}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
  /* jshint esversion: 6 */

  export default {
    name: 'preview-sidebar',
    data: function () {
      return {
        strings: this.$store.state.strings
      }
    },
    props: {
      site_data: {
        default: {},
        type: Object
      }
    },
    methods: {
      cancelPreview: function () {
        document.body.classList.remove('ti-ob--preview-open')
        this.$store.commit('resetStates')
      },
      showModal: function () {
        this.$store.commit('showImportModal', true)
      },
      buyPro: function () {
        let link = this.site_data.outbound_link ? this.site_data.outbound_link : this.$store.state.sitesData.pro_link
        let win = window.open(link, '_blank')
        win.focus()
      },
      allCurrentEditorSites: function () {
        let local = this.$store.state.sitesData.local
        let remote = this.$store.state.sitesData.remote
        let upsell = this.$store.state.sitesData.upsell
        let editor = this.$store.state.editor


        return {...local[editor], ...remote[editor], ...upsell[editor]}
      },
      changeTemplate: function (next = true) {
        this.$store.state.previewData = {}
        let currentDemo = this.$store.state.currentPreviewSlug
        let allDemos = Object.keys(this.allCurrentEditorSites())
        let currIndex = allDemos.indexOf(currentDemo)
        if (next) {
          currIndex++
          if (currIndex > allDemos.length-1) {
            currIndex = 0
          }
        } else {
          currIndex--
          if (currIndex < 0) {
            currIndex = allDemos.length-1
          }
        }
        this.$store.commit('populatePreview', { siteData : this.allCurrentEditorSites()[allDemos[currIndex]], currentItem: allDemos[currIndex]})
      }
    }
  }
</script>
