<?php
/**
 * Addon Hooks Functionality.
 *
 * @package Hestia
 */

/**
 * List of available hooks
 *
 * @package Hestia
 * @since   Hestia 1.1.39
 */
/* Big title / Slider section */
/**
 * Hook just before the Big title / Slider section
 *
 * HTML context: before `div#carousel-hestia-generic`
 */
function hestia_before_big_title_section_trigger() {
	do_action( 'hestia_before_big_title_section_hook' );
}

/**
 * Hook just before the Big title / Slider section content
 *
 * HTML context: before `div#carousel-hestia-generic div.container`
 */
function hestia_before_big_title_section_content_trigger() {
	do_action( 'hestia_before_big_title_section_content_hook' );
}

/**
 * Hook at the top of the Big title / Slider section content
 *
 * HTML context: just after `div#carousel-hestia-generic div.container`
 */
function hestia_top_big_title_section_content_trigger() {
	do_action( 'hestia_top_big_title_section_content_hook' );
}

/**
 * Hook after the button in Big Title Section
 *
 * HTML context: inside `div.big-title-content div.buttons`
 *
 * @param bool | int $slide_nb Current slide number.
 */
function hestia_big_title_section_buttons_trigger( $slide_nb = false ) {
	do_action( 'hestia_big_title_section_buttons', $slide_nb );
}

/**
 * Hook at the bottom of the Big title / Slider section content
 *
 * HTML context: just before the closing of `div#carousel-hestia-generic div.container`
 */
function hestia_bottom_big_title_section_content_trigger() {
	do_action( 'hestia_bottom_big_title_section_content_hook' );
}

/**
 * Hook just after the Big title / Slider section content
 *
 * HTML context: after `div#carousel-hestia-generic div.container`
 */
function hestia_after_big_title_section_content_trigger() {
	do_action( 'hestia_after_big_title_section_content_hook' );
}

/**
 * Hook just after the Big title / Slider section
 *
 * HTML context: after `div#carousel-hestia-generic`
 */
function hestia_after_big_title_section_trigger() {
	do_action( 'hestia_after_big_title_section_hook' );
}

/* Team section */
/**
 * Hook just before the Team section
 *
 * HTML context: before `section.hestia-team`
 */
function hestia_before_team_section_trigger() {
	do_action( 'hestia_before_team_section_hook' );
}

/**
 * Hook just before the Team section content
 *
 * HTML context: before `section.hestia-team div.container`
 */
function hestia_before_team_section_content_trigger() {
	do_action( 'hestia_before_team_section_content_hook' );
}

/**
 * Hook at the top of the Team section content
 *
 * HTML context: just after the `section.hestia-team div.container`
 */
function hestia_top_team_section_content_trigger() {
	do_action( 'hestia_top_team_section_content_hook' );
}

/**
 * Hook at the bottom of the Team section content
 *
 * HTML context: just before the closing of `section.hestia-team div.container`
 */
function hestia_bottom_team_section_content_trigger() {
	do_action( 'hestia_bottom_team_section_content_hook' );
}

/**
 * Hook just after the Team section content
 *
 * HTML context: after `section.hestia-team div.container`
 */
function hestia_after_team_section_content_trigger() {
	do_action( 'hestia_after_team_section_content_hook' );
}

/**
 * Hook just after the Team section
 *
 * HTML context: after `section.hestia-team`
 */
function hestia_after_team_section_trigger() {
	do_action( 'hestia_after_team_section_hook' );
}

/* Features section */
/**
 * Hook just before the Features section
 *
 * HTML context: before `section.hestia-features`
 */
function hestia_before_features_section_trigger() {
	do_action( 'hestia_before_features_section_hook' );
}

/**
 * Hook just before the Features section content
 *
 * HTML context: before `section.hestia-features div.container`
 */
function hestia_before_features_section_content_trigger() {
	do_action( 'hestia_before_features_section_content_hook' );
}

/**
 * Hook at the top of the Features section content
 *
 * HTML context: just after `section.hestia-features div.container`
 */
function hestia_top_features_section_content_trigger() {
	do_action( 'hestia_top_features_section_content_hook' );
}

