<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\DataExtractor;
use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\Model as Techletter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TechLetterGenerateController extends SiteBaseController
{
    public function indexAction(Request $request)
    {
        $repository = $this->get('app.techletter_sending_repository');
        $techLetters = $repository->getAll();
        $form = $this->createForm(SendingType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $techletter = $form->getData();
            $repository->save($techletter);

            return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $techletter->getId()]);
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
