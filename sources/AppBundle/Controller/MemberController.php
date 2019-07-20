<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Assemblee_Generale;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\LegacyModelFactory;

class MemberController extends SiteBaseController
{
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $assemblee_generale = $this->get(LegacyModelFactory::class)->createObject(Assemblee_Generale::class);

        return $this->render(
            ':site:member/index.html.twig',
            [
                'badges' => $this->getBadges($user),
                'user' => $user,
                'has_member_subscribed_to_techletter' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($user),
                'has_up_to_date_membership_fee' => $user->hasUpToDateMembershipFee(),
                'office_label' => $user->getNearestOfficeLabel(),
                'has_general_meeting_planned' => $assemblee_generale->hasGeneralMeetingPlanned(),
                'has_user_rspved_to_next_general_meeting' => $assemblee_generale->hasUserRspvedToLastGeneralMeeting($user),
            ]
        );
    }

    private function getBadges(User $user)
    {
        $seniority = $this->get(SeniorityComputer::class)->compute($user);
        $maxBadgesSeniority = 10;

        $badges = [];

        for ($i = min($seniority, $maxBadgesSeniority); $i > 0; $i--) {
            $badges[] = $i . 'ans';
        }

        return $badges;
    }
}
