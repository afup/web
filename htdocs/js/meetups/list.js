/* Classes reprises des composants Twig (Card, Card:Section, Button) et du thème de formulaire. */
var FIELD_CLASS = 'w-full rounded-lg border-2 border-neutre-300 bg-white px-3 h-10 text-sm text-gray-700 '
    + 'placeholder:text-neutre-400 focus:border-afup-500 focus:outline-none';
var CHECKBOX_CLASS = 'size-4 shrink-0 rounded border-2 border-neutre-300 accent-ruby-500 focus:outline-afup-500 cursor-pointer';
var LINK_CLASS = 'inline-flex items-center justify-center rounded-lg border whitespace-nowrap px-3 py-2 text-sm font-medium no-underline';

var svg = function (path, cls) {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="'
        + cls + '" aria-hidden="true">' + path + '</svg>';
};
var CHEVRON = '<path fill="currentColor" d="M12 15.5a1 1 0 0 1-.7-.3l-5-5a1 1 0 0 1 1.4-1.4l4.3 4.3l4.3-4.3a1 1 0 1 1 1.4 1.4l-5 5a1 1 0 0 1-.7.3"/>';
var ARROW = {
    prev: '<path fill="currentColor" d="M15.5 4.5a1 1 0 0 1 0 1.4L9.4 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0"/>',
    next: '<path fill="currentColor" d="M8.5 4.5a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4L9.9 4.5a1 1 0 0 0-1.4 0"/>',
    first: '<path fill="currentColor" d="M12.7 4.5a1 1 0 0 1 0 1.4L6.6 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0m7 0a1 1 0 0 1 0 1.4L13.6 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0"/>',
    last: '<path fill="currentColor" d="M11.3 4.5a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4l-6.8-6.8a1 1 0 0 0-1.4 0m-7 0a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4L5.7 4.5a1 1 0 0 0-1.4 0"/>'
};

/* instantsearch AJOUTE ces classes aux siennes (ais-*), il ne les remplace pas. */
var REFINEMENT_CSS = { list: 'flex flex-col gap-1', item: 'cursor-pointer' };
var SHOW_MORE = {
    active: '<span class="cursor-pointer text-sm text-ruby-700">Voir moins</span>',
    inactive: '<span class="cursor-pointer text-sm text-ruby-700">Voir plus</span>'
};
var sectionHeader = function (title) {
    return '<h3 class="flex items-center gap-3 mb-2 text-sm font-medium leading-5 uppercase text-neutre-700">'
        + '<span class="w-1 h-4 rounded-full bg-afup-300 shrink-0" aria-hidden="true"></span>' + title + '</h3>';
};

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
        /* Attribution obligatoire sur l'offre gratuite d'Algolia. */
        poweredBy: {
            cssClasses: { root: 'mt-2 flex justify-end', link: 'inline-block' },
            template: '<div class="{{cssClasses.root}}">'
                + '<a class="{{cssClasses.link}}" href="{{url}}" target="_blank" rel="noopener">'
                + '<img src="/images/search-by-algolia.svg" alt="Search by Algolia" width="130" height="18" class="h-4 w-auto" />'
                + '</a></div>'
        },
        cssClasses: { root: 'w-full', input: FIELD_CLASS }
    })
);

