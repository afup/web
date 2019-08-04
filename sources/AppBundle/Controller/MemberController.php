<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Assemblee_Generale;
use Afup\Site\Association\Personnes_Physiques;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\BadgesComputer;
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

        $personnesPhysiques = $this->get(LegacyModelFactory::class)->createObject(Personnes_Physiques::class);
        $cotisation = $personnesPhysiques->obtenirDerniereCotisation($user->getId());

        $dateFinCotisation = null;
        if ($cotisation) {
            $dateFinCotisation = \DateTime::createFromFormat('U', $cotisation['date_fin']);
        }

        return $this->render(
            ':site:member/index.html.twig',
            [
                'badges' => $this->get(BadgesComputer::class)->getBadges($user),
                'user' => $user,
                'has_member_subscribed_to_techletter' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($user),
                'has_up_to_date_membership_fee' => $user->hasUpToDateMembershipFee(),
                'office_label' => $user->getNearestOfficeLabel(),
                'has_general_meeting_planned' => $assemblee_generale->hasGeneralMeetingPlanned(),
                'has_user_rspved_to_next_general_meeting' => $assemblee_generale->hasUserRspvedToLastGeneralMeeting($user),
                'membershipfee_end_date' => $dateFinCotisation,
            ]
        );
    }
}