/**
 * Hook at the bottom of the Features section content
 *
 * HTML context: just before the closing of `section.hestia-features div.container`
 */
function hestia_bottom_features_section_content_trigger() {
	do_action( 'hestia_bottom_features_section_content_hook' );
}

/**
 * Hook just after the Features section content
 *
 * HTML context: after `section.hestia-features div.container`
 */
function hestia_after_features_section_content_trigger() {
	do_action( 'hestia_after_features_section_content_hook' );
}

/**
 * Hook just after the Features section
 *
 * HTML context: after `section.hestia-features`
 */
function hestia_after_features_section_trigger() {
	do_action( 'hestia_after_features_section_hook' );
}

/* Pricing section */
/**
 * Hook just before the Pricing section
 *
 * HTML context: before `section.pricing`
 */
function hestia_before_pricing_section_trigger() {
	do_action( 'hestia_before_pricing_section_hook' );
}

/**
 * Hook just before the Pricing section content
 *
 * HTML context: before `section.pricing div.container`
 */
function hestia_before_pricing_section_content_trigger() {
	do_action( 'hestia_before_pricing_section_content_hook' );
}

/**
 * Hook at the top of the Pricing section content
 *
 * HTML context: just before `section.pricing div.container`
 */
function hestia_top_pricing_section_content_trigger() {
	do_action( 'hestia_top_pricing_section_content_hook' );
}

/**
 * Hook at the bottom of the Pricing section content
 *
 * HTML context: just before the closing of `section.pricing div.container`
 */
function hestia_bottom_pricing_section_content_trigger() {
	do_action( 'hestia_bottom_pricing_section_content_hook' );
}

/**
 * Hook just after the Pricing section content
 *
 * HTML context: after `section.pricing div.container`
 */
function hestia_after_pricing_section_content_trigger() {
	do_action( 'hestia_after_pricing_section_content_hook' );
}

/**
 * Hook just after the Pricing section
 *
 * HTML context: after `section.pricing`
 */
function hestia_after_pricing_section_trigger() {
	do_action( 'hestia_after_pricing_section_hook' );
}

/* About section */
/**
 * Hook just before the About section
 *
 * HTML context: before `section.hestia-about`
 */
function hestia_before_about_section_trigger() {
	do_action( 'hestia_before_about_section_hook' );
}

/**
 * Hook just after the About section
 *
 * HTML context: after `section.hestia-about`
 */
function hestia_after_about_section_trigger() {
	do_action( 'hestia_after_about_section_hook' );
}

/* Shop section */
/**
 * Hook just before the Shop section
 *
 * HTML context: before `section.products`
 */
function hestia_before_shop_section_trigger() {
	do_action( 'hestia_before_shop_section_hook' );
}

/**
 * Hook just before the Shop section content
 *
 * HTML context: before `section.products div.container`
 */
function hestia_before_shop_section_content_trigger() {
	do_action( 'hestia_before_shop_section_content_hook' );
}

/**
 * Hook at the top of the Shop section content
 *
 * HTML context: just after the `section.products div.container`
 */
function hestia_top_shop_section_content_trigger() {
	do_action( 'hestia_top_shop_section_content_hook' );
}

/**
 * Hook at the bottom of the Shop section content
 *
 * HTML context: just before the closing of `section.products div.container`
 */
function hestia_bottom_shop_section_content_trigger() {
	do_action( 'hestia_bottom_shop_section_content_hook' );
}

/**
 * Hook just after the Shop section content
 *
 * HTML context: after `section.products div.container`
 */
function hestia_after_shop_section_content_trigger() {
	do_action( 'hestia_after_shop_section_content_hook' );
}

/**
 * Hook just after the Shop section
 *
 * HTML context: after `section.products`
 */
function hestia_after_shop_section_trigger() {
	do_action( 'hestia_after_shop_section_hook' );
}

/* Testimonials section */
/**
 * Hook just before the Testimonials section
 *
 * HTML context: before `section.hestia-testimonials`
 */
function hestia_before_testimonials_section_trigger() {
	do_action( 'hestia_before_testimonials_section_hook' );
}

