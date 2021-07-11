<template>
	<div class="updating-message">
		<p v-if="loadingMessage">{{loadingMessage}}...</p>
	</div>
</template>

<script>
  module.exports = {
    name: 'loader',
    props: { loadingMessage: String }
  }
</script>