<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Twig\ViewRenderer;
use Symfony\Component\HttpFoundation\Response;

final readonly class BecomeMemberAction
{
    public function __construct(
        private ViewRenderer $view,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/become_member.html.twig', [
            'membership_fee_natural_person' => AFUP_COTISATION_PERSONNE_PHYSIQUE,
            'membership_fee_legal_entity' => AFUP_COTISATION_PERSONNE_MORALE,
        ]);
    }
}
