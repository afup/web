<?php
/**
 * Starter Content Compatibility.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Starter_Content
 */
class Hestia_Starter_Content {

	const HOME_SLUG    = 'home';
	const BLOG_SLUG    = 'blog';
	const CONTACT_SLUG = 'contact';

	/**
	 * Run the hooks and filters.
	 */
	public function __construct() {
		$is_fresh_site = get_option( 'fresh_site' );

		if ( ! $is_fresh_site ) {
			return;
		}

		if ( ! is_customize_preview() ) {
			return;
		}

		add_filter(
			'default_post_metadata',
			array( $this, 'starter_meta' ),
			99,
			3
		);
	}

	/**
	 * Load default starter meta.
	 *
	 * @param mixed  $value Value.
	 * @param int    $post_id Post id.
	 * @param string $meta_key Meta key.
	 *
	 * @return string Meta value.
	 */
	public function starter_meta( $value, $post_id, $meta_key ) {
		if ( get_post_type( $post_id ) !== 'page' ) {
			return $value;
		}

		if ( $meta_key === '_wp_page_template' ) {
			if ( get_the_title( $post_id ) === 'Contact' ) {
				return 'page-templates/template-pagebuilder-full-width.php';
			}
		}

		return $value;
	}

	/**
	 * Navigation items
	 *
	 * @return array
	 */
	private function get_nav_menu_items() {
		return array(
			'page_home'        => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{' . self::HOME_SLUG . '}}',
			),
			'page_blog'        => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{' . self::BLOG_SLUG . '}}',
			),
			'page_contact'     => array(
				'type'      => 'post_type',
				'object'    => 'page',
				'object_id' => '{{' . self::CONTACT_SLUG . '}}',
			),
			'link_menu_button' => array(
				'url'     => '#about',
				'title'   => _x( 'More', 'Theme starter content' ),
				'classes' => array( 'btn', 'btn-round', 'btn-primary', 'hestia-mega-menu' ),
			),
		);
	}

	/**
	 * Default contact page content.
	 *
	 * @return string
	 */
	private function get_default_contact_content() {
		return '<div class="hestia-info info info-horizontal">
		<div class="icon icon-primary"><i class="fas fa-map-marker-alt"></i></div>
		<div class="description">
		<h4 class="info-title">Find us at the office</h4>
		Strada Povernei, nr 20, Bucharest, Romania
		
		</div>
		</div>
		<div class="hestia-info info info-horizontal">
		<div class="icon icon-primary"><i class="fas fa-mobile-alt"></i></div>
		<div class="description">
		<h4 class="info-title">Give us a ring</h4>
		John Doe
		+40 712 345 678
		Mon – Fri, 8:00-22:00
		
		</div>
		</div>';
	}

	/**
	 * Return starter content definition.
	 *
	 * @return mixed|void
	 */
	public function get() {
		$nav_items                   = $this->get_nav_menu_items();
		$contact_default             = $this->get_default_contact_content();
		$default_home_featured_image = get_template_directory_uri() . '/assets/img/contact.jpg';
		$default_slides              = array(
			array(
				'image_url' => get_template_directory_uri() . '/assets/img/slider1.jpg',
				'title'     => esc_html__( 'Lorem Ipsum', 'hestia-pro' ),
				'subtitle'  => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'hestia-pro' ),
				'text'      => esc_html__( 'Button', 'hestia-pro' ),
				'link'      => '#',
				'id'        => 'customizer_repeater_56d7ea7f40a56',
				'color'     => '#e91e63',
			),
		);

		return array(
			'theme_mods'  => array(
				'hestia_big_title_title'       => 'Welcome to Hestia!',
				'hestia_big_title_text'        => 'Lorem ipsum dolor sit amet',
				'hestia_big_title_button_text' => 'See more',
				'hestia_big_title_button_link' => '#about',
				'hestia_contact_title'         => 'Get in Touch',
				'hestia_contact_content_new'   => $contact_default,
				'hestia_blog_sidebar_layout'   => 'full-width',
				'hestia_slider_content'        => json_encode( $default_slides ),
				'disable_frontpage_sections'   => false,
			),
			'attachments' => array(
				'featured-image-home' => array(
					'post_title'   => __( 'Featured Image Homepage', 'hestia-pro' ),
					'post_content' => __( 'Featured Image Homepage', 'hestia-pro' ),
					'file'         => 'assets/img/contact.jpg',
				),
				'featured-slide1'     => array(
					'post_title' => 'First slide',
					'file'       => 'assets/img/slider1.jpg',
				),
				'post-1'              => array(
					'post_title' => 'Landscape',
					'file'       => 'assets/img/card-blog1.jpg',
				),
				'post-2'              => array(
					'post_title' => 'Drone',
					'file'       => 'assets/img/card-blog2.jpg',
				),
				'post-3'              => array(
					'post_title' => 'Convertible',
					'file'       => 'assets/img/card-blog3.jpg',
				),
				'post-4'              => array(
					'post_title' => 'Tourism',
					'file'       => 'assets/img/card-blog4.jpg',
				),
				'post-5'              => array(
					'post_title' => 'Castle',
					'file'       => 'assets/img/card-blog5.jpg',
				),
			),
			'posts'       => array(
				self::HOME_SLUG    => require __DIR__ . '/starter-content/home.php',
				self::CONTACT_SLUG => require __DIR__ . '/starter-content/contact.php',
				self::BLOG_SLUG    => array(
					'post_name'  => self::BLOG_SLUG,
					'post_type'  => 'page',
					'post_title' => _x( 'Blog', 'Theme starter content' ),
				),
				'custom_post_1'    => array(
					'post_type'    => 'post',
					'post_title'   => 'Appearance guide',
					'post_content' => '<!-- wp:paragraph -->
					<p>Yet bed any for travelling assistance indulgence unpleasing. Not thoughts all exercise blessing. Indulgence way everything joy alteration boisterous the attachment. Party we years to order allow asked of. We so opinion friends me message as delight. Whole front do of plate heard oh ought. His defective nor convinced residence own. Connection has put impossible own apartments boisterous. At jointure ladyship an insisted so humanity he. Friendly bachelor entrance to on by.</p>
					<!-- /wp:paragraph -->
					
					<!-- wp:paragraph -->
					<p>That last is no more than a foot high, and about seven paces across, a mere flat top of a grey rock which smokes like a hot cinder after a shower, and where no man would care to venture a naked sole before sunset. On the Little Isabel an old ragged palm, with a thick bulging trunk rough with spines, a very witch amongst palm trees, rustles a dismal bunch of dead leaves above the<a href="#"> coarse sand</a>.</p>
					<!-- /wp:paragraph -->',
					'thumbnail'    => '{{post-1}}',
				),
				'custom_post_2'    => array(
					'post_type'    => 'post',
					'post_title'   => 'Perfectly on furniture',
					'post_content' => '<!-- wp:heading {"level":3,"className":"title"} -->
					<h3 class="title">Feet evil to hold long he open knew an no.</h3>
					<!-- /wp:heading -->
					
					<!-- wp:paragraph -->
					<p>Apartments occasional boisterous as solicitude to introduced. Or fifteen covered we enjoyed demesne is in prepare. In stimulated my everything it literature. Greatly explain attempt perhaps in feeling he. House men taste bed not drawn joy. Through enquire however do equally herself at. Greatly way old may you present improve. Wishing the feeling village him musical.</p>
					<!-- /wp:paragraph -->
					
					<!-- wp:paragraph -->
					<p>Smile spoke total few great had never their too. Amongst moments do in arrived at my replied. Fat weddings servants but man believed prospect. Companions understood is as especially pianoforte connection introduced. Nay newspaper can sportsman are admitting gentleman belonging his.</p>
					<!-- /wp:paragraph -->',
					'thumbnail'    => '{{post-2}}',
				),
				'custom_post_3'    => array(
					'post_type'    => 'post',
					'post_title'   => 'Fat son how smiling natural',
					'post_content' => '<!-- wp:paragraph -->
					<p><em>To shewing another demands sentiments. Marianne property cheerful informed at striking at. Clothes parlors however by cottage on. In views it or meant drift to. Be concern parlors settled or do shyness address.&nbsp;</em></p>
					<!-- /wp:paragraph -->
					
					<!-- wp:heading -->
					<h2>He always do do former he highly.</h2>
					<!-- /wp:heading -->
					
					<!-- wp:paragraph -->
					<p>Continual so distrusts pronounce by unwilling listening</p>
					<!-- /wp:paragraph -->
					
					<!-- wp:paragraph -->
					<p>Expenses as material breeding insisted building to in. Continual so distrusts pronounce by unwilling listening. Thing do taste on we manor. Him had wound use found hoped. Of distrusts immediate enjoyment curiosity do. Marianne numerous saw thoughts the humoured.</p>
					<!-- /wp:paragraph -->',
					'thumbnail'    => '{{post-3}}',
				),
				'custom_post_4'    => array(
					'post_type'    => 'post',
					'post_title'   => 'Can curiosity may end shameless explained',
					'post_content' => '<!-- wp:heading -->
					<h2>Way nor furnished sir procuring therefore but.</h2>
					<!-- /wp:heading -->
					
					<!-- wp:paragraph -->
					<p>Warmth far manner myself active are cannot called. Set her half end girl rich met. Me allowance departure an curiosity ye. In no talking address excited it conduct. Husbands debating replying overcame<em>&nbsp;blessing</em>&nbsp;he it me to domestic.</p>
					<!-- /wp:paragraph -->
					
					<!-- wp:list -->
					<ul><li>As absolute is by amounted repeated entirely ye returned.</li><li>These ready timed enjoy might sir yet one since.</li><li>Years drift never if could forty being no.</li></ul>
					<!-- /wp:list -->',
					'thumbnail'    => '{{post-4}}',
				),
				'custom_post_5'    => array(
					'post_type'    => 'post',
					'post_title'   => 'Improve him believe opinion offered',
					'post_content' => '<!-- wp:paragraph -->
					<p>It acceptance thoroughly my advantages everything as. Are projecting inquietude affronting preference saw who. Marry of am do avoid ample as. Old disposal followed she ignorant desirous two has. Called played entire roused though for one too. He into walk roof made tall cold he. Feelings way likewise addition wandered contempt bed indulged.</p>
					<!-- /wp:paragraph -->
					
					<!-- wp:heading {"level":4} -->
					<h4><strong>Still court no small think death so an wrote.</strong></h4>
					<!-- /wp:heading -->
					
					<!-- wp:paragraph -->
					<p>Incommode necessary no it behaviour convinced distrusts an unfeeling he. Could death since do we hoped is in. Exquisite no my attention extensive. The determine conveying moonlight age. Avoid for see marry sorry child. Sitting so totally forbade hundred to.</p>
					<!-- /wp:paragraph -->',
					'thumbnail'    => '{{post-5}}',
				),
			),
			'nav_menus'   => array(
				'primary' => array(
					'name'  => esc_html__( 'Primary Menu', 'hestia-pro' ),
					'items' => $nav_items,
				),
			),
			'options'     => array(
				'show_on_front'            => 'page',
				'page_on_front'            => '{{' . self::HOME_SLUG . '}}',
				'page_for_posts'           => '{{' . self::BLOG_SLUG . '}}',
				'hestia_feature_thumbnail' => $default_home_featured_image,
			),
		);
	}
}
