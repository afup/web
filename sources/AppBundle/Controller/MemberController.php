<?php

namespace AppBundle\Controller;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
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
                'user' => $user,
                'has_member_subscribed_to_techletter' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($user),
                'has_up_to_date_membership_fee' => $user->hasUpToDateMembershipFee(),
                'office_label' => $user->getNearestOfficeLabel(),
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
