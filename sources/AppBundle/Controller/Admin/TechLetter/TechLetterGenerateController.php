<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\Form\GenerateType;
use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\HtmlParser;
use AppBundle\TechLetter\UrlCrawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\TechLetter\Model as Techletter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TechLetterGenerateController extends SiteBaseController
{
    /**
     * @see https://fr.wikipedia.org/wiki/Mot_par_minute
     */
    const WORD_READ_BY_MINUTES = 250;

    public function indexAction(Request $request)
    {
        $repository = $this->get('app.techletter_sending_repository');
        $techLetters = $repository->getAll();
        $form = $this->createForm(SendingType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $techletter = $form->getData();
            $repository->save($techletter);

            return $this->redirectToRoute('admin_techletter_generate', ['id' => $techletter->getId()]);
        }

        return $this->render('admin/techletter/index.html.twig',[
            'title' => "Veille de l'AFUP",
            'techletters' => $techLetters,
            'form' => $form->createView()
        ]);
    }

    public function generateAction($techletterId, Request $request)
    {
        $sendingRepository = $this->get('app.techletter_sending_repository');
        /**
         * @var $sending Techletter\Sending
         */
        $sending = $sendingRepository->get($techletterId);
        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }

        $techLetter = Techletter\TechLetterFactory::createTechLetterFromJson($sending->getTechletter());

        $form = $this->createForm(GenerateType::class);
        $form->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            $data = $form->getData();

            /*$techletterGenerator = new Generator();
            $techLetter = $techletterGenerator->generate($data['news'][0], $data['news'][1], array_filter($data['articles']), array_filter($data['projects']));
            */
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
                'sending' => $sending,
                'form' => $form->createView(),
                'tech_letter' => $techLetter /*new Techletter\TechLetter(
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
            ]
        );
    }

    public function retrieveDataAction(Request $request)
    {
        $url = $request->request->get('url');
        if ($url === null) {
            throw new BadRequestHttpException('Undefined url parameter');
        }

        /**
         * @todo create a specific class
         */

        $urlInfo = parse_url($url);

        $urlCrawler = new UrlCrawler();
        $html = $urlCrawler->crawlUrl($url);

        $parser = new HtmlParser($html);

        /**
         * Certaines données sont représentées sous 2 titres car les différents modèles utilisent des noms différents pour
         * des choses similaires
         * @todo fix it
         */
        $data = [
            'title' => substr($parser->getTitle(), 0, 250),
            'name' => substr($parser->getTitle(), 0, 250),
            'excerpt' => $parser->getMeta('description'),
            'description' => $parser->getMeta('description'),
            'host' => $urlInfo['host']
        ];

        $richSchema = $parser->getRichSchema();


        $listOfTypes = [
            "NewsArticle",
            "Report",
            "ScholarlyArticle",
            "SocialMediaPosting",
            "TechArticle",
            "Article",
            "BlogPosting",
        ];

        if ($richSchema !== false) {
            foreach($richSchema as $schema) {
                if (
                    ! isset($schema['@type'])
                    || !in_array($schema["@type"], $listOfTypes)
                ) {
                    continue;
                }

                if (isset($schema['datePublished'])) {
                    $date = new \DateTimeImmutable($schema['datePublished']);
                    $data['date'] = $date->format('Y-m-d');
                }
                if (isset($schema['articleBody'])) {
                    $body = strip_tags($schema['articleBody']);
                    $data['readingTime'] = floor(str_word_count($body) / self::WORD_READ_BY_MINUTES);
                }
            }
        }

        $data = array_map(function($value) {
            return trim($value);
        }, $data);

        return new JsonResponse($data);
    }

    public function previewAction(Request $request)
    {
        $sendingId = $request->request->getInt('techletterId');
        $repository = $this->get('app.techletter_sending_repository');
        /**
         * @var $sending Techletter\Sending
         */
        $sending = $repository->get($sendingId);

        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }

        $techletter = Techletter\TechLetterFactory::createTechLetterFromJson($request->request->get('techletter'));

        if ($techletter instanceof Techletter\TechLetter) {
            // @todo could be better elsewhere
            $sending->setTechletter(json_encode($techletter->jsonSerialize()));
            $repository->save($sending);
        }

        return $this->render('admin/techletter/mail_template.html.twig', [
            'preview' => true,
            'tech_letter' => $techletter
        ]);
    }
}
