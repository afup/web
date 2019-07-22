<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Assemblee_Generale;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\SeniorityComputer;
use AppBundle\Event\Model\Repository\EventRepository;
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
        $badges = [];

        $badgespath = __DIR__ . '/../../../htdocs/images/badges/';

        foreach ($this->getSpeakerYears() as $year) {
            $badgename = 'speaker' . $year;
            if (is_file($badgespath . $badgename . '.png')) {
                $badges[] = $badgename;
            }
        }

        $seniority = $this->get(SeniorityComputer::class)->compute($user);
        $maxBadgesSeniority = 10;

        for ($i = min($seniority, $maxBadgesSeniority); $i > 0; $i--) {
            $badges[] = $i . 'ans';
        }

        return $badges;
    }

    private function getSpeakerYears()
    {
        $events = $this->get('ting')->get(EventRepository::class)->getAllEventWithSpeakerEmail($this->getUser()->getEmail());

        $years = [];
        foreach ($events as $event) {
            $years[] = $event->getDateStart()->format('Y');
        }

        return $years;
    }
}
