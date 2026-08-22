/**
 * Historique des conférences — instantsearch.js 1.12.1
 *
 * Le rendu de cette page est intégralement produit ici : le template Twig ne fournit que des
 * conteneurs vides. Les classes visuelles sont donc posées de deux façons :
 *   - `cssClasses` : instantsearch AJOUTE ces classes à ses classes BEM `ais-*`
 *     (il ne les remplace pas), ce qui permet de styler ses propres wrappers.
 *   - `templates`  : le markup que l'on maîtrise entièrement.
 * Les classes reprennent celles des composants Twig (Card, Card:Section, Button) et du thème
 * de formulaire `templates/form_themes/tailwind.html.twig`, pour que la page reste alignée
 * sur `templates/site/news/list.html.twig` dont elle s'inspire.
 */

/* Icônes mingcute inlinées : on ne peut pas appeler <twig:ux:icon> depuis le JS. */
var icon = function (path, extraClass) {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" '
        + 'class="' + (extraClass || 'size-4') + '" aria-hidden="true">' + path + '</svg>';
};

var ICONS = {
    rss: '<path fill="currentColor" d="M5 17a2 2 0 1 1 0 4a2 2 0 0 1 0-4M4 10a1 1 0 0 0 0 2a8 8 0 0 1 8 8a1 1 0 1 0 2 0A10 10 0 0 0 4 10m0-7a1 1 0 1 0 0 2a15 15 0 0 1 15 15a1 1 0 1 0 2 0A17 17 0 0 0 4 3"/>',
    slides: '<path fill="currentColor" d="M20 3a1 1 0 1 1 0 2v9a3 3 0 0 1-3 3h-3.5l2.3 3.4a1 1 0 1 1-1.6 1.2L12 18.3l-2.2 3.3a1 1 0 1 1-1.6-1.2l2.3-3.4H7a3 3 0 0 1-3-3V5a1 1 0 0 1 0-2zM6 5v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V5z"/>',
    comments: '<path fill="currentColor" d="M12 2c5.5 0 10 3.8 10 8.5S17.5 19 12 19c-.9 0-1.8-.1-2.6-.3l-4 2.2A1 1 0 0 1 4 20v-3.2C2.7 15.2 2 13 2 10.5C2 5.8 6.5 2 12 2"/>',
    angleLeft: '<path fill="currentColor" d="M15.5 4.5a1 1 0 0 1 0 1.4L9.4 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0"/>',
    angleRight: '<path fill="currentColor" d="M8.5 4.5a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4L9.9 4.5a1 1 0 0 0-1.4 0"/>',
    angleDoubleLeft: '<path fill="currentColor" d="M12.7 4.5a1 1 0 0 1 0 1.4L6.6 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0m7 0a1 1 0 0 1 0 1.4L13.6 12l6.1 6.1a1 1 0 1 1-1.4 1.4l-6.8-6.8a1 1 0 0 1 0-1.4l6.8-6.8a1 1 0 0 1 1.4 0"/>',
    angleDoubleRight: '<path fill="currentColor" d="M11.3 4.5a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4l-6.8-6.8a1 1 0 0 0-1.4 0m-7 0a1 1 0 0 0 0 1.4l6.1 6.1l-6.1 6.1a1 1 0 1 0 1.4 1.4l6.8-6.8a1 1 0 0 0 0-1.4L5.7 4.5a1 1 0 0 0-1.4 0"/>'
};

/* Reprend les classes d'un champ du thème de formulaire (afup_field_class). */
var FIELD_CLASS = 'w-full rounded-lg border-2 border-neutre-300 bg-white px-3 h-10 text-sm '
    + 'text-gray-700 placeholder:text-neutre-400 focus:border-afup-500 focus:outline-none';

/* Reprend afup_toggle_class du thème de formulaire. */
var CHECKBOX_CLASS = 'size-4 shrink-0 rounded border-2 border-neutre-300 accent-ruby-500 '
    + 'focus:outline-afup-500 cursor-pointer';

