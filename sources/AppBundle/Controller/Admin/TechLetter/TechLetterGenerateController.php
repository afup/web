<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\Form\GenerateType;
use AppBundle\TechLetter\Generator;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\TechLetter\Model as Techletter;

class TechLetterGenerateController extends SiteBaseController
{
    public function generateAction(Request $request)
    {
        $form = $this->createForm(GenerateType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $techletterGenerator = new Generator();
            $techLetter = $techletterGenerator->generate($data['news'][0], $data['news'][1], array_filter($data['articles']), array_filter($data['projects']));

            $mailContent = $this
                ->render(
                    ':admin/techletter:mail_template.html.twig',
                    [
                        'tech_letter' => $techLetter,
                        'preview' => false
                    ]
                )
                ->getContent()
            ;

            $subject = sprintf("Veille de l'AFUP du %s", date('d/m/Y'));

            $template = $this->get('app.mailchimp_techletter_api')->createTemplate($subject . ' - Template', $mailContent);

            $this->get('app.mailchimp_techletter_api')->createCampaign(
                $this->container->getParameter('mailchimp_techletter_list'),
                [
                    'template_id' => $template->get('id'),
                    'from_name' => "Pôle Veille de l'AFUP",
                    'reply_to' => 'pole-veille@afup.org',
                    'subject_line' => $subject,
                ]
            );

            $message = "La campagne a été générée. Il faut maintenant se connecter sur Mailchimp pour la valider/en planifier l'envoi";
            $this->addFlash('notice', $message);

            return $this->redirectToRoute('admin_techletter_generate');
        }

        return $this->render(
            ':admin/techletter:generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
                'form' => $form->createView(),
                'tech_letter' => new Techletter\TechLetter(
                    new Techletter\News('http://symfony.com/blog/symfony-4-0-4-released', 'Sortie de Symfony 4.0.4', new \DateTimeImmutable('2018-01-29')),
                    null, //new Techletter\News('https://laravel-news.com/laravel-5-6', 'Sortie de Laravel 5.6', new \DateTimeImmutable('2018-02-07')),
                    [
                        new Techletter\Article(
                            'https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
                            'Normalizing composer.json - Andreas Möller',
                            'localheinz.com',
                            4,
                            'If you are using composer, you have probably modified composer.json at least once to keep things nice and tidy...'
                        ),
                        new Techletter\Article(
                            'https://mwop.net/blog/2018-01-23-psr-15.html',
                            'PSR-15 - Matthew Weier O\'phinney',
                            'mwop.net',
                            9,
                            'Yesterday, following a unanimous vote from its Core Committee, PHP-FIG formally accepted the proposed PSR-15, HTTP Server Handlers standard...'
                        ),
                        new Techletter\Article(
                            'http://sergeyzhuk.me/2018/02/02/why-asynchronous-php/',
                            'Asynchronous PHP: Why?',
                            'sergeyzhuk.me',
                            7,
                            'Asynchronous programming is on demand today. Especially in web-development where responsiveness of the application plays a huge role...'
                        ),
                        new Techletter\Article(
                            'https://murze.be/introducing-browsershot-v3-the-best-way-to-convert-html-to-pdfs-and-images',
                            'Introducing Browsershot v3 - murze.be',
                            'murze.be',
                            5,
                            'To convert html to a pdf or an image using wkhtmltopdf and wkhtmltoimage tends to be the popular option. Unfortunately those tools contain an outdated ...'
                        ),
                    ],
                    [
                        new Techletter\Project('https://github.com/angeloskath/php-nlp-tools', 'angeloskath/php-nlp-tools', 'Natural Language Processing Tools in PHP'),
                        new Techletter\Project('https://github.com/eleme/geohash', 'eleme/geohash', 'php geohash encoder/decoder'),
                        new Techletter\Project('https://github.com/novaway/elasticsearch-client', 'novaway/elasticsearch-client', 'A lightweight PHP 7.0+ client for Elasticsearch'),
                        new Techletter\Project('https://github.com/jenssegers/date', 'jenssegers/date', 'A library to help you work with dates in multiple languages'),
                    ]
                )
            ]
        );
    }


    public function createTemplateAction()
    {

    }

    public function retrieveDataAction(Request $request)
    {

    }

    public function previewAction(Request $request)
    {
        $techletter = Techletter\TechLetterFactory::createTechLetterFromJson($request->request->get('techletter'));

        //$techletter = Techletter\TechLetterFactory::createTechLetterFromJson('{"firstNews":{"url":"http:\/\/symfony.com\/blog\/symfony-4-0-4-released","title":"Sortie de Symfony 4.0.4","date":"Mon, 29 Jan 2018 00:00:00 +0100"},"secondNews":null,"articles":[{"url":"https:\/\/localheinz.com\/blog\/2018\/01\/15\/normalizing-composer.json\/","title":"Normalizing composer.json - Andreas M\u00f6ller","host":"localheinz.com","readingTime":4,"excerpt":"If you are using composer, you have probably modified composer.json at least once to keep things nice and tidy..."},{"url":"https:\/\/mwop.net\/blog\/2018-01-23-psr-15.html","title":"PSR-15 - Matthew Weier O\'phinney","host":"mwop.net","readingTime":9,"excerpt":"Yesterday, following a unanimous vote from its Core Committee, PHP-FIG formally accepted the proposed PSR-15, HTTP Server Handlers standard..."},{"url":"http:\/\/sergeyzhuk.me\/2018\/02\/02\/why-asynchronous-php\/","title":"Asynchronous PHP: Why?","host":"sergeyzhuk.me","readingTime":7,"excerpt":"Asynchronous programming is on demand today. Especially in web-development where responsiveness of the application plays a huge role..."},{"url":"https:\/\/murze.be\/introducing-browsershot-v3-the-best-way-to-convert-html-to-pdfs-and-images","title":"Introducing Browsershot v3 - murze.be","host":"murze.be","readingTime":5,"excerpt":"To convert html to a pdf or an image using wkhtmltopdf and wkhtmltoimage tends to be the popular option. Unfortunately those tools contain an outdated ..."}],"projects":[{"url":"https:\/\/github.com\/angeloskath\/php-nlp-tools","name":"angeloskath\/php-nlp-tools","description":"Natural Language Processing Tools in PHP"},{"url":"https:\/\/github.com\/eleme\/geohash","name":"eleme\/geohash","description":"php geohash encoder\/decoder"},{"url":"https:\/\/github.com\/novaway\/elasticsearch-client","name":"novaway\/elasticsearch-client","description":"A lightweight PHP 7.0+ client for Elasticsearch"},{"url":"https:\/\/github.com\/jenssegers\/date","name":"jenssegers\/date","description":"A library to help you work with dates in multiple languages"}]}');
        return $this->render('admin/techletter/mail_template.html.twig', [
            'preview' => true,
            'tech_letter' => $techletter /*new Techletter\TechLetter(
                new Techletter\News('http://symfony.com/blog/symfony-4-0-4-released', 'Sortie de Symfony 4.0.4', new \DateTimeImmutable('2018-01-29')),
                null, //new Techletter\News('https://laravel-news.com/laravel-5-6', 'Sortie de Laravel 5.6', new \DateTimeImmutable('2018-02-07')),
                [
                    new Techletter\Article(
                        'https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
                        'Normalizing composer.json - Andreas Möller',
                        'localheinz.com',
                        4,
                        'If you are using composer, you have probably modified composer.json at least once to keep things nice and tidy...'
                    ),
                    new Techletter\Article(
                        'https://mwop.net/blog/2018-01-23-psr-15.html',
                        'PSR-15 - Matthew Weier O\'phinney',
                        'mwop.net',
                        9,
                        'Yesterday, following a unanimous vote from its Core Committee, PHP-FIG formally accepted the proposed PSR-15, HTTP Server Handlers standard...'
                    ),
                    new Techletter\Article(
                        'http://sergeyzhuk.me/2018/02/02/why-asynchronous-php/',
                        'Asynchronous PHP: Why?',
                        'sergeyzhuk.me',
                        7,
                        'Asynchronous programming is on demand today. Especially in web-development where responsiveness of the application plays a huge role...'
                    ),
                    new Techletter\Article(
                        'https://murze.be/introducing-browsershot-v3-the-best-way-to-convert-html-to-pdfs-and-images',
                        'Introducing Browsershot v3 - murze.be',
                        'murze.be',
                        5,
                        'To convert html to a pdf or an image using wkhtmltopdf and wkhtmltoimage tends to be the popular option. Unfortunately those tools contain an outdated ...'
                    ),
                ],
                [
                    new Techletter\Project('https://github.com/angeloskath/php-nlp-tools', 'angeloskath/php-nlp-tools', 'Natural Language Processing Tools in PHP'),
                    new Techletter\Project('https://github.com/eleme/geohash', 'eleme/geohash', 'php geohash encoder/decoder'),
                    new Techletter\Project('https://github.com/novaway/elasticsearch-client', 'novaway/elasticsearch-client', 'A lightweight PHP 7.0+ client for Elasticsearch'),
                    new Techletter\Project('https://github.com/jenssegers/date', 'jenssegers/date', 'A library to help you work with dates in multiple languages'),
                ]
            )*/
        ]);
    }
}
