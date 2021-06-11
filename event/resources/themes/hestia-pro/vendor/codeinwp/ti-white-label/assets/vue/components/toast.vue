<template>
	<div class="toast-wrap">
		<div class="toast" :class="[toast.type, fadeOutClass]">
			<i class="dashicons toast-icon" :class="{
			'dashicons-yes' : toast.type === 'success',
			'dashicons-warning' : toast.type === 'error-toast'
			}"></i>
			<span class="message">{{toast.message}}</span>
			<span class="dismiss" @click="dismiss"><i class="dashicons dashicons-no"></i></span>
		</div>
	</div>
</template>

<script>
	export default {
		name: "toast",
		data() {
			return {
				fadeOutClass: ''
			}
		},
		computed: {
			toast() {
				return this.$store.state.toast;
			}
		},
		methods: {
			dismiss() {
				this.fadeOutClass = 'fade-out';
				setTimeout( () => {
					this.$store.commit( 'clearToast' );
				}, 600 );
			}
		},
		mounted() {
			setTimeout( () => {
				this.fadeOutClass = 'fade-out';
			}, 1700 );
			setTimeout( () => {
				this.$store.commit( 'clearToast' );
			}, 2100 );
		}
	}
</script>