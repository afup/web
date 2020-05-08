<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Personnes_Physiques;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\LegacyModelFactory;

class MemberController extends SiteBaseController
{
    const DAYS_BEFORE_CALL_TO_UPDATE = 15;

    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $generalMeetingFactory = $this->get(GeneralMeetingRepository::class);
        $personnesPhysiques = $this->get(LegacyModelFactory::class)->createObject(Personnes_Physiques::class);
        $cotisation = $personnesPhysiques->obtenirDerniereCotisation($user->getId());

        $dateFinCotisation = null;
        if ($cotisation) {
            $dateFinCotisation = \DateTime::createFromFormat('U', $cotisation['date_fin']);
        }

        $daysBeforeMembershipExpiration = $user->getDaysBeforeMembershipExpiration();

        return $this->render(
            ':site:member/index.html.twig',
            [
                'badges' => $this->get(BadgesComputer::class)->getBadges($user),
                'user' => $user,
                'has_member_subscribed_to_techletter' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($user),
                'membership_fee_call_to_update' => null === $daysBeforeMembershipExpiration || $daysBeforeMembershipExpiration < self::DAYS_BEFORE_CALL_TO_UPDATE,
                'has_up_to_date_membership_fee' => $user->hasUpToDateMembershipFee(),
                'office_label' => $user->getNearestOfficeLabel(),
                'has_general_meeting_planned' => $generalMeetingFactory->hasGeneralMeetingPlanned(),
                'has_user_rspved_to_next_general_meeting' => $generalMeetingFactory->hasUserRspvedToLastGeneralMeeting($user),
                'membershipfee_end_date' => $dateFinCotisation,
            ]
        );
    }
}
