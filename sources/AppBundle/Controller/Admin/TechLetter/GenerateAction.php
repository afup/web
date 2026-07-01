<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Mailchimp\Mailchimp;
use AppBundle\TechLetter\Model\TechLetterFactory;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GenerateAction extends AbstractController
{
    public function __construct(
        private readonly EnvoiRepository $envoiRepository,
        #[Autowire('@app.mailchimp_techletter_api')]
        private readonly Mailchimp $mailchimp,
        private readonly TechLetterFactory $techLetterFactory,
        #[Autowire(env: 'MAILCHIMP_TECHLETTER_LIST')]
        private readonly string $mailchimpTechletterList,
    ) {}

    public function __invoke($techletterId, Request $request): Response
    {
        $envoi = $this->envoiRepository->find($techletterId);
        if ($envoi === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($envoi->envoyeMailchimp === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }

        $techLetter = $this->techLetterFactory->createTechLetterFromJson($envoi->contenu);

        // Save the date
        if ($request->getMethod() === Request::METHOD_POST
            && $this->isCsrfTokenValid('techletterDate', $request->request->get('_csrf_token'))) {
            $envoi->dateEnvoi = new \DateTime($request->request->get('sendingDate'));
            $this->envoiRepository->save($envoi);
            $this->addFlash('notice', 'Date mise à jour');

            return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $techletterId]);
        }

        if (
            $request->getMethod() === Request::METHOD_POST
            && ($this->isCsrfTokenValid('sendToMailchimp', $request->request->get('_csrf_token'))
                || $this->isCsrfTokenValid('sendToMailchimpAndSchedule', $request->request->get('_csrf_token')))
        ) {
            // Ne pas planifier l'envoi dans le passé
            $limitDatetime = new \DateTime('+5 min');
            if ($envoi->dateEnvoi < $limitDatetime) {
                $envoi->dateEnvoi = $limitDatetime;
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

            $subject = sprintf("Veille de l'AFUP du %s", $envoi->dateEnvoi->format('d/m/Y'));

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
                    $this->mailchimp->scheduleCampaign($response['id'], $envoi->dateEnvoi);
                    $message = sprintf("Newsletter envoyée, verrouillée et planifiée pour être envoyée à %s (%s) sur Mailchimp",
                        $envoi->dateEnvoi->format('d/m/Y H:i'),
                        $envoi->dateEnvoi->getTimezone()->getName(),
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

            $envoi->urlArchive = $response['long_archive_url'];
            $envoi->envoyeMailchimp = true;
            $this->envoiRepository->save($envoi);

            return $this->redirectToRoute('admin_techletter_index');
        }

        return $this->render(
            'admin/techletter/generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
                'envoi' => $envoi,
                'tech_letter' => $techLetter,
            ],
        );
    }
}
