<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Mailchimp\Mailchimp;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use AppBundle\TechLetter\Model\TechLetterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GenerateAction extends AbstractController
{
    public function __construct(
        private readonly SendingRepository $sendingRepository,
        #[Autowire('@app.mailchimp_techletter_api')]
        private readonly Mailchimp $mailchimp,
        private readonly TechLetterFactory $techLetterFactory,
        #[Autowire(env: 'MAILCHIMP_TECHLETTER_LIST')]
        private readonly string $mailchimpTechletterList,
    ) {}

    public function __invoke($techletterId, Request $request): Response
    {
        $sending = $this->sendingRepository->get($techletterId);
        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }

        $techLetter = $this->techLetterFactory->createTechLetterFromJson($sending->getTechletter());

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
                    ],
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
                ],
            );

            if ($this->isCsrfTokenValid('sendToMailchimpAndSchedule', $request->request->get('_csrf_token'))) {
                try {
                    $this->mailchimp->scheduleCampaign($response['id'], $sending->getSendingDate());
                    $message = sprintf("Newsletter envoyée, verrouillée et planifiée pour être envoyée à %s (%s) sur Mailchimp",
                        $sending->getSendingDate()->format('d/m/Y H:i'),
                        $sending->getSendingDate()->getTimezone()->getName(),
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
            ],
        );
    }
}
