wp.customize.controlConstructor['hestia-dimensions'] = wp.customize.Control.extend({

    updateControl: function (value, query) {
        var control = this;
        var selector = control.selector;
        var currentValue = jQuery(selector).find('.dimensions-collector').val();

        if( typeof currentValue !== 'undefined' && currentValue !== ''){
            var obj = JSON.parse(currentValue);
            obj[query] = value;
            control.setting.set( JSON.stringify(obj) );
        } else {
            var returnObject = {};
            returnObject[query] = value;
            control.setting.set( JSON.stringify(returnObject) );
        }
    },


    ready: function() {

        'use strict';

        var control = this;
        var selector = control.selector;
        jQuery(selector).find('.dimensions-reset-container').on( 'click', function () {
            var default_value = jQuery(this).data('default');

            var dimensions = ['desktop','tablet','mobile'];
            dimensions.forEach(function (value) {
                if( typeof default_value[value] !== 'undefined' ){
                    var decodedValue = JSON.parse(default_value[value]);
                    for (var key in decodedValue) {
                        var val = decodedValue[key];
                        jQuery( selector ).find('.'+key).find('input').val(val);
                    }
                    control.updateControl(default_value[value],value);
                }
            });
        });

        var dimensions = ['desktop','tablet','mobile'];
        var types = ['_vertical', '_horizontal', '_top', '_left', '_right', '_bottom'];
        dimensions.forEach(function (value) {

            control.container.on( 'change keyup paste', '.dimension-'+value+'_vertical, .dimension-'+value+'_horizontal, .dimension-'+value+'_top, .dimension-'+value+'_bottom, .dimension-'+value+'_left, .dimension-'+value+'_right', function() {
                var return_value = {};
                var family = jQuery( this ).parent().parent();

                types.forEach(function (type) {
                    if( family.find('.dimension-'+value+type).length > 0 ){
                        return_value[value+type] = family.find('.dimension-'+value+type).val();
                    }
                });

                control.updateControl(JSON.stringify(return_value),value);
            } );
        });
    }

});

jQuery( document ).ready( function($) {

    // Linked button
    $( '.hestia-linked' ).on( 'click', function() {

        // Set up variables
        var $this = $( this );

        // Remove linked class
        $this.parent().parent( '.dimension-wrap' ).prevAll().slice(0,4).find( 'input' ).removeClass( 'linked' ).attr( 'data-element', '' );

        // Remove class
        $this.parent( '.link-dimensions' ).removeClass( 'unlinked' );

    } );

    // Unlinked button
    $( '.hestia-unlinked' ).on( 'click', function() {

        // Set up variables
        var $this 		= $( this ),
            $element 	= $this.data( 'element' );

        // Add linked class
        $this.parent().parent( '.dimension-wrap' ).prevAll().slice(0,4).find( 'input' ).addClass( 'linked' ).attr( 'data-element', $element );

        // Add class
        $this.parent( '.link-dimensions' ).addClass( 'unlinked' );

    } );

    // Values linked inputs
    $( '.dimension-wrap' ).on( 'input', '.linked', function() {

        var $data 	= $( this ).attr( 'data-element' ),
            $val 	= $( this ).val();

        $( '.linked[ data-element="' + $data + '" ]' ).each( function() {
            $( this ).val( $val ).change();
        } );

    } );



} );