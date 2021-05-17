<script>
  export default {
    data: function () {
      return {
        strings: this.$store.state.strings,
        wasImported: this.$store.state.contentImported
      }
    },
    computed: {
      defaultTemplate: function () {
        return this.$store.state.sitesData.default_template
      }
    },
    methods: {
      redirectToHome: function () {
        window.location.replace(this.$store.state.homeUrl)
      }
    }
  }
</script>

<template>

	<div v-if="Object.keys(this.defaultTemplate).length && wasImported !== 'yes'">
		<div class="site-box">
			<div class="preview-image">
				<img :src="defaultTemplate.screenshot" :alt="defaultTemplate.name">
			</div>
			<div class="footer">
				<h4>{{defaultTemplate.name}}</h4>
				<div class="theme-actions">
					<button class="button button-primary" @click="redirectToHome">
						{{strings.go_to_site}}
					</button>
				</div>
			</div>
		</div>
	</div>
</template>
