jQuery(document).ready( function ( $ ) {

    var container = $( '#hestia-page-settings' );

    $.metaRadio = {

        /**
         * Init function
         */
        'init': function () {
            this.buttonSet();
            this.resetControl();
            this.handleClick();
        },

        /**
         * If elementor is installed, when clicked on a meta, it took you at the beginning of page.
         * This fixes the issue.
         */
        'handleClick': function () {
            container.find('.ui-button').on( 'click', function () {
                $(this).parent().find('.ui-button').removeClass('ui-state-active');
                $(this).addClass('ui-state-active');

                var forLabel = $(this).attr('for');
                var options = forLabel.split( '-' );
                var controlName = options[0];
                var controlValue = options[1];
                if( options.length > 1){
                    for( var i = 2; i < options.length; i++) {
                        controlValue += '-'+options[i];
                    }
                }
                $('input[name="'+controlName+'"][value="'+controlValue+'"]').prop('checked', true);
                $(this).siblings( '.reset-data-wrapper').children('.reset-data').removeClass('disabled');
                return false;
            });
        },

        /**
         * Buttonset init
         */
        'buttonSet': function () {
            container.find('.buttonset').buttonset();
            this.checkDefault();
        },

        /**
         * Check meta default value.
         */
        'checkDefault': function () {
            container.find('.inside').find('div[id^=\'control-hestia\']').each(function () {
               var control = $(this);
               var defaultValue = control.find( '.reset-data' ).data('default');
               var controlId = control.find( '.reset-data' ).data('id');
               control.find('input[name="'+controlId+'"]').each( function () {
                   if( $(this).attr('checked') === 'checked' && $(this).val() === defaultValue ){
                     $(this).siblings( '.reset-data-wrapper').children('.reset-data').addClass('disabled');
                   }
                });
            });
        },

        /**
         * Reset Control to default state
         */
        'resetControl': function () {
            $( '.reset-data' ).on('click', function () {
                var resetButton = $(this);
                var controlId = resetButton.data('id');
                var defaultValue = resetButton.data('default');
                resetButton.addClass('disabled');
                resetButton.parent().parent().find('label').removeClass('ui-state-active');
                resetButton.parent().parent().find('input[name="'+controlId+'"]').prop('checked', false);
                resetButton.parent().parent().find('label[for="'+ controlId +'-'+defaultValue+'"]').addClass('ui-state-active');
            });
        }
    };

    $.metaRadio.init();

});
