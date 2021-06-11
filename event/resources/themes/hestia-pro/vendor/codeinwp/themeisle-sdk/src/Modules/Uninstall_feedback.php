<?php
/**
 * The deactivate feedback model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Feedback
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */

namespace ThemeisleSDK\Modules;

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Product;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Uninstall feedback module for ThemeIsle SDK.
 */
class Uninstall_Feedback extends Abstract_Module {
	/**
	 * How many seconds before the deactivation window is triggered for themes?
	 *
	 * @var int Number of days.
	 */
	const AUTO_TRIGGER_DEACTIVATE_WINDOW_SECONDS = 3;
	/**
	 * How many days before the deactivation window pops up again for the theme?
	 *
	 * @var int Number of days.
	 */
	const PAUSE_DEACTIVATE_WINDOW_DAYS = 100;
	/**
	 * Where to send the data.
	 *
	 * @var string Endpoint url.
	 */
	const FEEDBACK_ENDPOINT = 'https://api.themeisle.com/tracking/uninstall';

	/**
	 * Default options for plugins.
	 *
	 * @var array $options_plugin The main options list for plugins.
	 */
	private $options_plugin = array(
		'I found a better plugin'            => array(
			'id'          => 3,
			'type'        => 'text',
			'placeholder' => 'What\'s the plugin\'s name?',
		),
		'I could not get the plugin to work' => array(
			'type'        => 'textarea',
			'placeholder' => 'What problem are you experiencing?',
			'id'          => 4,
		),
		'I no longer need the plugin'        => array(
			'id'          => 5,
			'type'        => 'textarea',
			'placeholder' => 'If you could improve one thing about our product, what would it be?',
		),
		'It\'s a temporary deactivation. I\'m just debugging an issue.' => array(
			'type'        => 'textarea',
			'placeholder' => 'What problem are you experiencing?',
			'id'          => 6,
		),
	);
	/**
	 * Default options for theme.
	 *
	 * @var array $options_theme The main options list for themes.
	 */
	private $options_theme = array(
		'I don\'t know how to make it look like demo' => array(
			'id' => 7,
		),
		'It lacks options'                            => array(
			'placeholder' => 'What option is missing?',
			'type'        => 'text',
			'id'          => 8,
		),
		'Is not working with a plugin that I need'    => array(
			'id'          => 9,
			'type'        => 'text',
			'placeholder' => 'What is the name of the plugin',
		),
		'I want to try a new design, I don\'t like {theme} style' => array(
			'id' => 10,
		),
	);
	/**
	 * Default other option.
	 *
	 * @var array $other The other option
	 */
	private $other = array(
		'Other' => array(
			'id'          => 999,
			'type'        => 'textarea',
			'placeholder' => 'What can we do better?',
		),
	);
	/**
	 * Default heading for plugin.
	 *
	 * @var string $heading_plugin The heading of the modal
	 */
	private $heading_plugin = 'What\'s wrong?';
	/**
	 * Default heading for theme.
	 *
	 * @var string $heading_theme The heading of the modal
	 */
	private $heading_theme = 'What does not work for you in {theme}?';
	/**
	 * Default submit button action text.
	 *
	 * @var string $button_submit The text of the deactivate button
	 */
	private $button_submit = 'Submit &amp; Deactivate';
	/**
	 * Default cancel button.
	 *
	 * @var string $button_cancel The text of the cancel button
	 */
	private $button_cancel = 'Skip &amp; Deactivate';

