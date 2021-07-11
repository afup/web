<template>
	<div class="import-modal__wrapper">
		<div class="modal__item" v-on-clickaway="closeModal">
			<div class="modal__header">
				<div class="background" :style="{ backgroundImage: 'url(' + siteData.screenshot + ')' }"></div>
				<button v-if="!importing || currentStep === 'error'" type="button" class="close" @click="closeModal">×
				</button>
				<h2 class="title ellipsis">{{isMigration? siteData.theme_name : siteData.title}}</h2>
			</div>
			<div class="modal__content">
				<template v-if="currentStep !== 'done' && currentStep !== 'error' && !importing ">
					<div class="disclaimers" :class="{ 'warning' : siteData.external_plugins}">
						<strong>
							<i class="dashicons dashicons-info"></i>
							{{ siteData.external_plugins ? strings.external_plugins_notice : strings.note + ':'}}
						</strong>
						<ol v-if="siteData.external_plugins">
							<li v-for="(url, plugin) in siteData.external_plugins">
								<a :href="url" target="_blank">{{plugin}}</a>
							</li>
						</ol>
						<ol v-else>
							<li>{{strings.backup_disclaimer}}</li>
							<li>{{strings.placeholders_disclaimer_new}}</li>
							<li v-if="this.siteData.images_gallery"><a :href="this.siteData.images_gallery"
									target="_blank">{{strings.images_gallery_link}}</a></li>
						</ol>
					</div>
					<div class="import__options" :class="{ 'disabled' : siteData.external_plugins}">
						<h4>{{strings.general}}:</h4>
						<ul class="features">
							<li class="option_toggle">
								<label class="option-toggle-label"
										:class="importOptions.content ? 'active' : 'inactive'"><span
										class="dashicons dashicons-admin-post"></span><span>{{strings.content}}</span></label>
								<toggle-button v-if="! isMigration" @change="adjustImport( 'content' )"
										:value="importOptions.content"
										color="#008ec2"></toggle-button>
							</li>
							<li class="option_toggle">
								<label class="option-toggle-label"
										:class="importOptions.customizer ? 'active' : 'inactive'"><span
										class="dashicons dashicons-admin-customizer"></span><span>{{strings.customizer}}</span></label>
								<toggle-button v-if="! isMigration" @change="adjustImport( 'customizer' )"
										:value="importOptions.customizer"
										color="#008ec2"></toggle-button>
							</li>
							<li class="option_toggle">
								<label class="option-toggle-label"
										:class="importOptions.widgets ? 'active' : 'inactive'"><span
										class="dashicons dashicons-admin-generic"></span><span>{{strings.widgets}}</span></label>
								<toggle-button v-if="! isMigration" @change="adjustImport( 'widgets' )"
										:value="importOptions.widgets"
										color="#008ec2"></toggle-button>
							</li>
						</ul>
						<h4>{{strings.plugins}}:</h4>
						<ul class="features">
							<template class="option_toggle" v-for="( plugins, pluginType ) in allPlugins">
								<li class="option_toggle" v-for="( plugin, index ) in plugins">
									<label class="option-toggle-label ellipsis"
											:class="importOptions.installablePlugins[index] ?  'active' : 'inactive'">
										<span class="dashicons dashicons-admin-plugins"></span>
										<span v-html="plugin"></span></label>
									<toggle-button v-if="pluginType !== 'mandatory'"
											@change="adjustPlugins( index, plugin )"
											:value="importOptions.installablePlugins[index]"
											color="#008ec2"></toggle-button>
								</li>
							</template>
						</ul>
					</div>
				</template>
				<template v-else>
					<stepper></stepper>
					<h3 class="success" v-if="currentStep === 'done'">
						<span class="dashicons dashicons-heart"></span>
						<span>{{strings.import_done}}</span>
					</h3>
					<error-well v-if="errorMessage"></error-well>
				</template>
			</div>
			<div class="modal__footer" v-if="! importing">
				<template v-if="currentStep !== 'done' && currentStep !== 'error'">
					<button class="button button-secondary" v-on:click="closeModal">{{strings.cancel_btn}}</button>
					<button class="button button-primary" :disabled="! checIfShouldImport || siteData.external_plugins"
							v-on:click="startImport">
						{{strings.import_btn}}
					</button>
				</template>
				<template v-else-if="currentStep === 'error'">
					<button class="button button-secondary" v-on:click="resetImport">{{strings.back}}</button>
				</template>
				<template v-else>
					<button class="button button-link" v-if="this.$store.state.onboard !== 'yes'"
							v-on:click="resetImport">{{strings.back}}
					</button>
					<a :href="this.homeUrl" class="button button-secondary">{{strings.go_to_site}}</a>
					<a :href="editTemplateLink()" class="button button-primary">{{strings.edit_template}}</a>
				</template>
			</div>
		</div>
	</div>
