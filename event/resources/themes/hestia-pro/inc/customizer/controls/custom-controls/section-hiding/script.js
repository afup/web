/**
 * Scripts file for the Show/Hide frontpage control in customizer
 *
 * @package Hestia
 */

/* global jQuery */
/* global wp */

jQuery(window).on('load',
  function () {
    'use strict';

    var controlValue;

    var add_customizer_shortcut = {
      'subscribe': '#accordion-section-sidebar-widgets-subscribe-widgets',
      'big_title': '#accordion-section-sidebar-widgets-sidebar-big-title'
    };

    Object.keys(add_customizer_shortcut).forEach(function (key) {
      var value = add_customizer_shortcut[key];

      var section = jQuery(value);
      var sectionHide = wp.customize.control('hestia_' + key + '_hide');

      if (typeof sectionHide !== 'undefined') {
        controlValue = sectionHide.setting.get();
      }
      var iconClass = 'dashicons-visibility';

      if (controlValue === true) {
        iconClass = 'dashicons-hidden';
        section.find('.accordion-section-title').addClass('hestia-section-hidden').removeClass('hestia-section-visible');
      } else {
        section.find('.accordion-section-title').addClass('hestia-section-visible').removeClass('hestia-section-hidden');
      }

      section.find('.screen-reader-text').after('<a data-control="hestia_' + key + '_hide" class="alignright hestia-toggle-section" href="#"><span class="dashicons' + iconClass + '"></span></a>');
    });

    var toggleSection = jQuery('.hestia-toggle-section');
    /**
     * Fix for icons when they are in changeset is active
     */
    toggleSection.each(
      function () {
        var controlName = jQuery(this).data('control');
        var controlValue;
        if (typeof wp.customize.control(controlName) !== 'undefined') {
          controlValue = wp.customize.control(controlName).setting.get();
        }
        var parentHeader = jQuery(this).parent();
        if (typeof (controlName) !== 'undefined' && controlName !== '') {
          var iconClass = 'dashicons-visibility';
          if (controlValue === true) {
            iconClass = 'dashicons-hidden';
            parentHeader.addClass('hestia-section-hidden').removeClass('hestia-section-visible');
          } else {
            parentHeader.addClass('hestia-section-visible').removeClass('hestia-section-hidden');
          }
          jQuery(this).children().attr('class', 'dashicons ' + iconClass);
        }
      }
    );

    toggleSection.on(
      'click', function (e) {
        e.stopPropagation();
        var controlName = jQuery(this).data('control');
        var parentHeader = jQuery(this).parent();
        var controlValue = wp.customize.control(controlName).setting.get();
        if (typeof (controlName) !== 'undefined' && controlName !== '') {
          var iconClass = 'dashicons-visibility';
          /* Compare with false because value already changed when triggered this function */
          if (controlValue === false) {
            iconClass = 'dashicons-hidden';
            parentHeader.addClass('hestia-section-hidden').removeClass('hestia-section-visible');
          } else {
            parentHeader.addClass('hestia-section-visible').removeClass('hestia-section-hidden');
          }
          wp.customize.control(controlName).setting.set(!controlValue);
          jQuery(this).children().attr('class', 'dashicons ' + iconClass);
        }
      }
    );

    jQuery('ul').find('[data-customize-setting-link]').on(
      'click', function () {
        var showHideControls = [
          'hestia_big_title_hide',
          'hestia_features_hide',
          'hestia_about_hide',
          'hestia_shop_hide',
          'hestia_portfolio_hide',
          'hestia_team_hide',
          'hestia_pricing_hide',
          'hestia_ribbon_hide',
          'hestia_testimonials_hide',
          'hestia_subscribe_hide',
          'hestia_clients_bar_hide',
          'hestia_blog_hide',
          'hestia_contact_hide'
        ];
        var controlName = jQuery(this).data('customize-setting-link');
        if (showHideControls.indexOf(controlName) <= -1) {
          return;
        }
        var sectionName = jQuery(this).parent().parent().parent().attr('id');
        sectionName = sectionName.replace('sub-', '');
        var parentHeader = jQuery('#' + sectionName).find('.accordion-section-title');
        if (typeof (sectionName) !== 'undefined' && sectionName !== '') {

          if (wp.customize.control(controlName) && wp.customize.control(controlName).setting) {
            var controlValue = wp.customize.control(controlName).setting.get();

            var iconClass = 'dashicons-visibility';
            if (controlValue === false) {
              iconClass = 'dashicons-hidden';
              parentHeader.addClass('hestia-section-hidden').removeClass('hestia-section-visible');
            } else {
              parentHeader.addClass('hestia-section-visible');
              parentHeader.removeClass('hestia-section-hidden');
            }
            parentHeader.find('.hestia-toggle-section').children().attr('class', 'dashicons ' + iconClass);
          }
        }
      }
    );
  }
);
