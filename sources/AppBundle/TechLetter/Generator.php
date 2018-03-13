<?php

namespace AppBundle\TechLetter;

use AppBundle\TechLetter\Model\Article;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Project;
use AppBundle\TechLetter\Model\TechLetter;

class Generator
{
    /**
     * @return TechLetter
     */
    public function generate($firstNewsUrl, $secondNewsUrl, $articleUrls, $projectsUrls)
    {
        return new TechLetter(
            new News(
                'Sortie de Symfony 4.0.4',
                'http://symfony.com/blog/symfony-4-0-4-released',
                new \DateTimeImmutable('2018-01-29')
            ),
            new News(
                'Sortie de Laravel 5.6',
                'https://laravel-news.com/laravel-5-6',
                new \DateTimeImmutable('2018-02-07')
            ),
            [
                new Article(
                    'https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
                    'Normalizing composer.json - Andreas Möller',
                    'localheinz.com',
                    4,
                    'If you are using composer, you have probably modified composer.json at least once to keep things nice and tidy...'
                ),
                new Article(
                    'https://mwop.net/blog/2018-01-23-psr-15.html',
                    'PSR-15 - Matthew Weier O\'phinney',
                    'mwop.net',
                    9,
                    'Yesterday, following a unanimous vote from its Core Committee, PHP-FIG formally accepted the proposed PSR-15, HTTP Server Handlers standard...'
                ),
                new Article(
                    'https://murze.be/introducing-browsershot-v3-the-best-way-to-convert-html-to-pdfs-and-images',
                    'Introducing Browsershot v3 - murze.be',
                    'murze.be',
                    5,
                    'To convert html to a pdf or an image using wkhtmltopdf and wkhtmltoimage tends to be the popular option. Unfortunately those tools contain an outdated ...'
                ),
                new Article(
                    'http://sergeyzhuk.me/2018/02/02/why-asynchronous-php/',
                    'Asynchronous PHP: Why?',
                    'sergeyzhuk.me',
                    7,
                    'Asynchronous programming is on demand today. Especially in web-development where responsiveness of the application plays a huge role...'
                ),
            ],
            [
                new Project(
                    'https://github.com/angeloskath/php-nlp-tools',
                    'angeloskath/php-nlp-tools',
                    'Natural Language Processing Tools in PHP'
                ),
                new Project(
                    'https://github.com/eleme/geohash',
                    'eleme/geohash',
                    'php geohash encoder/decoder'
                ),
                new Project(
                    'https://github.com/novaway/elasticsearch-client',
                    'novaway/elasticsearch-client',
                    'A lightweight PHP 7.0+ client for Elasticsearch'
                ),
                new Project(
                    'https://github.com/jenssegers/date',
                    'jenssegers/date',
                    'A library to help you work with dates in multiple languages'
                )
            ]
        );
    }
}
