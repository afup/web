<?php

namespace AppBundle\Controller\Website;

use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\WebsiteBlocks;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MemberController extends Controller
{
    const DAYS_BEFORE_CALL_TO_UPDATE = 15;

    private WebsiteBlocks $websiteBlocks;

    public function __construct(WebsiteBlocks $websiteBlocks)
    {
        $this->websiteBlocks = $websiteBlocks;
    }

    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $generalMeetingFactory = $this->get(GeneralMeetingRepository::class);
        $userService = $this->get(UserService::class);
        $cotisation = $userService->getLastSubscription($user);

        $dateFinCotisation = null;
        if ($cotisation) {
            $dateFinCotisation = \DateTime::createFromFormat('U', $cotisation['date_fin']);
        }

        $daysBeforeMembershipExpiration = $user->getDaysBeforeMembershipExpiration();

        $generalMeetingRepository = $this->get(GeneralMeetingRepository::class);
        $generalMeetingQuestionRepository = $this->get(GeneralMeetingQuestionRepository::class);

        $latestDate = $generalMeetingRepository->getLatestDate();
        $hasGeneralMeetingPlanned = $generalMeetingFactory->hasGeneralMeetingPlanned($latestDate);

        $displayLinkToGeneralMeetingVote = false;

        if ($hasGeneralMeetingPlanned
            && null !== $latestDate
            && ($latestDate->format('Y-m-d') == (new \DateTime())->format('Y-m-d'))
            && count($generalMeetingQuestionRepository->loadByDate($latestDate)) > 0
        ) {
            $displayLinkToGeneralMeetingVote = true;
        }

        return $this->websiteBlocks->render('site/member/index.html.twig', [
            'badges' => $this->get(BadgesComputer::class)->getBadges($user),
            'user' => $user,
            'has_member_subscribed_to_techletter' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($user),
            'membership_fee_call_to_update' => null === $daysBeforeMembershipExpiration || $daysBeforeMembershipExpiration < self::DAYS_BEFORE_CALL_TO_UPDATE,
            'has_up_to_date_membership_fee' => $user->hasUpToDateMembershipFee(),
            'office_label' => $user->getNearestOfficeLabel(),
            'has_general_meeting_planned' => $hasGeneralMeetingPlanned,
            'has_user_rspved_to_next_general_meeting' => $generalMeetingFactory->hasUserRspvedToLastGeneralMeeting($user),
            'membershipfee_end_date' => $dateFinCotisation,
            'display_link_to_general_meeting_vote' => $displayLinkToGeneralMeetingVote,
        ]);
    }
}
