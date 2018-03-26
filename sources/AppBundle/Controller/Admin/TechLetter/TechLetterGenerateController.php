<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\Form\GenerateType;
use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\HtmlParser;
use AppBundle\TechLetter\Model as Techletter;
use AppBundle\TechLetter\UrlCrawler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('admin/techletter/index.html.twig', [
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

        if (
            $request->getMethod() === Request::METHOD_POST
            && $this->isCsrfTokenValid('sendToMailchimp', $request->request->get('_csrf_token'))
        ) {
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

            $subject = sprintf("Veille de l'AFUP du %s", $sending->getSendingDate()->format('d/m/Y'));

            $template = $this->get('app.mailchimp_techletter_api')->createTemplate($subject . ' - Template', $mailContent);

            $response = $this->get('app.mailchimp_techletter_api')->createCampaign(
                $this->container->getParameter('mailchimp_techletter_list'),
                [
                    'template_id' => $template->get('id'),
                    'from_name' => "Pôle Veille de l'AFUP",
                    'reply_to' => 'pole-veille@afup.org',
                    'subject_line' => $subject,
                ]
            );

            $sending->setSentToMailchimp(true);
            $sendingRepository->save($sending);

            $message = "La campagne a été générée. Il faut maintenant <a href='https://us8.admin.mailchimp.com/campaigns/edit?id=" . $response['web_id'] . "' target='_blank'>se connecter sur Mailchimp</a> pour la valider/en planifier l'envoi";
            $this->addFlash('notice', $message);

            return $this->redirectToRoute('admin_techletter_index');
        }

        return $this->render(
            ':admin/techletter:generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
                'sending' => $sending,
                'form' => $form->createView(),
                'tech_letter' => $techLetter
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
            foreach ($richSchema as $schema) {
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

        $data = array_map(function ($value) {
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
        if ($this->isCsrfTokenValid('techletterPreview', $request->request->get('_csrf_token')) === false) {
            throw $this->createAccessDeniedException('You cannot edit this techletter');
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

    public function membersAction()
    {
        $subscribers = $this->get('ting')->get(TechletterSubscriptionsRepository::class)->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/members.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter'
        ]);
    }
}