/* Titre de section, calqué sur templates/components/Card/Section.html.twig */
var sectionHeader = function (title) {
    return '<h3 class="flex items-center gap-3 mb-2 text-sm font-medium leading-5 uppercase text-neutre-700">'
        + '<span class="w-1 h-4 rounded-full bg-afup-300 shrink-0" aria-hidden="true"></span>'
        + title + '</h3>';
};

/* Une ligne de filtre, calquée sur templates/components/Form/FilterList.html.twig */
var refinementItemTemplate = function (data) {
    return '<label class="flex items-center gap-2 cursor-pointer py-0.5">'
        + '<input type="checkbox" class="' + CHECKBOX_CLASS + '"' + (data.isRefined ? ' checked="checked"' : '') + ' />'
        + '<span class="text-sm font-normal text-neutre-700">' + data.name + '</span>'
        + (data.count ? '<span class="ml-auto text-xs text-neutre-400">' + data.count + '</span>' : '')
        + '</label>';
};

var refinementCssClasses = {
    list: 'flex flex-col gap-1',
    item: 'cursor-pointer'
};

var showMoreTemplates = {
    active: '<span class="inline-flex cursor-pointer items-center gap-1 text-sm text-ruby-700">Voir moins</span>',
    inactive: '<span class="inline-flex cursor-pointer items-center gap-1 text-sm text-ruby-700">Voir plus</span>'
};

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
        /* Attribution obligatoire sur l'offre gratuite d'Algolia. */
        poweredBy: {
            cssClasses: { root: 'mt-2 flex justify-end', link: 'inline-block' },
            template: '<div class="{{cssClasses.root}}">'
                + '<a class="{{cssClasses.link}}" href="{{url}}" target="_blank" rel="noopener">'
                + '<img src="/images/search-by-algolia.svg" alt="Search by Algolia" width="130" height="18" class="h-4 w-auto" />'
                + '</a></div>'
        },
        cssClasses: {
            root: 'w-full',
            input: FIELD_CLASS
        }
    })
);

search.addWidget(
    instantsearch.widgets.hits({
        hitsPerPage: 7,
        container: '#hits-container',
        cssClasses: {
            root: 'flex flex-col gap-4',
            empty: 'font-sans text-base text-neutre-700'
        },
        templates: {
            empty: 'Aucune conférence ne correspond à ces critères.',
            item: function (data) {
                var url = '/talks/' + data.url_key;
                var thumbnail = typeof data.video_id !== 'undefined'
                    ? 'https://img.youtube.com/vi/' + data.video_id + '/hqdefault.jpg'
                    : '/images/no_video.jpg';

                var links = '';
                var link = function (href, svg, label) {
                    return '<a href="' + href + '" target="_blank" rel="noopener"'
                        + ' class="inline-flex items-center gap-1.5 rounded-lg bg-afup-100 px-2 py-1 text-xs font-medium text-afup-500 hover:bg-afup-500 hover:text-white">'
                        + icon(svg, 'size-3.5') + label + '</a>';
                };

                if (typeof data.blog_post_url !== 'undefined') {
                    links += link(data.blog_post_url, ICONS.rss, 'Article de blog');
                }
                if (typeof data.slides_url !== 'undefined') {
                    links += link(data.slides_url, ICONS.slides, 'Slides');
                }
                if (typeof data.joindin_url !== 'undefined') {
                    links += link(data.joindin_url, ICONS.comments, 'Fiche joind.in');
                }

                return ''
                    + '<article class="flex flex-col sm:flex-row gap-4 rounded-2xl border-2 border-neutre-300 p-4">'
                    +   '<a href="' + url + '" class="shrink-0 w-full sm:w-44">'
                    +     '<img src="' + thumbnail + '" alt="" loading="lazy" class="w-full aspect-video rounded-xl object-cover bg-neutre-200" />'
                    +   '</a>'
                    +   '<div class="flex flex-col gap-2 grow">'
                    +     '<a href="' + url + '" class="font-titre text-lg leading-snug text-afup-800 hover:underline">' + data.title + '</a>'
                    +     '<p class="font-sans text-sm text-neutre-400">' + data.speakers_label + ' · ' + data.event.title + '</p>'
                    +     (links ? '<div class="flex flex-wrap items-center gap-2 mt-auto">' + links + '</div>' : '')
                    +   '</div>'
                    + '</article>';
            }
        }
    })
);

