<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;

class AdminTechletterController extends SiteBaseController
{
    public function indexAction()
    {
        $subscribers = $this->get('ting')->get(TechletterSubscriptionsRepository::class)->getAllSubscriptionsWithUser();
        return $this->render('admin/techletter/index.html.twig', [
            'subscribers' => $subscribers,
            'title' => 'Liste des abonnés à la techletter'
        ]);
    }
}
