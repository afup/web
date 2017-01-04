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
                var content = '<div class="conf-title"><a href="/talks/' + data.url_key + '">' + data.title + '</a> ' + '<span class="talk-list-event-label-badge">' + data.event.title + '</span></div>'
                        + ' <i>' + data.speakers_label + '</i>'
                        + '<div style="text-align:right">'
                    ;

                if (typeof data.video_url !== 'undefined') {
                    content += '<a href="' + data.video_url + '" class="talk-list-button" target="video"><i class="fa fa-youtube-play"></i> Vidéo</a>';
                }

                if (typeof data.slides_url !== 'undefined') {
                    content += '<a href="' + data.slides_url + '" class="talk-list-button" target="slides"><i class="fa fa-slideshare"></i> Slides</a>';
                }

                if (typeof data.blog_post_url !== 'undefined') {
                    content += '<a href="' + data.blog_post_url + '" class="talk-list-button" target="blog-post"><i class="fa fa-rss"></i> Article</a>';
                }

                if (typeof data.joindin_url !== 'undefined') {
                    content += '<a href="' + data.joindin_url + '" class="talk-list-button" target="joindin"><i class="fa fa-comments"></i> Fiche joinin</a>';
                }

                content += '</div>';
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

    content += data.name;

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
    instantsearch.widgets.refinementList({
        container: '#refinement-event',
        attributeName: 'event.title',
        templates: {
            header: "Événement",
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-speaker',
        attributeName: 'speakers.label',
        templates: {
            header: "Conférencier",
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
