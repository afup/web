<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\Form\GenerateType;
use Symfony\Component\HttpFoundation\Request;

class TechLetterGenerateController extends SiteBaseController
{
    public function generateAction(Request $request)
    {
        $form = $this->createForm(GenerateType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mailContent = $this
                ->render(
                    ':admin/techletter:mail_template.html.twig',
                    [
                        'first_news' => [
                            'title' => 'Sortie de Symfony 4.0.4',
                            'url' => 'http://symfony.com/blog/symfony-4-0-4-released',
                            'date' => new \DateTimeImmutable('2018-01-29')

                        ],
                        'second_news' => [
                            'title' => 'Sortie de Laravel 5.6',
                            'url' => 'https://laravel-news.com/laravel-5-6',
                            'date' => new \DateTimeImmutable('2018-02-07')
                        ],
                        'articles' => [
                            [
                                'title' => 'Normalizing composer.json - Andreas Möller',
                                'url' => 'https://localheinz.com/blog/2018/01/15/normalizing-composer.json/',
                                'host' => 'localheinz.com',
                                'reading_time' => 4,
                                'excerpt' => 'If you are using composer, you have probably modified composer.json at least once to keep things nice and tidy...',
                            ],
                            [
                                'title' => 'PSR-15 - Matthew Weier O\'phinney',
                                'url' => 'https://mwop.net/blog/2018-01-23-psr-15.html',
                                'host' => 'mwop.net',
                                'reading_time' => 9,
                                'excerpt' => 'Yesterday, following a unanimous vote from its Core Committee, PHP-FIG formally accepted the proposed PSR-15, HTTP Server Handlers standard...',
                            ],
                            [
                                'title' => 'Introducing Browsershot v3 - murze.be',
                                'url' => 'https://murze.be/introducing-browsershot-v3-the-best-way-to-convert-html-to-pdfs-and-images',
                                'host' => 'murze.be',
                                'reading_time' => 5,
                                'excerpt' => 'To convert html to a pdf or an image using wkhtmltopdf and wkhtmltoimage tends to be the popular option. Unfortunately those tools contain an outdated ...',
                            ],
                            [
                                'title' => 'Asynchronous PHP: Why?',
                                'url' => 'http://sergeyzhuk.me/2018/02/02/why-asynchronous-php/',
                                'host' => 'sergeyzhuk.me',
                                'reading_time' => 7,
                                'excerpt' => 'Asynchronous programming is on demand today. Especially in web-development where responsiveness of the application plays a huge role...',
                            ],
                        ],
                        'projects' => [
                            [
                                'name' => 'angeloskath/php-nlp-tools',
                                'url' => 'https://github.com/angeloskath/php-nlp-tools',
                                'description' => 'Natural Language Processing Tools in PHP',
                            ],
                            [
                                'name' => 'eleme/geohash',
                                'url' => 'https://github.com/eleme/geohash',
                                'description' => 'php geohash encoder/decoder',
                            ],
                            [
                                'name' => 'novaway/elasticsearch-client',
                                'url' => 'https://github.com/novaway/elasticsearch-client',
                                'description' => 'A lightweight PHP 7.0+ client for Elasticsearch',
                            ],
                            [
                                'name' => 'jenssegers/date',
                                'url' => 'https://github.com/jenssegers/date',
                                'description' => 'A library to help you work with dates in multiple languages',
                            ],

                        ],
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
            ]
        );
    }
}
