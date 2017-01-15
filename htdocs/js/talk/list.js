var search = instantsearch({
    appId: document.head.querySelector("[name=algolia_appid]").content,
    apiKey: document.head.querySelector("[name=algolia_apikey]").content,
    indexName: 'afup_talks',
    urlSync: {}
});

search.addWidget(
    instantsearch.widgets.searchBox({
        container: '#search-box',
        placeholder: 'Rechercher une conférence...',
        poweredBy: true

    })
);

search.addWidget(
    instantsearch.widgets.hits({
        hitsPerPage: 7,
        container: '#hits-container',
        templates: {
            empty: "Pas de résultat",
            item: function(data) {
                var thumbnailUrl = '/images/no_video.jpg';

                if (typeof data.video_id !== 'undefined') {
                    thumbnailUrl = 'https://img.youtube.com/vi/' + data.video_id + '/hqdefault.jpg';
                }

                var content = ''
                + '<div class="container">'
                ;

                content += ''
                    + '<div class="col-md-2">'
                       + '<a href="/talks/' + data.url_key + '"><img src="' + thumbnailUrl + '" /></a>'
                    + '</div>'
                ;

                content += ''
                    + '<div class="col-md-10">'
                        + '<div class="talk-list-title-container">'
                            + '<a href="/talks/' + data.url_key + '"><h2>' + data.title + '</h2></a>'
                        + '</div>'
                        + '<div class="talk-list-speakers-container ">'
                            + '' + data.speakers_label + ''
                            + ' - <i>' + data.event.title + '</i>'
                        + '</div>'
                        + '<div class="links-container">'
                ;


                if (typeof data.blog_post_url !== 'undefined') {
                    content += '<a class="talk-info-link" href="' + data.blog_post_url + '" class="talk-list-" target="blog-post"><i class="fa fa-rss"></i> Article</a>';
                }

                if (typeof data.slides_url !== 'undefined') {
                    content += '<a class="talk-info-link"  href="' + data.slides_url + '" class="" target="slides"><i class="fa fa-slideshare"></i> Slides</a>';
                }

                if (typeof data.joindin_url !== 'undefined') {
                    content += '<a class="talk-info-link" href="' + data.joindin_url + '" class="talk-" target="joindin"><i class="fa fa-comments"></i> Fiche joind.in</a>';
                }

                content += ''
                        + '</div>'
                ;

                content += ''
                    + '</div>'
                + '</div>'
                ;

                return content;
            }
        }
    })
);


var refinementItemTemplate = function(data) {
    var content = "";
    content += '<input type="checkbox" ';
    if (data.isRefined) {
        content += ' checked="checked" ';
    }
    content += " />";

    content += '<label>' + data.name +'</label>';

    if (data.count) {
        content += ' <span class="talk-list-refinement-count-badge">' + data.count + "</span>";
    }

    return content;
};

search.addWidget(
    instantsearch.widgets.toggle({
        container: '#refinement-has-video',
        attributeName: 'has_video',
        label: 'Avec vidéo',
        values: {
            on: true
        },
        autoHideContainer: false,
        templates: {
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.toggle({
        container: '#refinement-has-slides',
        attributeName: 'has_slides',
        label: 'Avec slides',
        values: {
            on: true
        },
        autoHideContainer: false,
        templates: {
            item: refinementItemTemplate
        }
    })
);


search.addWidget(
    instantsearch.widgets.toggle({
        container: '#refinement-has-blog-post',
        attributeName: 'has_blog_post',
        label: 'Avec article de blog',
        values: {
            on: true
        },
        autoHideContainer: false,
        templates: {
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-event',
        attributeName: 'event.title',
        operator: "and",
        templates: {
            header: "<h4>Événement</h4>",
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-speaker',
        attributeName: 'speakers.label',
        operator: "and",
        templates: {
            header: "<h4>Conférencier</h4>",
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.pagination({
        container: '#pagination'
    })
);
search.start();
