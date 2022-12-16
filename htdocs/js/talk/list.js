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
                    content += '<a class="talk-info-link" href="' + data.blog_post_url + '" class="talk-list-" target="blog-post"><i class="fa fa-rss"></i> Article de blog</a>';
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
    instantsearch.widgets.toggle({
        container: '#refinement-video-has-fr-subtitles',
        attributeName: 'video_has_fr_subtitles',
        label: 'Avec sous titres français',
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
        container: '#refinement-video-has-en-subtitles',
        attributeName: 'video_has_en_subtitles',
        label: 'Avec sous titres anglais',
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
        sortBy: function(a, b) {
            if (a.name.substring(0, 8) === 'AFUP Day') {
                var aYear = a.name.split(' ').splice(-2, 1).join('');
            } else {
                var aYear = parseInt(a.name.substring(a.name.length - 4), 10);
            }

            if (b.name.substring(0, 8) === 'AFUP Day') {
                var bYear = b.name.split(' ').splice(-2, 1).join('');
            } else {
                var bYear = parseInt(b.name.substring(b.name.length - 4), 10);
            }

            if (aYear < bYear) {
                return 1;
            }

            if (aYear > bYear) {
                return -1;
            }

            return 0;
        },
        operator: "and",
        templates: {
            header: "<h4>Évènement</h4>",
            item: refinementItemTemplate
        },
        showMore: {
            templates: {
                active: '<a class="ais-show-more ais-show-more__inactive">Voir moins</a>',
                inactive: '<a class="ais-show-more ais-show-more__inactive">Voir plus</a>'
            }
        }
    })
);

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-type',
        attributeName: 'type.label',
        operator: "and",
        templates: {
            header: "<h4>Format</h4>",
            item: refinementItemTemplate
        }
    })
);

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-language',
        operator: "and",
        attributeName: 'language.label',
        templates: {
            header: "<h4>Langue</h4>",
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
        },
        showMore: {
            limit: 20,
            templates: {
                active: '<a class="ais-show-more ais-show-more__inactive">Voir moins</a>',
                inactive: '<a class="ais-show-more ais-show-more__inactive">Voir plus</a>'
            }
        }
    })
);

search.addWidget(
    instantsearch.widgets.clearAll({
        container: '#refinement-clear',
        templates: {
            link: 'Supprimer les filtres'
        },
        autoHideContainer: true
    })
);

search.addWidget(
    instantsearch.widgets.pagination({
        container: '#pagination',
        labels : {
            first: '<i class="fa fa-angle-double-left"></i>',
            previous: '<i class="fa fa-angle-left"></i>',
            next: '<i class="fa fa-angle-right"></i>',
            last: '<i class="fa fa-angle-double-right"></i>'
        }
    })
);
search.start();
