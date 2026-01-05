<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Member;

use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\UserMembership\BadgesComputer;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\Security\Authentication;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public const DAYS_BEFORE_CALL_TO_UPDATE = 15;

    public function __construct(
        private readonly ViewRenderer $view,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly UserService $userService,
        private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        private readonly BadgesComputer $badgesComputer,
        private readonly TechletterSubscriptionsRepository $techletterSubscriptionsRepository,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(): Response
    {
        $user = $this->authentication->getAfupUser();
        $generalMeetingFactory = $this->generalMeetingRepository;
        $userService = $this->userService;
        $cotisation = $userService->getLastSubscription($user);

        $dateFinCotisation = null;
        if ($cotisation instanceof MembershipFee) {
            $dateFinCotisation = $cotisation->getEndDate();
        }

        $daysBeforeMembershipExpiration = $user->getDaysBeforeMembershipExpiration();

        $generalMeetingRepository = $this->generalMeetingRepository;
        $generalMeetingQuestionRepository = $this->generalMeetingQuestionRepository;

        $latestDate = $generalMeetingRepository->getLatestGeneralAssemblyDate();
        $hasGeneralMeetingPlanned = $generalMeetingFactory->hasGeneralMeetingPlanned();

        $displayLinkToGeneralMeetingVote = false;

        if ($hasGeneralMeetingPlanned
            && null !== $latestDate
            && ($latestDate->format('Y-m-d') === (new \DateTime('-1 day'))->format('Y-m-d'))
            && count($generalMeetingQuestionRepository->loadByDate($latestDate)) > 0
        ) {
            $displayLinkToGeneralMeetingVote = true;
        }

        return $this->view->render('site/member/index.html.twig', [
            'badges' => $this->badgesComputer->getBadges($user),
            'user' => $user,
            'has_member_subscribed_to_techletter' => $this->techletterSubscriptionsRepository->hasUserSubscribed($user),
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
