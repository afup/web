var search = instantsearch({
    appId: document.head.querySelector("[name=algolia_appid]").content,
    apiKey: document.head.querySelector("[name=algolia_apikey]").content,
    indexName: 'afup_meetups',
    urlSync: {}
});

search.addWidget(
    instantsearch.widgets.searchBox({
        container: '#search-box',
        placeholder: 'Rechercher un meetup...',
        poweredBy: true

    })
);

search.addWidget(
    instantsearch.widgets.hits({
        hitsPerPage: 14,
        container: '#hits-container',
        templates: {
            empty: "Pas de résultat",
            item: function(data) {
                var content = ''
                    + '<div class="container event-line">'
                ;

                content += `<div class="col-sm-6 col-md-1 meetups-office-logo"><img src="${ data.office.logo_url }" /></div>`;

                content += `<div class="col-sm-6 col-md-2 meetups-meetup-date">
                                ${data.office.label} <br />
                                <span style="font-size: 1.2em">${data.day_month}<br />${data.year}</span>
                            </div>`
                ;

                content += `<div class="col-md-${ data.is_upcoming ? '6' : '9' }">
                    <div class="talk-list-title-container meetups-list-title-container"><a href="${data.event_url}"><h2>${data.label}</h2></a></div>`
                ;

                if ('undefined' !== typeof data.venue) {
                    content += `${data.venue.name}<br />${data.venue.address_1 || ''}<br />${data.venue.city || ''}<br />`;
                }
                content += `<a href="#" class="description-toggler" data-toggled-html="Masquer la description">Voir la description</a>`;

                content += `<div class="event-description" style="display:none">${data.description}`;
                if ('undefined' !== typeof data.twitter) {
                    content += `<div><i class="fa fa-twitter" aria-hidden="true"></i> <a href="http://twitter.com/${data.twitter}">@${data.twitter}</a></div>`
                }
                content += '</div>';


                content += '</div>';

                if (data.is_upcoming) {
                    content += '<div class="col-sm-12 col-md-3 meetups-register">';
                    content += `<a href="${data.event_url}" class="button">S'inscrire</a>`;
                    content += '</div>';
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

    content += '<label>' + data.name +'</label>';

    if (data.count) {
        content += ' <span class="talk-list-refinement-count-badge">' + data.count + "</span>";
    }

    return content;
};


search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-office',
        attributeName: 'office.label',
        operator: "or",
        templates: {
            header: "<h4>Antenne</h4>",
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
    instantsearch.widgets.toggle({
        container: '#refinement-is-upcoming',
        attributeName: 'is_upcoming',
        label: 'Meetup à venir',
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
        container: '#refinement-year',
        attributeName: 'year',
        operator: "or",
        templates: {
            header: "<h4>Année</h4>",
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

$(document).ready(function() {
    $('#hits-container').on('click', '.description-toggler', function(e) {
        e.preventDefault();
        var toggler = $(this);
        var line = toggler.parents('.event-line');
        var description = $('.event-description', line);
        description.toggle();
        var togglerHtml = toggler.html();
        toggler.html(toggler.data('toggled-html'))
        toggler.data('toggled-html', togglerHtml)
    });
});