/**
 * Hook just before the Testimonials section content
 *
 * HTML context: before `section.hestia-testimonials div.container`
 */
function hestia_before_testimonials_section_content_trigger() {
	do_action( 'hestia_before_testimonials_section_content_hook' );
}

/**
 * Hook at the top of the Testimonials section content
 *
 * HTML context: just after the `section.hestia-testimonials div.container`
 */
function hestia_top_testimonials_section_content_trigger() {
	do_action( 'hestia_top_testimonials_section_content_hook' );
}

/**
 * Hook at the bottom of the Testimonials section content
 *
 * HTML context: just before the closing of `section.hestia-testimonials div.container`
 */
function hestia_bottom_testimonials_section_content_trigger() {
	do_action( 'hestia_bottom_testimonials_section_content_hook' );
}

/**
 * Hook just after the Testimonials section content
 *
 * HTML context: after `section.hestia-testimonials div.container`
 */
function hestia_after_testimonials_section_content_trigger() {
	do_action( 'hestia_after_testimonials_section_content_hook' );
}

/**
 * Hook just after the Testimonials section
 *
 * HTML context: after `section.hestia-testimonials`
 */
function hestia_after_testimonials_section_trigger() {
	do_action( 'hestia_after_testimonials_section_hook' );
}

/* Subscribe section */
/**
 * Hook just before the Subscribe section
 *
 * HTML context: before `section.subscribe-line`
 */
function hestia_before_subscribe_section_trigger() {
	do_action( 'hestia_before_subscribe_section_hook' );
}

/**
 * Hook just before the Subscribe section content
 *
 * HTML context: before `section.subscribe-line div.container`
 */
function hestia_before_subscribe_section_content_trigger() {
	do_action( 'hestia_before_subscribe_section_content_hook' );
}

/**
 * Hook at the top of the Subscribe section content
 *
 * HTML context: just after the `section.subscribe-line div.container`
 */
function hestia_top_subscribe_section_content_trigger() {
	do_action( 'hestia_top_subscribe_section_content_hook' );
}

/**
 * Hook at the bottom of the Subscribe section content
 *
 * HTML context: just before the closing of `section.subscribe-line div.container`
 */
function hestia_bottom_subscribe_section_content_trigger() {
	do_action( 'hestia_bottom_subscribe_section_content_hook' );
}

/**
 * Hook just after the Subscribe section content
 *
 * HTML context: after `section.subscribe-line div.container`
 */
function hestia_after_subscribe_section_content_trigger() {
	do_action( 'hestia_after_subscribe_section_content_hook' );
}

/**
 * Hook just after the Subscribe section
 *
 * HTML context: after `section.subscribe-line`
 */
function hestia_after_subscribe_section_trigger() {
	do_action( 'hestia_after_subscribe_section_hook' );
}

/* Blog section */
/**
 * Hook just before the Blog section
 *
 * HTML context: before `section.hestia-blogs`
 */
function hestia_before_blog_section_trigger() {
	do_action( 'hestia_before_blog_section_hook' );
}

/**
 * Hook just before the Blog section content
 *
 * HTML context: before `section.hestia-blogs div.container`
 */
function hestia_before_blog_section_content_trigger() {
	do_action( 'hestia_before_blog_section_content_hook' );
}

/**
 * Hook at the top of the Blog section content
 *
 * HTML context: just after the `section.hestia-blogs div.container`
 */
function hestia_top_blog_section_content_trigger() {
	do_action( 'hestia_top_blog_section_content_hook' );
}

/**
 * Hook at the bottom of the Blog section content
 *
 * HTML context: just before the closing of `section.hestia-blogs div.container`
 */
function hestia_bottom_blog_section_content_trigger() {
	do_action( 'hestia_bottom_blog_section_content_hook' );
}

/**
 * Hook just after the Blog section content
 *
 * HTML context: after `section.hestia-blogs div.container`
 */
function hestia_after_blog_section_content_trigger() {
	do_action( 'hestia_after_blog_section_content_hook' );
}

/**
 * Hook just after the Blog section
 *
 * HTML context: after `section.hestia-blogs`
 */
function hestia_after_blog_section_trigger() {
	do_action( 'hestia_after_blog_section_hook' );
}

