<?php

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Controller\SiteBaseController;

class TechLetterGenerateController extends SiteBaseController
{
    public function generateAction()
    {
        $mailContent = $this->render(':admin/techletter:mail_template.html.twig')->getContent();

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

        return $this->render(
            ':admin/techletter:generate.html.twig',
            [
                'title' => "Veille de l'AFUP",
            ]
        );
    }
}
