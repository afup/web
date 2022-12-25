<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Controller\SiteBaseController;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use AppBundle\TechLetter\DataExtractor;
use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\Model as Techletter;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TechLetterGenerateController extends SiteBaseController
{
    /** @var SendingRepository */
    private $sendingRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var TechletterSubscriptionsRepository */
    private $techletterSubscriptionsRepository;
    /** @var Mailer */
    private $mailer;

    public function __construct(
        SendingRepository $sendingRepository,
        TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        Mailer $mailer
    ) {
        $this->sendingRepository = $sendingRepository;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->techletterSubscriptionsRepository = $techletterSubscriptionsRepository;
        $this->mailer = $mailer;
    }

    public function indexAction(Request $request)
    {
        $techLetters = $this->sendingRepository->getAll();
        $form = $this->formFactory->create(SendingType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $techletter = $form->getData();
            $this->sendingRepository->save($techletter);

            return new RedirectResponse($this->urlGenerator->generate('admin_techletter_generate', [
                'techletterId' => $techletter->getId(),
            ]));
        }

        return $this->render('admin/techletter/index.html.twig', [
            'title' => "Veille de l'AFUP",
            'techletters' => $techLetters,
            'form' => $form->createView(),
        ]);
    }

    public function historyAction()
    {
        $history = [];
        foreach ($this->sendingRepository->getAll() as $sending) {
            $defaultColumns = [
                'date' => $sending->getSendingDate(),
            ];

            $techLetter = Techletter\TechLetterFactory::createTechLetterFromJson($sending->getTechletter());

            if (null !== ($firstNews = $techLetter->getFirstNews())) {
                $url = $firstNews->getUrl();
                $history[] = $defaultColumns + [
                    'type' => 'First news',
                    'url' => $url,
                    'title' => $firstNews->getTitle()
                ];
            }

            if (null !== ($secondNewsNews = $techLetter->getSecondNews())) {
                $url = $secondNewsNews->getUrl();
                $history[] = $defaultColumns + [
                    'type' => 'second news',
                    'url' => $url,
                    'title' => $secondNewsNews->getTitle(),
                ];
            }

            foreach ($techLetter->getArticles() as $article) {
                $history[] = $defaultColumns + [
                    'type' => 'article',
                    'url' => $article->getUrl(),
                    'title' => $article->getTitle(),
                ];
            }

            foreach ($techLetter->getProjects() as $project) {
                $history[] = $defaultColumns + [
                    'type' => 'project',
                    'url' => $project->getUrl(),
                    'title' => $project->getName(),
                ];
            }
        }

        return $this->render('admin/techletter/history.html.twig', [
            'title' => "Veille de l'AFUP",
            'history' => $history,
        ]);
    }

    public function generateAction($techletterId, Request $request)
    {
        /**
         * @var $sending Techletter\Sending
         */
        $sending = $this->sendingRepository->get($techletterId);
        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }

        $techLetter = Techletter\TechLetterFactory::createTechLetterFromJson($sending->getTechletter());

        // Save the date
        if ($request->getMethod() === Request::METHOD_POST
            && $this->isCsrfTokenValid('techletterDate', $request->request->get('_csrf_token'))) {
            $sending->setSendingDate(new \DateTime($request->request->get('sendingDate')));
            $this->sendingRepository->save($sending);
            $this->addFlash('notice', 'Date mise à jour');

            return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $techletterId]);
        }

        if (
            $request->getMethod() === Request::METHOD_POST &&
            ($this->isCsrfTokenValid('sendToMailchimp', $request->request->get('_csrf_token')) ||
             $this->isCsrfTokenValid('sendToMailchimpAndSchedule', $request->request->get('_csrf_token')))
        ) {
            // Ne pas planifier l'envoi dans le passé
            $limitDatetime = new \DateTime('+5 min');
            if ($sending->getSendingDate() < $limitDatetime) {
                $sending->setSendingDate($limitDatetime);
            }

            $mailContent = $this
                ->render(
                    ':admin/techletter:mail_template.html.twig',
                    [
                        'tech_letter' => $techLetter,
                        'preview' => false
                    ]
                )
                ->getContent();

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

            if ($this->isCsrfTokenValid('sendToMailchimpAndSchedule', $request->request->get('_csrf_token'))) {
                try {
                    $this->get('app.mailchimp_techletter_api')->scheduleCampaign($response['id'], $sending->getSendingDate());
                    $message = sprintf("Newsletter envoyée, verrouillée et planifiée pour être envoyée à %s (%s) sur Mailchimp",
                        $sending->getSendingDate()->format('d/m/Y H:i'),
                        $sending->getSendingDate()->getTimezone()->getName()
                    );
                    $this->addFlash('notice', $message);
                } catch (\Exception $exception) {
                    $error = json_decode($exception->getMessage(), false);
                    $message = sprintf('Erreur Mailchimp: "%s" (%s). La campagne a été générée.', $error->title, $error->detail);
                    $this->addFlash('error', $message);
                }
            } else {
                $message = "La campagne a été générée. Il faut maintenant <a href='https://us8.admin.mailchimp.com/campaigns/edit?id=" . $response['web_id'] . "' target='_blank'>se connecter sur Mailchimp</a> pour la valider/en planifier l'envoi";
                $this->addFlash('notice', $message);
            }

            $sending->setArchiveUrl($response['long_archive_url']);
            $sending->setSentToMailchimp(true);
            $this->sendingRepository->save($sending);


            return $this->redirectToRoute('admin_techletter_index');
        }

        return $this->render(
            ':admin/techletter:generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
                'sending' => $sending,
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

        $dataExtractor = new DataExtractor();
        $data = $dataExtractor->extractDataForTechLetter($url);

        return new JsonResponse($data);
    }

    public function previewAction(Request $request)
    {
        $sendingId = $request->request->getInt('techletterId');
        /**
         * @var $sending Techletter\Sending
         */
        $sending = $this->sendingRepository->get($sendingId);

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
            $this->sendingRepository->save($sending);
        }

        return $this->render('admin/techletter/mail_template.html.twig', [
            'preview' => true,
            'tech_letter' => $techletter
        ]);
    }

    public function sendTestAction(Request $request)
    {
        $sendingId = $request->query->getInt('techletterId');
        /**
         * @var $sending Techletter\Sending
         */
        $sending = $this->sendingRepository->get($sendingId);

        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }

        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You send a test on a sent techletter');
        }

        $subject = sprintf("[Test] Veille de l'AFUP du %s", $sending->getSendingDate()->format('d/m/Y'));

        $techLetter = Techletter\TechLetterFactory::createTechLetterFromJson($sending->getTechletter());

        $message = new Message($subject, null, new MailUser($this->getParameter('techletter_test_email_address')));
        $this->mailer->renderTemplate($message,':admin/techletter:mail_template.html.twig', [
            'tech_letter' => $techLetter,
            'preview' => false,
        ]);
        $this->mailer->send($message);

        $this->addFlash('notice', 'Le mail de test a été envoyé');

        return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $sendingId]);
    }

    public function membersAction()
    {
        $subscribers = $this->techletterSubscriptionsRepository->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/members.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter'
        ]);
    }
}
