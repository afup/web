/* global infinite */
/* global console */

jQuery(document).ready(function($){

    /**
     * Append button after all posts
     */
    $('.blog-posts-wrap').append( '<div class="trigger"></div>' );
    var page = 2;
    var lock = false;


    $(window).scroll(function(){
        var postWrap = $('.blog-posts-wrap');
        var button = postWrap.find('.trigger');
        var processing = isElementInViewport(button);

        if ( processing === true && lock === false ) {
            if( page <= infinite.max_page ){
                postWrap.append( '<div class="loading text-center"><i class="fa fa-3x fa-spin fa-spinner" aria-hidden="true"></i></div>' );
            }

            lock = true;
            var data = {
                action: 'infinite_scroll',
                page: page,
                nonce: infinite.nonce
            };
            $.post(infinite.ajaxurl, data, function(res) {
                if( res ) {
                    postWrap.find('.loading').remove();
                    if( typeof infinite.masonry !== 'undefined' ){
                        var html = $.parseHTML( res );
                        var masonryGrid =  $('.post-grid-display');

                        $.each( html, function( i, el ){
                            masonryGrid.append( el );
                            masonryGrid.masonry( 'reloadItems' );
                            masonryGrid.imagesLoaded().progress( function() {
                                masonryGrid.masonry('layout');
                            });

                        });
                    } else {
                        button.prev().append( res );
                    }
                    page++;
                    lock = false;
                } else {
                    console.log(res);
                }
            }).fail(function(xhr) {
                console.log(xhr.responseText);
            });

        }


    });
});

/**
 * Detect if an element is in viewport or not
 *
 * @param el
 * @returns {boolean}
 */
function isElementInViewport (el) {

    //special bonus for those using jQuery
    if (typeof jQuery === 'function' && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}