var toggles = [
    { container: '#refinement-has-video', attributeName: 'has_video', label: 'Avec vidéo' },
    { container: '#refinement-has-slides', attributeName: 'has_slides', label: 'Avec slides' },
    { container: '#refinement-has-blog-post', attributeName: 'has_blog_post', label: 'Avec article de blog' },
    { container: '#refinement-video-has-fr-subtitles', attributeName: 'video_has_fr_subtitles', label: 'Avec sous titres français' },
    { container: '#refinement-video-has-en-subtitles', attributeName: 'video_has_en_subtitles', label: 'Avec sous titres anglais' }
];

toggles.forEach(function (toggle) {
    search.addWidget(
        instantsearch.widgets.toggle({
            container: toggle.container,
            attributeName: toggle.attributeName,
            label: toggle.label,
            values: { on: true },
            autoHideContainer: false,
            cssClasses: refinementCssClasses,
            templates: { item: refinementItemTemplate }
        })
    );
});

/* Les évènements les plus récents en premier. */
var eventYear = function (name) {
    if (name.substring(0, 8) === 'AFUP Day') {
        return name.split(' ').splice(-2, 1).join('');
    }

    return parseInt(name.substring(name.length - 4), 10);
};

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-event',
        attributeName: 'event.title',
        sortBy: function (a, b) {
            var aYear = eventYear(a.name);
            var bYear = eventYear(b.name);

            if (aYear < bYear) {
                return 1;
            }

            if (aYear > bYear) {
                return -1;
            }

            return 0;
        },
        operator: 'and',
        cssClasses: refinementCssClasses,
        templates: {
            header: sectionHeader('Évènement'),
            item: refinementItemTemplate
        },
        showMore: { templates: showMoreTemplates }
    })
);

[
    { container: '#refinement-type', attributeName: 'type.label', title: 'Format' },
    { container: '#refinement-language', attributeName: 'language.label', title: 'Langue' }
].forEach(function (refinement) {
    search.addWidget(
        instantsearch.widgets.refinementList({
            container: refinement.container,
            attributeName: refinement.attributeName,
            operator: 'and',
            cssClasses: refinementCssClasses,
            templates: {
                header: sectionHeader(refinement.title),
                item: refinementItemTemplate
            }
        })
    );
});

search.addWidget(
    instantsearch.widgets.refinementList({
        container: '#refinement-speaker',
        attributeName: 'speakers.label',
        operator: 'and',
        cssClasses: refinementCssClasses,
        templates: {
            header: sectionHeader('Conférencier'),
            item: refinementItemTemplate
        },
        showMore: { limit: 20, templates: showMoreTemplates }
    })
);

search.addWidget(
    instantsearch.widgets.clearAll({
        container: '#refinement-clear',
        templates: {
            link: '<span class="cursor-pointer text-sm text-neutre-400 hover:text-neutre-700">Effacer les filtres</span>'
        },
        autoHideContainer: true
    })
);

/* Reprend le rendu de templates/site/pager.html.twig (composant Button). */
var PAGINATION_LINK = 'inline-flex items-center justify-center rounded-lg border whitespace-nowrap '
    + 'px-3 py-2 text-sm font-medium no-underline';

search.addWidget(
    instantsearch.widgets.pagination({
        container: '#pagination',
        cssClasses: {
            root: 'mx-auto flex w-full justify-center',
            list: 'flex flex-row flex-wrap gap-2 items-center',
            item: 'list-none',
            link: PAGINATION_LINK + ' bg-white text-neutre-700 border-neutre-300 hover:bg-afup-700 hover:text-white hover:border-afup-700',
            active: '[&>*]:bg-afup-100 [&>*]:text-afup-500 [&>*]:border-transparent',
            disabled: 'invisible'
        },
        labels: {
            first: icon(ICONS.angleDoubleLeft),
            previous: icon(ICONS.angleLeft),
            next: icon(ICONS.angleRight),
            last: icon(ICONS.angleDoubleRight)
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