	/**
	 * Loads the additional resources
	 */
	function load_resources() {
		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, array( 'theme-install', 'plugins' ) ) ) {
			return;
		}

		$this->add_feedback_popup_style();

		if ( $this->product->get_type() === 'theme' ) {
			$this->add_theme_feedback_drawer_js();
			$this->render_theme_feedback_popup();

			return;
		}
		$this->add_plugin_feedback_popup_js();
		$this->render_plugin_feedback_popup();
	}

	/**
	 * Render theme feedback drawer.
	 */
	private function render_theme_feedback_popup() {
		$heading              = str_replace( '{theme}', $this->product->get_name(), $this->heading_theme );
		$button_submit        = apply_filters( $this->product->get_key() . '_feedback_deactivate_button_submit', 'Submit' );
		$options              = $this->options_theme;
		$options              = $this->randomize_options( apply_filters( $this->product->get_key() . '_feedback_deactivate_options', $options ) );
		$info_disclosure_link = '<a href="#" class="info-disclosure-link">' . apply_filters( $this->product->get_slug() . '_themeisle_sdk_info_collect_cta', 'What info do we collect?' ) . '</a>';

		$options += $this->other;

		?>
		<div class="ti-theme-uninstall-feedback-drawer ti-feedback">
			<div class="popup--header">
				<h5><?php echo wp_kses( $heading, array( 'span' => true ) ); ?> </h5>
				<button class="toggle"><span>&times;</span></button>
			</div><!--/.popup--header-->
			<div class="popup--body">
				<?php $this->render_options_list( $options ); ?>
			</div><!--/.popup--body-->
			<div class="popup--footer">
				<div class="actions">
					<?php
					echo wp_kses_post( $info_disclosure_link );
					echo wp_kses_post( $this->get_disclosure_labels() );
					echo '<div class="buttons">';
					echo get_submit_button(
						$button_submit,
						'secondary',
						$this->product->get_key() . 'ti-deactivate-yes',
						false,
						array(
							'data-after-text' => $button_submit,
							'disabled'        => true,
						)
					);
					echo '</div>';
					?>
				</div><!--/.actions-->
			</div><!--/.popup--footer-->
		</div>
		<?php
	}

	/**
	 * Add feedback styles.
	 */
	private function add_feedback_popup_style() {
		?>
		<style>
			.ti-feedback {
				background: #fff;
				max-width: 400px;
				z-index: 10000;
				box-shadow: 0 0 15px -5px rgba(0, 0, 0, .5);
				transition: all .3s ease-out;
			}


			.ti-feedback .popup--header {
				position: relative;
				background-color: #23A1CE;
			}

			.ti-feedback .popup--header h5 {
				margin: 0;
				font-size: 16px;
				padding: 15px;
				color: #fff;
				font-weight: 600;
				text-align: center;
				letter-spacing: .3px;
			}

			.ti-feedback .popup--body {
				padding: 15px;
			}

			.ti-feedback .popup--form {
				margin: 0;
				font-size: 13px;
			}

			.ti-feedback .popup--form input[type="radio"] {
				<?php echo is_rtl() ? 'margin: 0 0 0 10px;' : 'margin: 0 10px 0 0;'; ?>
			}

			.ti-feedback .popup--form input[type="radio"]:checked ~ textarea {
				display: block;
			}

			.ti-feedback .popup--form textarea {
				width: 100%;
				margin: 10px 0 0;
				display: none;
				max-height: 150px;
			}

			.ti-feedback li {
				display: flex;
				align-items: center;
				margin-bottom: 15px;
				flex-wrap: wrap;
			}

			.ti-feedback li label {
				max-width: 90%;
			}

			.ti-feedback li:last-child {
				margin-bottom: 0;
			}

			.ti-feedback .popup--footer {
				padding: 0 15px 15px;
			}

			.ti-feedback .actions {
				display: flex;
				flex-wrap: wrap;
			}

			.info-disclosure-link {
				width: 100%;
				margin-bottom: 15px;
			}

			.ti-feedback .info-disclosure-content {
				max-height: 0;
				overflow: hidden;
				width: 100%;
				transition: .3s ease;
			}

			.ti-feedback .info-disclosure-content.active {
				max-height: 300px;
			}

			.ti-feedback .info-disclosure-content p {
				margin: 0;
			}

			.ti-feedback .info-disclosure-content ul {
				margin: 10px 0;
				border-radius: 3px;
			}

			.ti-feedback .info-disclosure-content ul li {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 0;
				padding: 5px 0;
				border-bottom: 1px solid #ccc;
			}

			.ti-feedback .buttons {
				display: flex;
				width: 100%;
			}

			.ti-feedback .buttons input:last-child {
				<?php echo is_rtl() ? 'margin-right: auto;' : 'margin-left: auto;'; ?>
			}

			.ti-theme-uninstall-feedback-drawer {
				border-top-left-radius: 5px;
				position: fixed;
				top: 100%;
				right: 15px;
			}

			.ti-theme-uninstall-feedback-drawer.active {
				transform: translateY(-100%);
			}

			.ti-theme-uninstall-feedback-drawer .popup--header {
				border-top-left-radius: 5px;
			}

			.ti-theme-uninstall-feedback-drawer .popup--header .toggle {
				position: absolute;
				padding: 3px 0;
				width: 30px;
				top: -26px;
				right: 0;
				cursor: pointer;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				font-size: 20px;
				background-color: #23A1CE;
				color: #fff;
				border: none;
				line-height: 20px;
			}

			.ti-theme-uninstall-feedback-drawer .toggle span {
				margin: 0;
				display: inline-block;
			}

			.ti-theme-uninstall-feedback-drawer:not(.active) .toggle span {
				transform: rotate(45deg);
			}

			.ti-theme-uninstall-feedback-drawer .popup--header .toggle:hover {
				background-color: #1880a5;
			}


			.ti-plugin-uninstall-feedback-popup .popup--header:before {
				content: "";
				display: block;
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				<?php
				echo is_rtl() ?
				'right: -10px;
				border-top: 20px solid transparent;
				border-left: 20px solid #23A1CE;
				border-bottom: 20px solid transparent;' :
				'left: -10px;
				border-top: 20px solid transparent;
				border-right: 20px solid #23A1CE;
				border-bottom: 20px solid transparent;';
				?>
			}

			.ti-plugin-uninstall-feedback-popup {
				display: none;
				position: absolute;
				white-space: normal;
				width: 400px;
				<?php echo is_rtl() ? 'right: calc( 100% + 15px );' : 'left: calc( 100% + 15px );'; ?>
				top: -15px;
			}

			.ti-plugin-uninstall-feedback-popup.sending-feedback .popup--body i {
				animation: rotation 2s infinite linear;
				display: block;
				float: none;
				align-items: center;
				width: 100%;
				margin: 0 auto;
				height: 100%;
				background: transparent;
				padding: 0;
			}

			.ti-plugin-uninstall-feedback-popup.sending-feedback .popup--body i:before {
				padding: 0;
				background: transparent;
				box-shadow: none;
				color: #b4b9be
			}


			.ti-plugin-uninstall-feedback-popup.active {
				display: block;
			}

			tr[data-plugin^="<?php echo $this->product->get_slug(); ?>"] .deactivate {
				position: relative;
			}

			body.ti-feedback-open .ti-feedback-overlay {
				content: "";
				display: block;
				background-color: rgba(0, 0, 0, 0.5);
				top: 0;
				bottom: 0;
				right: 0;
				left: 0;
				z-index: 10000;
				position: fixed;
			}

			@media (max-width: 768px) {
				.ti-plugin-uninstall-feedback-popup {
					position: fixed;
					max-width: 100%;
					margin: 0 auto;
					left: 50%;
					top: 50px;
					transform: translateX(-50%);
				}

				.ti-plugin-uninstall-feedback-popup .popup--header:before {
					display: none;
				}
			}
		</style>
		<?php
	}

	/**
	 * Theme feedback drawer JS.
	 */
	private function add_theme_feedback_drawer_js() {
		$key = $this->product->get_key();
		?>
		<script type="text/javascript" id="ti-deactivate-js">
			(function ($) {
				$(document).ready(function () {
					setTimeout(function () {
						$('.ti-theme-uninstall-feedback-drawer').addClass('active');
					}, <?php echo absint( self::AUTO_TRIGGER_DEACTIVATE_WINDOW_SECONDS * 1000 ); ?> );

					$('.ti-theme-uninstall-feedback-drawer .toggle').on('click', function (e) {
						e.preventDefault();
						$('.ti-theme-uninstall-feedback-drawer').toggleClass('active');
					});

					$('.info-disclosure-link').on('click', function (e) {
						e.preventDefault();
						$('.info-disclosure-content').toggleClass('active');
					});

					$('.ti-theme-uninstall-feedback-drawer input[type="radio"]').on('change', function () {
						var radio = $(this);
						if (radio.parent().find('textarea').length > 0 &&
							radio.parent().find('textarea').val().length === 0) {
							$('#<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
							radio.parent().find('textarea').on('keyup', function (e) {
								if ($(this).val().length === 0) {
									$('#<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
								} else {
									$('#<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
								}
							});
						} else {
							$('#<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
						}
					});

					$('#<?php echo $key; ?>ti-deactivate-yes').on('click', function (e) {
						e.preventDefault();
						e.stopPropagation();

						var selectedOption = $(
							'.ti-theme-uninstall-feedback-drawer input[name="ti-deactivate-option"]:checked');
						$.post(ajaxurl, {
							'action': '<?php echo esc_attr( $key ) . '_uninstall_feedback'; ?>',
							'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
							'id': selectedOption.parent().attr('ti-option-id'),
							'msg': selectedOption.parent().find('textarea').val(),
							'type': 'theme',
							'key': '<?php echo esc_attr( $key ); ?>'
						});
						$('.ti-theme-uninstall-feedback-drawer').fadeOut();
					});
				});
			})(jQuery);

		</script>
		<?php
		do_action( $this->product->get_key() . '_uninstall_feedback_after_js' );
	}

	/**
	 * Render the options list.
	 *
	 * @param array $options the options for the feedback form.
	 */
	private function render_options_list( $options ) {
		$key            = $this->product->get_key();
		$inputs_row_map = [
			'text'     => 1,
			'textarea' => 2,
		];
		?>
		<ul class="popup--form">
			<?php foreach ( $options as $title => $attributes ) { ?>
				<li ti-option-id="<?php echo esc_attr( $attributes['id'] ); ?>">
					<input type="radio" name="ti-deactivate-option" id="<?php echo esc_attr( $key . $attributes['id'] ); ?>">
					<label for="<?php echo esc_attr( $key . $attributes['id'] ); ?>">
						<?php echo str_replace( '{theme}', $this->product->get_name(), $title ); ?>
					</label>
					<?php
					if ( array_key_exists( 'type', $attributes ) ) {
						$placeholder = array_key_exists( 'placeholder', $attributes ) ? $attributes['placeholder'] : '';
						echo '<textarea width="100%" rows="' . $inputs_row_map[ $attributes['type'] ] . '" name="comments" placeholder="' . esc_attr( $placeholder ) . '"></textarea>';
					}
					?>
				</li>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Render plugin feedback popup.
	 */
	private function render_plugin_feedback_popup() {
		$button_cancel        = apply_filters( $this->product->get_key() . '_feedback_deactivate_button_cancel', $this->button_cancel );
		$button_submit        = apply_filters( $this->product->get_key() . '_feedback_deactivate_button_submit', $this->button_submit );
		$options              = $this->randomize_options( apply_filters( $this->product->get_key() . '_feedback_deactivate_options', $this->options_plugin ) );
		$info_disclosure_link = '<a href="#" class="info-disclosure-link">' . apply_filters( $this->product->get_slug() . '_themeisle_sdk_info_collect_cta', 'What info do we collect?' ) . '</a>';

		$options += $this->other;
		?>
		<div class="ti-plugin-uninstall-feedback-popup ti-feedback" id="<?php echo esc_attr( $this->product->get_slug() . '_uninstall_feedback_popup' ); ?>">
			<div class="popup--header">
				<h5><?php echo wp_kses( $this->heading_plugin, array( 'span' => true ) ); ?> </h5>
			</div><!--/.popup--header-->
			<div class="popup--body">
				<?php $this->render_options_list( $options ); ?>
			</div><!--/.popup--body-->
			<div class="popup--footer">
				<div class="actions">
					<?php
					echo wp_kses_post( $info_disclosure_link );
					echo wp_kses_post( $this->get_disclosure_labels() );
					echo '<div class="buttons">';
					echo get_submit_button(
						$button_cancel,
						'secondary',
						$this->product->get_key() . 'ti-deactivate-no',
						false
					);
					echo get_submit_button(
						$button_submit,
						'primary',
						$this->product->get_key() . 'ti-deactivate-yes',
						false,
						array(
							'data-after-text' => $button_submit,
							'disabled'        => true,
						)
					);
					echo '</div>';
					?>
				</div><!--/.actions-->
			</div><!--/.popup--footer-->
		</div>

		<?php
	}

	/**
	 * Add plugin feedback popup JS
	 */
	private function add_plugin_feedback_popup_js() {
		$popup_id = '#' . $this->product->get_slug() . '_uninstall_feedback_popup';
		$key      = $this->product->get_key();
		?>
		<script type="text/javascript" id="ti-deactivate-js">
			(function ($) {
				$(document).ready(function () {
					var targetElement = 'tr[data-plugin^="<?php echo $this->product->get_slug(); ?>/"] span.deactivate a';
					var redirectUrl = $(targetElement).attr('href');
					if ($('.ti-feedback-overlay').length === 0) {
						$('body').prepend('<div class="ti-feedback-overlay"></div>');
					}
					$('<?php echo esc_attr( $popup_id ); ?> ').appendTo($(targetElement).parent());

					$(targetElement).on('click', function (e) {
						e.preventDefault();
						$('<?php echo esc_attr( $popup_id ); ?> ').addClass('active');
						$('body').addClass('ti-feedback-open');
						$('.ti-feedback-overlay').on('click', function () {
							$('<?php echo esc_attr( $popup_id ); ?> ').removeClass('active');
							$('body').removeClass('ti-feedback-open');
						});
					});

					$('<?php echo esc_attr( $popup_id ); ?> .info-disclosure-link').on('click', function (e) {
						e.preventDefault();
						$(this).parent().find('.info-disclosure-content').toggleClass('active');
					});

					$('<?php echo esc_attr( $popup_id ); ?> input[type="radio"]').on('change', function () {
						var radio = $(this);
						if (radio.parent().find('textarea').length > 0 &&
							radio.parent().find('textarea').val().length === 0) {
							$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
							radio.parent().find('textarea').on('keyup', function (e) {
								if ($(this).val().length === 0) {
									$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-yes').attr('disabled', 'disabled');
								} else {
									$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
								}
							});
						} else {
							$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-yes').removeAttr('disabled');
						}
					});

					$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-no').on('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						$(targetElement).unbind('click');
						$('body').removeClass('ti-feedback-open');
						$('<?php echo esc_attr( $popup_id ); ?>').remove();
						if (redirectUrl !== '') {
							location.href = redirectUrl;
						}
					});

					$('<?php echo esc_attr( $popup_id ); ?> #<?php echo $key; ?>ti-deactivate-yes').on('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						$(targetElement).unbind('click');
						var selectedOption = $(
							'<?php echo esc_attr( $popup_id ); ?> input[name="ti-deactivate-option"]:checked');
						var data = {
							'action': '<?php echo esc_attr( $key ) . '_uninstall_feedback'; ?>',
							'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
							'id': selectedOption.parent().attr('ti-option-id'),
							'msg': selectedOption.parent().find('textarea').val(),
							'type': 'plugin',
							'key': '<?php echo esc_attr( $key ); ?>'
						};
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: data,
							complete() {
								$('body').removeClass('ti-feedback-open');
								$('<?php echo esc_attr( $popup_id ); ?>').remove();
								if (redirectUrl !== '') {
									location.href = redirectUrl;
								}
							},
							beforeSend() {
								$('<?php echo esc_attr( $popup_id ); ?>').addClass('sending-feedback');
								$('<?php echo esc_attr( $popup_id ); ?> .popup--footer').remove();
								$('<?php echo esc_attr( $popup_id ); ?> .popup--body').html('<i class="dashicons dashicons-update-alt"></i>');
							}
						});
					});
				});
			})(jQuery);

		</script>
		<?php
		do_action( $this->product->get_key() . '_uninstall_feedback_after_js' );
	}

	/**
	 * Get the disclosure labels markup.
	 *
	 * @return string
	 */
	private function get_disclosure_labels() {
		$disclosure_new_labels = apply_filters( $this->product->get_slug() . '_themeisle_sdk_disclosure_content_labels', [], $this->product );
		$disclosure_labels     = array_merge(
			[
				'title' => 'Below is a detailed view of all data that ThemeIsle will receive if you fill in this survey. No domain name, email address or IP addresses are transmited after you submit the survey.',
				'items' => [
					sprintf( '%s %s version %s %s %s %s', '<strong>', ucwords( $this->product->get_type() ), '</strong>', '<code>', $this->product->get_version(), '</code>' ),
					sprintf( '%sCurrent website:%s %s %s %s', '<strong>', '</strong>', '<code>', get_site_url(), '</code>' ),
					sprintf( '%s Uninstall reason %s %s Selected reason from the above survey %s ', '<strong>', '</strong>', '<i>', '</i>' ),
				],
			],
			$disclosure_new_labels
		);

		$info_disclosure_content = '<div class="info-disclosure-content"><p>' . wp_kses_post( $disclosure_labels['title'] ) . '</p><ul>';
		foreach ( $disclosure_labels['items'] as $disclosure_item ) {
			$info_disclosure_content .= sprintf( '<li>%s</li>', wp_kses_post( $disclosure_item ) );
		}
		$info_disclosure_content .= '</ul></div>';

		return $info_disclosure_content;
	}

	/**
	 * Randomizes the options array.
	 *
	 * @param array $options The options array.
	 */
	function randomize_options( $options ) {
		$new  = array();
		$keys = array_keys( $options );
		shuffle( $keys );

		foreach ( $keys as $key ) {
			$new[ $key ] = $options[ $key ];
		}

		return $new;
	}

	/**
	 * Called when the deactivate button is clicked.
	 */
	function post_deactivate() {
		check_ajax_referer( (string) __CLASS__, 'nonce' );

		$this->post_deactivate_or_cancel();

		if ( empty( $_POST['id'] ) ) {

			wp_send_json( [] );

			return;
		}
		$this->call_api(
			array(
				'type'    => 'deactivate',
				'id'      => $_POST['id'],
				'comment' => isset( $_POST['msg'] ) ? $_POST['msg'] : '',
			)
		);
		wp_send_json( [] );

	}

	/**
	 * Called when the deactivate/cancel button is clicked.
	 */
	private function post_deactivate_or_cancel() {
		if ( ! isset( $_POST['type'] ) || ! isset( $_POST['key'] ) ) {
			return;
		}
		if ( 'theme' !== $_POST['type'] ) {
			return;
		}

		set_transient( 'ti_sdk_pause_' . $_POST['key'], true, self::PAUSE_DEACTIVATE_WINDOW_DAYS * DAY_IN_SECONDS );

	}

	/**
	 * Calls the API
	 *
	 * @param array $attributes The attributes of the post body.
	 *
	 * @return bool Is the request succesfull?
	 */
	protected function call_api( $attributes ) {
		$slug                  = $this->product->get_slug();
		$version               = $this->product->get_version();
		$attributes['slug']    = $slug;
		$attributes['version'] = $version;
		$attributes['url']     = get_site_url();

		$response = wp_remote_post(
			self::FEEDBACK_ENDPOINT,
			array(
				'body' => $attributes,
			)
		);

		return is_wp_error( $response );
	}

	/**
	 * Should we load this object?.
	 *
	 * @param Product $product Product object.
	 *
	 * @return bool Should we load the module?
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}
		if ( $product->is_theme() && ( false !== get_transient( 'ti_sdk_pause_' . $product->get_key(), false ) ) ) {
			return false;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}
		global $pagenow;

		if ( ! isset( $pagenow ) || empty( $pagenow ) ) {
			return false;
		}

		if ( $product->is_plugin() && 'plugins.php' !== $pagenow ) {
			return false;

		}
		if ( $product->is_theme() && 'theme-install.php' !== $pagenow ) {
			return false;
		}

		return true;
	}

	/**
	 * Loads module hooks.
	 *
	 * @param Product $product Product details.
	 *
	 * @return Uninstall_Feedback Current module instance.
	 */
	public function load( $product ) {

		if ( apply_filters( $product->get_key() . '_hide_uninstall_feedback', false ) ) {
			return;
		}

		$this->product = $product;
		add_action( 'admin_head', array( $this, 'load_resources' ) );
		add_action( 'wp_ajax_' . $this->product->get_key() . '_uninstall_feedback', array( $this, 'post_deactivate' ) );

		return $this;
	}
}