/* Contact section */
/**
 * Hook just before the Contact section
 *
 * HTML context: before `section.contactus`
 */
function hestia_before_contact_section_trigger() {
	do_action( 'hestia_before_contact_section_hook' );
}

/**
 * Hook just before the Contact section content
 *
 * HTML context: before `section.contactus div.container`
 */
function hestia_before_contact_section_content_trigger() {
	do_action( 'hestia_before_contact_section_content_hook' );
}

/**
 * Hook at the top of the Contact section content
 *
 * HTML context: just after the `section.contactus div.container`
 */
function hestia_top_contact_section_content_trigger() {
	do_action( 'hestia_top_contact_section_content_hook' );
}

/**
 * Hook at the bottom of the Contact section content
 *
 * HTML context: just before the closing of `section.contactus div.container`
 */
function hestia_bottom_contact_section_content_trigger() {
	do_action( 'hestia_bottom_contact_section_content_hook' );
}

/**
 * Hook just after the Contact section content
 *
 * HTML context: after `section.contactus div.container`
 */
function hestia_after_contact_section_content_trigger() {
	do_action( 'hestia_after_contact_section_content_hook' );
}

/**
 * Hook just after the Contact section
 *
 * HTML context: after `section.contactus`
 */
function hestia_after_contact_section_trigger() {
	do_action( 'hestia_after_contact_section_hook' );
}

/* Portfolio section */
/**
 * Hook just before the Portfolio section
 *
 * HTML context: before `section.hestia-work`
 */
function hestia_before_portfolio_section_trigger() {
	do_action( 'hestia_before_portfolio_section_hook' );
}

/**
 * Hook just before the Portfolio section content
 *
 * HTML context: before `section.hestia-work div.container`
 */
function hestia_before_portfolio_section_content_trigger() {
	do_action( 'hestia_before_portfolio_section_content_hook' );
}

/**
 * Hook at the top of the Portfolio section content
 *
 * HTML context: just after `section.hestia-work div.container`
 */
function hestia_top_portfolio_section_content_trigger() {
	do_action( 'hestia_top_portfolio_section_content_hook' );
}

/**
 * Hook at the bottom of the Portfolio section content
 *
 * HTML context:  just before the closing of `section.hestia-work div.container`
 */
function hestia_bottom_portfolio_section_content_trigger() {
	do_action( 'hestia_bottom_portfolio_section_content_hook' );
}

/**
 * Hook just after the Portfolio section content
 *
 * HTML context: after `section.hestia-work div.container`
 */
function hestia_after_portfolio_section_content_trigger() {
	do_action( 'hestia_after_portfolio_section_content_hook' );
}

/**
 * Hook just after the Portfolio section
 *
 * HTML context: after `section.hestia-work`
 */
function hestia_after_portfolio_section_trigger() {
	do_action( 'hestia_after_portfolio_section_hook' );
}

/* Clients Bar section */
/**
 * Hook just before the Clients Bar section
 *
 * HTML context: before `section.hestia-clients-bar`
 */
function hestia_before_clients_bar_section_trigger() {
	do_action( 'hestia_before_clients_bar_section_hook' );
}

/**
 * Hook just before the Clients Bar section content
 *
 * HTML context: just after `section.hestia-clients-bar div.container`
 */
function hestia_clients_bar_section_content_trigger() {
	do_action( 'hestia_clients_bar_section_content_hook' );
}

/**
 * Hook just after the Clients Bar section
 *
 * HTML context: after `section.hestia-clients-bar`
 */
function hestia_after_clients_bar_section_trigger() {
	do_action( 'hestia_after_clients_bar_section_hook' );
}

/* Ribbon section */
/**
 * Hook just before the Ribbon section
 *
 * HTML context: before `section.hestia-ribbon`
 */
function hestia_before_ribbon_section_trigger() {
	do_action( 'hestia_before_ribbon_section_hook' );
}

/**
 * Hook just after the Ribbon section
 *
 * HTML context: after `section.hestia-ribbon`
 */
function hestia_after_ribbon_section_trigger() {
	do_action( 'hestia_after_ribbon_section_hook' );
}
