<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use AppBundle\Mailchimp\Mailchimp;
use AppBundle\TechLetter\DataExtractor;
use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\Model as Techletter;
use AppBundle\TechLetter\Model\News;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TechLetterGenerateController extends AbstractController
{
    public function __construct(
        private readonly SendingRepository $sendingRepository,
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        private readonly Mailer $mailer,
        private readonly Mailchimp $mailchimp,
        private readonly string $techletterTestEmailAddress,
        private readonly string $mailchimpTechletterList,
    ) {
    }

    public function index(Request $request)
    {
        $techLetters = $this->sendingRepository->getAllOrderedByDateDesc();
        $form = $this->createForm(SendingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $techletter = $form->getData();
            $this->sendingRepository->save($techletter);

            return $this->redirectToRoute('admin_techletter_generate', [
                'techletterId' => $techletter->getId(),
            ]);
        }

        return $this->render('admin/techletter/index.html.twig', [
            'title' => "Veille de l'AFUP",
            'techletters' => $techLetters,
            'form' => $form->createView(),
        ]);
    }

    public function history(): Response
    {
        $history = [];
        foreach ($this->sendingRepository->getAll() as $sending) {
            $defaultColumns = [
                'date' => $sending->getSendingDate(),
            ];

            $techLetter = Techletter\TechLetterFactory::createTechLetterFromJson($sending->getTechletter());

            if (($firstNews = $techLetter->getFirstNews()) instanceof News) {
                $url = $firstNews->getUrl();
                $history[] = $defaultColumns + [
                    'type' => 'First news',
                    'url' => $url,
                    'title' => $firstNews->getTitle(),
                ];
            }

            if (($secondNewsNews = $techLetter->getSecondNews()) instanceof News) {
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

    public function generate($techletterId, Request $request)
    {
        /**
         * @var Techletter\Sending $sending
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
                    'admin/techletter/mail_template.html.twig',
                    [
                        'tech_letter' => $techLetter,
                        'preview' => false,
                    ]
                )
                ->getContent();

            $subject = sprintf("Veille de l'AFUP du %s", $sending->getSendingDate()->format('d/m/Y'));

            $template = $this->mailchimp->createTemplate($subject . ' - Template', $mailContent);

            $response = $this->mailchimp->createCampaign(
                $this->mailchimpTechletterList,
                [
                    'template_id' => $template['id'],
                    'from_name' => "Pôle Veille de l'AFUP",
                    'reply_to' => 'pole-veille@afup.org',
                    'subject_line' => $subject,
                ]
            );

            if ($this->isCsrfTokenValid('sendToMailchimpAndSchedule', $request->request->get('_csrf_token'))) {
                try {
                    $this->mailchimp->scheduleCampaign($response['id'], $sending->getSendingDate());
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
            'admin/techletter/generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
                'sending' => $sending,
                'tech_letter' => $techLetter,
            ]
        );
    }

    public function retrieveData(Request $request)
    {
        $url = $request->request->get('url');
        if ($url === null) {
            throw new BadRequestHttpException('Undefined url parameter');
        }

        $dataExtractor = new DataExtractor();
        $data = $dataExtractor->extractDataForTechLetter($url);

        return new JsonResponse($data);
    }

    public function preview(Request $request): Response
    {
        $sendingId = $request->request->getInt('techletterId');
        /**
         * @var Techletter\Sending $sending
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
        // @todo could be better elsewhere
        $sending->setTechletter(json_encode($techletter->jsonSerialize()));
        $this->sendingRepository->save($sending);

        return $this->render('admin/techletter/mail_template.html.twig', [
            'preview' => true,
            'tech_letter' => $techletter,
        ]);
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $sendingId = $request->query->getInt('techletterId');
        /**
         * @var Techletter\Sending $sending
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

        $message = new Message($subject, null, new MailUser($this->techletterTestEmailAddress));
        $this->mailer->renderTemplate($message,'admin/techletter/mail_template.html.twig', [
            'tech_letter' => $techLetter,
            'preview' => false,
        ]);
        $this->mailer->send($message);

        $this->addFlash('notice', 'Le mail de test a été envoyé');

        return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $sendingId]);
    }

    public function members(): Response
    {
        $subscribers = $this->techletterSubscriptionsRepository->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/members.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter',
        ]);
    }
}