search.addWidget(
    instantsearch.widgets.hits({
        hitsPerPage: 14,
        container: '#hits-container',
        cssClasses: { root: 'flex flex-col gap-4', empty: 'font-sans text-base text-neutre-700' },
        templates: {
            empty: 'Aucun meetup ne correspond à ces critères.',
            item: function(data) {
                var twitter = ('undefined' !== typeof data.twitter)
                    ? '<a href="https://twitter.com/' + data.twitter + '" class="mt-2 inline-flex items-center gap-1.5 '
                      + 'text-sm text-afup-500 underline hover:no-underline">@' + data.twitter + '</a>'
                    : '';

                return ''
                    + '<article class="flex flex-col sm:flex-row gap-4 rounded-2xl border-2 border-neutre-300 p-4">'
                    +   '<div class="flex flex-row sm:flex-col items-center gap-3 sm:w-24 sm:shrink-0">'
                    +     '<div class="size-14 shrink-0 flex items-center justify-center rounded-2xl bg-neutre-200">'
                    +       '<img src="' + data.office.logo_url + '" alt="" loading="lazy" class="w-full h-full object-contain p-1.5" />'
                    +     '</div>'
                    +     '<div class="flex flex-col sm:items-center sm:text-center">'
                    +       '<span class="font-sans text-sm font-medium text-afup-800">' + data.office.label + '</span>'
                    +       '<span class="font-sans text-xs text-neutre-400">' + data.day_month + ' ' + data.year + '</span>'
                    +     '</div>'
                    +   '</div>'
                    +   '<div class="flex flex-col gap-2 grow min-w-0">'
                    +     '<a href="' + data.event_url + '" class="font-titre text-lg leading-snug text-afup-800 hover:underline">' + data.label + '</a>'
                    /* <details> natif : remplace le repli qui était géré en jQuery. */
                    +     '<details class="group mt-1">'
                    +       '<summary class="flex w-fit cursor-pointer items-center gap-1 text-sm text-ruby-700 list-none [&::-webkit-details-marker]:hidden">'
                    +         svg(CHEVRON, 'size-4 transition-transform group-open:rotate-180')
                    +         '<span class="group-open:hidden">Voir la description</span>'
                    +         '<span class="hidden group-open:inline">Masquer la description</span>'
                    +       '</summary>'
                    /* `prose` : la description arrive en HTML libre de l'API Meetup, et le reset
                       Tailwind a supprimé les marges de ses <p>. */
                    +       '<div class="mt-2 prose prose-sm max-w-none text-neutre-700">' + data.description + '</div>'
                    +       twitter
                    +     '</details>'
                    +   '</div>'
                    +   (data.is_upcoming
                        ? '<div class="sm:self-start sm:shrink-0">'
                          + '<a href="' + data.event_url + '" class="' + LINK_CLASS + ' bg-ruby-500 border-transparent '
                          + 'px-8 py-3 text-base font-semibold text-white hover:bg-ruby-700">S\'inscrire</a>'
                          + '</div>'
                        : '')
                    + '</article>';
            }
        }
    })
);


var refinementItemTemplate = function(data) {
    return '<label class="flex items-center gap-2 cursor-pointer py-0.5">'
        + '<input type="checkbox" class="' + CHECKBOX_CLASS + '"' + (data.isRefined ? ' checked="checked"' : '') + ' />'
        + '<span class="text-sm font-normal text-neutre-700">' + data.name + '</span>'
        + (data.count ? '<span class="ml-auto text-xs text-neutre-400">' + data.count + '</span>' : '')
        + '</label>';
};


search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-office',
        attributeName: 'office.label',
        operator: "or",
        cssClasses: REFINEMENT_CSS,
        templates: {
            header: sectionHeader('Antenne'),
            item: refinementItemTemplate
        },
        showMore: { templates: SHOW_MORE }
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
        cssClasses: REFINEMENT_CSS,
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
        cssClasses: REFINEMENT_CSS,
        templates: {
            header: sectionHeader('Année'),
            item: refinementItemTemplate
        },
        showMore: { limit: 20, templates: SHOW_MORE }
    })
);

search.addWidget(
    instantsearch.widgets.clearAll({
        container: '#refinement-clear',
        templates: {
            link: '<span class="cursor-pointer text-sm text-neutre-400 hover:text-neutre-700">Supprimer les filtres</span>'
        },
        autoHideContainer: true
    })
);

search.addWidget(
    instantsearch.widgets.pagination({
        container: '#pagination',
        cssClasses: {
            root: 'flex flex-row flex-wrap justify-center gap-2 items-center',
            item: 'list-none',
            link: LINK_CLASS + ' bg-white text-neutre-700 border-neutre-300 hover:bg-afup-700 hover:text-white hover:border-afup-700',
            active: '[&>*]:bg-afup-100 [&>*]:text-afup-500 [&>*]:border-transparent',
            disabled: 'invisible max-sm:hidden'
        },
        labels : {
            first: svg(ARROW.first, 'size-4'),
            previous: svg(ARROW.prev, 'size-4'),
            next: svg(ARROW.next, 'size-4'),
            last: svg(ARROW.last, 'size-4')
        }
    })
);

/* instantsearch 1.x n'émet pas aria-current sur la page courante. */
search.on('render', function () {
    document.querySelectorAll('#pagination .ais-pagination--item__page').forEach(function (item) {
        var link = item.querySelector('.ais-pagination--link');

        if (!link) {
            return;
        }

        if (item.classList.contains('ais-pagination--item__active')) {
            link.setAttribute('aria-current', 'page');
        } else {
            link.removeAttribute('aria-current');
        }
    });
});

search.start();