</template>

<script>
	import { directive as onClickaway } from 'vue-clickaway';
	import Stepper from './stepper.vue';
	import Loader from './loader.vue';
	import Tabs from './tabs.vue';
	import ErrorWell from './error-well.vue';

	export default {
		name: 'import-modal',
		data: function() {
			return {
				strings: this.$store.state.strings,
				homeUrl: this.$store.state.homeUrl,
				siteData: this.$store.state.previewData,
				advancedExpanded: false
			};
		},
		computed: {
			defaultTemplate: function() {
				return this.$store.state.sitesData;
			},
			allPlugins() {
				return {
					recommended: this.siteData.recommended_plugins,
					mandatory: this.siteData.mandatory_plugins
				};
			},
			currentStep() {
				return this.$store.state.currentStep;
			},
			importing() {
				return this.$store.state.importing;
			},
			checIfShouldImport() {
				if (
						this.$store.state.importOptions.content ||
						this.$store.state.importOptions.customizer ||
						this.$store.state.importOptions.widgets
				) {
					return true;
				}
				return false;
			},
			importOptions() {
				return this.$store.state.importOptions;
			},
			errorMessage() {
				return this.$store.state.errorToast;
			},
			isMigration() {
				return this.$store.state.importOptions.isMigration;
			}
		},
		methods: {
			toggleAdvanced() {
				this.advancedExpanded = !this.advancedExpanded;
			},
			adjustPlugins: function(index, plugin) {
				let plugins = this.$store.state.importOptions.installablePlugins;
				plugins[index] = !plugins[index];
				this.$store.commit( 'updatePlugins', plugins );
			},
			adjustImport: function(context) {
				let options = this.$store.state.importOptions;
				options[context] = !options[context];
				this.$store.commit( 'updateImportOptions', options );
			},
			getEditor: function() {
				return this.$store.state.editor;
			},
			getPageId: function() {
				return this.$store.state.frontPageId;
			},
			closeModal: function() {
				if ( this.importing ) {
					return false;
				}
				this.$store.commit( 'showImportModal', false );
			},
			runMigration: function() {
				this.$store.state.importOptions.isMigration = true;
				this.$store.state.migration = 'isRunning';
				this.$store.dispatch( 'importSite', {
					template: this.siteData.template,
					template_name: this.siteData.template_name
				} );
			},
			startImport: function() {
				if ( this.isMigration ) {
					this.runMigration();
					return false;
				}
				this.$store.dispatch( 'importSite', {
					plugins: this.siteData.recommended_plugins,
					content: {
						'content_file': this.siteData.content_file,
						'front_page': this.siteData.front_page,
						'shop_pages': this.siteData.shop_pages
					},
					themeMods: {
						'theme_mods': this.siteData.theme_mods,
						'source_url': this.siteData.demo_url,
						'wp_options': this.siteData.wp_options
					},
					widgets: this.siteData.widgets,
					source: this.siteData.source
				} );
			},
			redirectToHome: function() {
				window.location.replace( this.homeUrl );
			},
			resetImport: function() {
				this.$store.commit( 'resetStates' );
			},
			editTemplateLink: function() {
				let editor = this.getEditor(),
						pageId = this.getPageId(),
						customizerRedirect = this.siteData.edit_content_redirect,
						url = this.homeUrl;
				if ( editor === 'elementor' || this.isMigration ) {
					url = this.homeUrl + '/wp-admin/post.php?post=' + pageId + '&action=elementor';
				}
				if ( editor === 'gutenberg' ) {
					url = this.homeUrl + '/wp-admin/post.php?post=' + pageId + '&action=edit';
				}
				if ( editor === 'brizy' ) {
					url = this.homeUrl + '/?brizy-edit';
				}
				if ( editor === 'beaver-builder' ) {
					url = this.homeUrl + '/?fl_builder';
				}
				if ( editor === 'thrive-architect' ) {
					url = this.homeUrl + '/wp-admin/post.php?post=' + pageId + '&action=architect&tve=true';
				}
				if ( editor === 'divi-builder' ) {
					url = this.homeUrl + '/?et_fb=1&PageSpeed=off';
				}
				if ( customizerRedirect === 'customizer' ) {
					url = this.homeUrl + '/wp-admin/customize.php';
				}
				return url;
			}
		},
		beforeMount() {
			let body = document.querySelectorAll( '#ti-sites-library > div' )[0];
			body.style.overflow = 'hidden';
		},
		beforeDestroy() {
			let body = document.querySelectorAll( '#ti-sites-library > div' )[0];
			body.style.overflow = '';
		},
		directives: {
			onClickaway
		},
		components: {
			Stepper,
			Loader,
			Tabs,
			ErrorWell
		}
	};
</script>
