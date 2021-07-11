<template>
	<div :class="{ 'is__onboarding' : this.$store.state.onboard === 'yes' && ! previewOpen } ">
		<div class="library-wrapper">
			<loader v-if="isLoading" :loading-message="strings.loading"></loader>
			<error-well v-else-if="errorMessage && !modalOpen"></error-well>
			<template v-else>
				<button v-if="this.$store.state.onboard === 'yes'" @click="cancelOnboarding" class="close-modal">
					<span>×</span>
				</button>
				<template v-if="this.$store.state.onboard === 'yes'">
					<div class="header" v-if="themeStrings.onboard_header ||themeStrings.onboard_description">
						<h1 v-if="themeStrings.onboard_header">{{themeStrings.onboard_header}}</h1>
						<p v-if="themeStrings.onboard_description">{{themeStrings.onboard_description}}</p>
					</div>
				</template>

				<migrate-notice v-if="this.$store.state.sitesData.migrate_data.screenshot"></migrate-notice>

				<template>
					<h3 v-if="themeStrings.templates_title">{{themeStrings.templates_title}}</h3>
					<p v-if="themeStrings.templates_description">{{themeStrings.templates_description}}</p>
					<div class="skip-wrap" v-if="this.$store.state.onboard === 'yes' && ! isLoading">
						<a @click="cancelOnboarding" class="skip-onboarding button button-primary">
							{{strings.later}}
						</a>
					</div>
				</template>
				<div class="ti-sites-lib">
					<editors-tabs></editors-tabs>
					<preview v-if="previewOpen"></preview>
				</div>
			</template>
		</div>
		<ImportModal v-if="modalOpen"></ImportModal>
	</div>
</template>

<script>
  import Loader from './loader.vue'
  import ImportModal from './import-modal.vue'
  import MigrateNotice from './migrate-notice.vue'
  import EditorsTabs from './editors-tabs.vue'
  import Preview from './preview.vue'
  import ErrorWell from './error-well.vue'

  module.exports = {
    name: 'app',
    data: function () {
      return {
        strings: this.$store.state.strings
      }
    },
    computed: {
      isLoading: function () {
        return this.$store.state.ajaxLoader
      },
      previewOpen: function () {
        return this.$store.state.previewOpen
      },
      modalOpen: function () {
        return this.$store.state.importModalState
      },
      themeStrings: function () {
        return this.$store.state.sitesData.i18n
      },
      errorMessage () {
        return this.$store.state.errorToast
      }
    },
    methods: {
      cancelOnboarding: function () {
        window.location.replace(this.$store.state.aboutUrl)
      }
    },
    components: {
      Loader,
      Preview,
      ImportModal,
      MigrateNotice,
      EditorsTabs,
      ErrorWell
    }
  }
</script>

<style lang="scss">
	@import "../../scss/style.scss";
</style>
