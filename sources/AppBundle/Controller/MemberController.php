<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;

class MemberController extends SiteBaseController
{
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            ':site:member/index.html.twig',
            [
                'badges' => $this->getBadges($user),
            ]
        );
    }

    private function getBadges(User $user)
    {
        $seniority = $this->get(SeniorityComputer::class)->compute($user);

        $badges = [];

        for ($i = $seniority; $i > 0; $i--) {
            $badges[] = $i . 'ans';
        }

        return $badges;
    }
}
