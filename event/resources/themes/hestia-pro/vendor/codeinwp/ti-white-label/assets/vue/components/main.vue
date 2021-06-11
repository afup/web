<template>

	<section class="ti-wl-branding">
		<vue-form class="form-branding" :state="formstate" @submit.prevent="onSubmit">
			<div class="form-content">
				<div class="form-block postbox">
					<h2><span class="dashicons dashicons-store ti-wl-icon ti-agency-icon"></span><span>{{strings.block_form_title_agency}}</span></h2>
					<div class="inside">
						<validate class="field-wrap">
							<label for="agency-author">{{strings.agency_author_label}}:</label>
							<input v-model="model.agency_form.author_name" name="agency-author" id="agency-author"/>
						</validate>

						<validate class="field-wrap">
							<label for="agency-author-url">{{strings.agency_author_url_label}}:</label>
							<input v-model="model.agency_form.author_url" name="agency-author-url" type="url" id="agency-author-url"/>

							<field-messages name="agency-author-url">
								<div slot="url" class="notice invalid">{{strings.agency_author_url_label}} {{strings.not_valid}}</div>
							</field-messages>
						</validate>

						<validate class="field-wrap">
							<toggle-button name="enable-starter-sites" v-model="model.agency_form.starter_sites" :value="model.agency_form.starter_sites"
										   color="#0085ba"
										   :sync="true"/>

							<label class="toggle-label"> {{strings.agency_starter_sites_label}} </label>
						</validate>
					</div>
				</div>

				<div class="form-block postbox">
					<h2><span class="dashicons dashicons-admin-customizer ti-wl-icon ti-theme-icon"></span><span>{{strings.block_form_title_theme}}</span></h2>
					<div class="inside">
						<validate class="field-wrap">
							<label for="theme-name">{{strings.theme_name_label}}:</label>
							<input v-model="model.theme_form.theme_name" name="theme-name" id="theme-name"/>
						</validate>

						<validate class="field-wrap">
							<label for="theme-description">{{strings.theme_description_label}}:</label>
							<textarea  v-model="model.theme_form.theme_description" name="theme-description" id="theme-description">{{model.theme_form.theme_description}}</textarea>
						</validate>

						<validate class="field-wrap">
							<label for="screenshot-url">{{strings.screenshot_url_label}}:</label>
							<input v-model="model.theme_form.screenshot_url" name="screenshot-url" type="url" id="screenshot-url"/>

							<field-messages name="screenshot-url">
								<div slot="url" class="notice invalid">{{strings.screenshot_url_label}} {{strings.not_valid}}</div>
							</field-messages>
						</validate>
					</div>
				</div>

				<div v-if="model.plugin_form" class="form-block postbox">
					<h2><span class="dashicons dashicons-admin-plugins ti-wl-icon ti-plugin-icon"></span><span>{{strings.block_form_title_plugin}}</span></h2>
					<div class="inside">
						<validate class="field-wrap">
							<label for="plugin-name">{{strings.plugin_name_label}}:</label>
							<input v-model="model.plugin_form.plugin_name" name="plugin-name" id="plugin-name"/>
						</validate>

						<validate class="field-wrap">
							<label for="plugin-description">{{strings.plugin_description_label}}:</label>
							<textarea  v-model="model.plugin_form.plugin_description" name="plugin-description" id="plugin-description">{{model.plugin_form.plugin_description}}</textarea>
						</validate>
					</div>
				</div>
			</div>

			<div class="form-sidebar">
				<div class="form-block postbox">
					<h2> <span>{{strings.block_form_title_enable_white_label}}</span></h2>
					<div class="inside">
						<validate class="field-wrap">
							<toggle-button name="enable-white-label" v-model="model.white_label_form.white_label" :value="model.white_label_form.white_label"
									color="#0085ba"
									:sync="true"/>

							<label class="toggle-label"> {{strings.block_form_title_enable_white_label}} </label>
							<p v-if="model.white_label_form.white_label === true"> {{strings.white_label_description}}</p>
						</validate>
						<validate class="field-wrap">
							<toggle-button name="enable-license-hiding" v-model="model.white_label_form.license" :value="model.white_label_form.license"
										   color="#0085ba"
										   :sync="true"/>

							<label class="toggle-label"> {{strings.license_field_label}} </label>
							<p v-if="model.white_label_form.license === true"> {{strings.license_field_description}}</p>
						</validate>

						<button :disabled="this.formstate.$invalid" type="submit" class="button button-primary button-hero">{{strings.submit_button_label}}</button>
					</div>
				</div>
			</div>
		</vue-form>
		<toast v-if="this.$store.state.toast.message"></toast>

	</section>
</template>

<script>
	import Toast from './toast.vue';

	export default {
		name: "app",
		components: {
			Toast
		},
		data: function () {
			return {
				formstate: {},
				strings: this.$store.state.strings,
				model: this.$store.state.formsData
			}
		},
		methods: {
			onSubmit: function () {
				if(this.formstate.$invalid) {
					return;
				}
				this.updateState();
				this.updateFields();
			},
			updateState:function(){
				this.$store.commit( 'updateThemeFields', this.model );
			},
			updateFields: function () {
				this.$store.dispatch( 'updateFields', this.model );
			}
		}
	}
</script>

<style lang="scss">
	@import  "../../scss/style.scss";
</style>
