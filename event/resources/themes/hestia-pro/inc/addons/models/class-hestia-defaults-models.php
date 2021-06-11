<?php
/**
 * This file stores all functions that return default content.
 *
 * @package hestia
 */

/**
 * Class Hestia_Defaults_Models
 *
 * @package hestia
 */
class Hestia_Defaults_Models {
	/**
	 * Call this method to get singleton
	 */
	public static function instance() {
		static $instance = false;
		if ( $instance === false ) {
			// Late static binding (PHP 5.3+)
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Make constructor private, so nobody can call "new Class".
	 */
	private function __construct() {}

	/**
	 * Prevent cloneing the instance.
	 */
	public function __clone() {
		wp_die( 'Cloning is forbidden.' );
	}

	/**
	 * Prevent Serialize instance.
	 */
	public function __sleep() {
		wp_die( 'Serialize instances of this class is forbidden.' );
	}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		wp_die( 'Unserializing instances of this class is forbidden.' );
	}

	/**
	 * Get default values for features section.
	 *
	 * @since 1.1.31
	 * @access public
	 */
	public function get_features_default() {
		return apply_filters(
			'hestia_features_default_content',
			json_encode(
				array(
					array(
						'icon_value' => 'fab fa-weixin',
						'title'      => esc_html__( 'Responsive', 'hestia-pro' ),
						'text'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
						'link'       => '#',
						'color'      => '#e91e63',
					),
					array(
						'icon_value' => 'fas fa-check',
						'title'      => esc_html__( 'Quality', 'hestia-pro' ),
						'text'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
						'link'       => '#',
						'color'      => '#00bcd4',
					),
					array(
						'icon_value' => 'far fa-life-ring',
						'title'      => esc_html__( 'Support', 'hestia-pro' ),
						'text'       => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
						'link'       => '#',
						'color'      => '#4caf50',
					),
				)
			)
		);
	}

	/**
	 * Get default values for features section.
	 *
	 * @since 1.1.31
	 * @access public
	 */
	public function get_team_default() {
		return apply_filters(
			'hestia_team_default_content',
			json_encode(
				array(
					array(
						'image_url'       => get_template_directory_uri() . '/assets/img/1.jpg',
						'title'           => esc_html__( 'Desmond Purpleson', 'hestia-pro' ),
						'subtitle'        => esc_html__( 'CEO', 'hestia-pro' ),
						'text'            => esc_html__( 'Locavore pinterest chambray affogato art party, forage coloring book typewriter. Bitters cold selfies, retro celiac sartorial mustache.', 'hestia-pro' ),
						'id'              => 'customizer_repeater_56d7ea7f40c56',
						'social_repeater' => json_encode(
							array(
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb908674e06',
									'link' => 'facebook.com',
									'icon' => 'fab fa-facebook-f',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9148530ft',
									'link' => 'plus.google.com',
									'icon' => 'fab fa-google-plus-g',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9148530fc',
									'link' => 'twitter.com',
									'icon' => 'fab fa-twitter',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9150e1e89',
									'link' => 'linkedin.com',
									'icon' => 'fab fa-linkedin-in',
								),
							)
						),
					),
					array(
						'image_url'       => get_template_directory_uri() . '/assets/img/2.jpg',
						'title'           => esc_html__( 'Parsley Pepperspray', 'hestia-pro' ),
						'subtitle'        => esc_html__( 'Marketing Specialist', 'hestia-pro' ),
						'text'            => esc_html__( 'Craft beer salvia celiac mlkshk. Pinterest celiac tumblr, portland salvia skateboard cliche thundercats. Tattooed chia austin hell.', 'hestia-pro' ),
						'id'              => 'customizer_repeater_56d7ea7f40c66',
						'social_repeater' => json_encode(
							array(
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9155a1072',
									'link' => 'facebook.com',
									'icon' => 'fab fa-facebook-f',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9160ab683',
									'link' => 'twitter.com',
									'icon' => 'fab fa-google-plus-g',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9160ab484',
									'link' => 'pinterest.com',
									'icon' => 'fab fa-twitter',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb916ddffc9',
									'link' => 'linkedin.com',
									'icon' => 'fab fa-linkedin-in',
								),
							)
						),
					),
					array(
						'image_url'       => get_template_directory_uri() . '/assets/img/3.jpg',
						'title'           => esc_html__( 'Desmond Eagle', 'hestia-pro' ),
						'subtitle'        => esc_html__( 'Graphic Designer', 'hestia-pro' ),
						'text'            => esc_html__( 'Pok pok direct trade godard street art, poutine fam typewriter food truck narwhal kombucha wolf cardigan butcher whatever pickled you.', 'hestia-pro' ),
						'id'              => 'customizer_repeater_56d7ea7f40c76',
						'social_repeater' => json_encode(
							array(
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb917e4c69e',
									'link' => 'facebook.com',
									'icon' => 'fab fa-facebook-f',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb91830825c',
									'link' => 'twitter.com',
									'icon' => 'fab fa-google-plus-g',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb918d65f2e',
									'link' => 'linkedin.com',
									'icon' => 'fab fa-twitter',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb918d65f2x',
									'link' => 'dribbble.com',
									'icon' => 'fab fa-linkedin-in',
								),
							)
						),
					),
					array(
						'image_url'       => get_template_directory_uri() . '/assets/img/4.jpg',
						'title'           => esc_html__( 'Ruby Von Rails', 'hestia-pro' ),
						'subtitle'        => esc_html__( 'Lead Developer', 'hestia-pro' ),
						'text'            => esc_html__( 'Small batch vexillologist 90\'s blue bottle stumptown bespoke. Pok pok tilde fixie chartreuse, VHS gluten-free selfies wolf hot.', 'hestia-pro' ),
						'id'              => 'customizer_repeater_56d7ea7f40c86',
						'social_repeater' => json_encode(
							array(
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb925cedcg5',
									'link' => 'github.com',
									'icon' => 'fab fa-facebook-f',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb925cedcb2',
									'link' => 'facebook.com',
									'icon' => 'fab fa-google-plus-g',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb92615f030',
									'link' => 'twitter.com',
									'icon' => 'fab fa-twitter',
								),
								array(
									'id'   => 'customizer-repeater-social-repeater-57fb9266c223a',
									'link' => 'linkedin.com',
									'icon' => 'fab fa-linkedin-in',
								),
							)
						),
					),
				)
			)
		);
	}

	/**
	 * Import lite content to slider
	 *
	 * @return array
	 */
	public function get_slider_default() {
		$default = array(
			array(
				'image_url' => get_template_directory_uri() . '/assets/img/slider1.jpg',
				'title'     => esc_html__( 'Lorem Ipsum', 'hestia-pro' ),
				'subtitle'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
				'text'      => esc_html__( 'Button', 'hestia-pro' ),
				'link'      => '#',
				'id'        => 'customizer_repeater_56d7ea7f40a56',
				'color'     => '#e91e63',
			),
			array(
				'image_url' => get_template_directory_uri() . '/assets/img/slider2.jpg',
				'title'     => esc_html__( 'Lorem Ipsum', 'hestia-pro' ),
				'subtitle'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
				'text'      => esc_html__( 'Button', 'hestia-pro' ),
				'link'      => '#',
				'id'        => 'customizer_repeater_56d7ea7f40a57',
				'color'     => '#e91e63',
			),
			array(
				'image_url' => get_template_directory_uri() . '/assets/img/slider3.jpg',
				'title'     => esc_html__( 'Lorem Ipsum', 'hestia-pro' ),
				'subtitle'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
				'text'      => esc_html__( 'Button', 'hestia-pro' ),
				'link'      => '#',
				'id'        => 'customizer_repeater_56d7ea7f40a58',
				'color'     => '#e91e63',
			),
		);

		$lite_content = get_option( 'theme_mods_hestia' );

		if ( $lite_content ) {

			$hestia_big_title_title       = '';
			$hestia_big_title_text        = '';
			$hestia_big_title_button_text = '';
			$hestia_big_title_button_link = '';
			$hestia_big_title_background  = apply_filters( 'hestia_big_title_background_default', get_template_directory_uri() . '/assets/img/slider1.jpg' );

			if ( array_key_exists( 'hestia_big_title_title', $lite_content ) ) {
				$hestia_big_title_title = $lite_content['hestia_big_title_title'];
			}
			if ( array_key_exists( 'hestia_big_title_text', $lite_content ) ) {
				$hestia_big_title_text = $lite_content['hestia_big_title_text'];
			}
			if ( array_key_exists( 'hestia_big_title_button_text', $lite_content ) ) {
				$hestia_big_title_button_text = $lite_content['hestia_big_title_button_text'];
			}
			if ( array_key_exists( 'hestia_big_title_button_link', $lite_content ) ) {
				$hestia_big_title_button_link = $lite_content['hestia_big_title_button_link'];
			}
			if ( array_key_exists( 'hestia_big_title_background', $lite_content ) ) {
				$hestia_big_title_background = $lite_content['hestia_big_title_background'];
			}
			if ( ! empty( $hestia_big_title_title ) || ! empty( $hestia_big_title_text ) || ! empty( $hestia_big_title_button_text ) || ! empty( $hestia_big_title_button_link ) || ! empty( $hestia_big_title_background ) ) {
				return array(
					array(
						'id'        => 'customizer_repeater_56d7ea7f40a56',
						'title'     => $hestia_big_title_title,
						'subtitle'  => $hestia_big_title_text,
						'text'      => $hestia_big_title_button_text,
						'link'      => $hestia_big_title_button_link,
						'image_url' => $hestia_big_title_background,
					),
				);
			}
		}

		return $default;
	}

	/**
	 * Get default values for testimonials section.
	 *
	 * @since 1.1.31
	 * @access public
	 */
	public function get_testimonials_default() {
		return apply_filters(
			'hestia_testimonials_default_content',
			json_encode(
				array(
					array(
						'image_url' => get_template_directory_uri() . '/assets/img/5.jpg',
						'title'     => esc_html__( 'Inverness McKenzie', 'hestia-pro' ),
						'subtitle'  => esc_html__( 'Business Owner', 'hestia-pro' ),
						'text'      => esc_html__( '"We have no regrets! After using your product my business skyrocketed! I made back the purchase price in just 48 hours! I couldn\'t have asked for more than this."', 'hestia-pro' ),
						'id'        => 'customizer_repeater_56d7ea7f40d56',
					),
					array(
						'image_url' => get_template_directory_uri() . '/assets/img/6.jpg',
						'title'     => esc_html__( 'Hanson Deck', 'hestia-pro' ),
						'subtitle'  => esc_html__( 'Independent Artist', 'hestia-pro' ),
						'text'      => esc_html__( '"Your company is truly upstanding and is behind its product 100 percent. Hestia is worth much more than I paid. I like Hestia more each day because it makes easier."', 'hestia-pro' ),
						'id'        => 'customizer_repeater_56d7ea7f40d66',
					),
					array(
						'image_url' => get_template_directory_uri() . '/assets/img/7.jpg',
						'title'     => esc_html__( 'Natalya Undergrowth', 'hestia-pro' ),
						'subtitle'  => esc_html__( 'Freelancer', 'hestia-pro' ),
						'text'      => esc_html__( '"Thank you for making it painless, pleasant and most of all hassle free! I am so pleased with this product. Dude, your stuff is great! I will refer everyone I know."', 'hestia-pro' ),
						'id'        => 'customizer_repeater_56d7ea7f40d76',
					),
				)
			)
		);
	}

	/**
	 * Get payment icons default values.
	 *
	 * @return string
	 */
	public function get_payment_icons_defaults() {
		$default_payment_icons = array(
			array( 'icon_value' => 'fab fa-cc-visa' ),
			array( 'icon_value' => 'fab fa-cc-mastercard' ),
			array( 'icon_value' => 'fab fa-cc-paypal' ),
			array( 'icon_value' => 'fab fa-cc-stripe' ),
		);
		return apply_filters( 'hestia_payment_icons_default_content', json_encode( $default_payment_icons ) );
	}
}
