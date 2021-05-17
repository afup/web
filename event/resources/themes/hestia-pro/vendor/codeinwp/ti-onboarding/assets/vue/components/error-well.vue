<template>
	<div class="error-well">
		<div class="message">
			<p v-html="strings.error_report"></p>
		</div>
		<div class="buttons">
			<a :href="logUrl" target="_blank" class="button button-primary" type="submit">
				<i class="dashicons dashicons-download"></i>
				{{strings.download_error_log}}
			</a>
		</div>
	</div>
</template>

<script>
  export default {
    name: 'error-well',
    data () {
      return {
        collapsed: true
      }
    },
    computed: {
      errorMessage () {
        return this.$store.state.errorToast
      },
      strings () {
        return this.$store.state.strings
      },
      logUrl () {
        return themeisleSitesLibApi.logUrl
      }
    },
  }
</script>
