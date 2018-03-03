<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Controller\SiteBaseController;
use AppBundle\TechLetter\Form\GenerateType;
use AppBundle\TechLetter\Generator;
use Symfony\Component\HttpFoundation\Request;

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
