<template>
	<div class="ti__stepper">
		<ul>
			<li
					v-for="(step, slug, index) in steps"
					:key="slug"
					:class="{
					'active-step': slug === currentStep,
					'waiting' : step.done === 'no' && slug !== currentStep,
					'done' : step.done === 'yes',
					'error' : step.done === 'error',
					'skip' : step.done === 'skip',
					 }" class="step">
				<div class="step-count">
					<i class="dashicons"
							:class="{
					'dashicons-update': slug === currentStep,
					'dashicons-clock' : step.done === 'no' && slug !== currentStep,
					'dashicons-yes' : step.done === 'yes',
					'dashicons-no' : step.done === 'error',
					'dashicons-redo' : step.done === 'skip',
				}"></i>
				</div>
				<span class="nicename">{{step.nicename}}</span>

			</li>
		</ul>
	</div>
</template>

<script>
  /* jshint esversion: 6 */

  module.exports = {
    name: 'stepper',
    computed: {
      currentStep: function () {
        return this.$store.state.currentStep
      },
      steps: function () {
        return this.$store.state.importSteps
      },
      isMigration: function () {
        return this.$store.state.importOptions.isMigration
      }
    },
    mounted () {
      let importOptions = this.$store.state.importOptions

      if (Object.values(importOptions.installablePlugins).indexOf(true) < 0) {
        this.$store.state.importSteps.plugins.done = 'skip'
      }

      if (importOptions.content === false) {
        this.$store.state.importSteps.content.done = 'skip'
      }

      if (importOptions.customizer === false || this.isMigration) {
        this.$store.state.importSteps.theme_mods.done = 'skip'
      }
      if (importOptions.widgets === false || this.isMigration) {
        this.$store.state.importSteps.widgets.done = 'skip'
      }
    }
  }
</script>