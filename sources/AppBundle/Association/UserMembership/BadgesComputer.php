<?php

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;

class BadgesComputer
{
    /**
     * @var SeniorityComputer
     */
    private $seniorityComputer;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var UserBadgeRepository
     */
    private $userBadgeRepository;

    public function __construct(SeniorityComputer $seniorityComputer, EventRepository $eventRepository, UserBadgeRepository $userBadgeRepository)
    {
        $this->seniorityComputer = $seniorityComputer;
        $this->eventRepository = $eventRepository;
        $this->userBadgeRepository = $userBadgeRepository;
    }

    public function getBadges(User $user)
    {
        $badgesInfos = $this->prepareBadgesInfos($user);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        return $this->filterExistingBadges($badgesInfos);
    }

    private function getSpecificBadges(User $user)
    {
        $specific = [];

        $userBadges = $this->userBadgeRepository->findByUserId($user->getId());

        foreach ($userBadges as $userBadge) {
            $specific[] = [
                'date' => $userBadge->getIssuedAt()->format('Y-m-d'),
                'url' => $userBadge->getBadge()->getUrl(),
            ];
        }

        return $specific;
    }

    public function getCompanyBadges(CompanyMember $companyMember)
    {
        $badgesInfos = $this->prepareCompanyBadgesInfos($companyMember);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        $badgesCodes = $this->mapBadgesCodes($badgesInfos);

        $badges = $this->filterExistingBadges($badgesCodes);

        return $badges;
    }

    private function prepareCompanyBadgesInfos(CompanyMember $companyMember)
    {
        $seniorityInfos = $this->seniorityComputer->computeCompanyAndReturnInfos($companyMember);
        $maxBadgesSeniority = 10;

        $badgesCodes = [];

        for ($i = min($seniorityInfos['years'], $maxBadgesSeniority); $i > 0; $i--) {
            $badgesCodes[] = [
                'date' => ($seniorityInfos['first_year'] + $i) . '-01-01',
                'code' => $i . 'ans',
            ];
        }

        return $badgesCodes;
    }

    private function prepareBadgesInfos(User $user)
    {
        $badgesCodes = [];

        foreach ($this->getSpeakerYears($user) as $year) {
            $badgesCodes[] = [
                'date' => $year . '-01-01',
                'code' => 'speaker' . $year
            ];
        }

        foreach ($this->getEventsInfos($user) as $eventInfo) {
            $badgesCodes[] = [
                'date' => $eventInfo['date']->format('Y-m-d'),
                'code' => 'jy-etais-' . $eventInfo['path'],
            ];
        }

        $seniorityInfos = $this->seniorityComputer->computeAndReturnInfos($user);
        $maxBadgesSeniority = 10;

        for ($i = min($seniorityInfos['years'], $maxBadgesSeniority); $i > 0; $i--) {
            $badgesCodes[] = [
                'date' => ($seniorityInfos['first_year'] + $i) . '-01-01',
                'code' => $i . 'ans',
            ];
        }

        $badgesCodes = array_merge($badgesCodes, $this->getSpecificBadges($user));

        return $badgesCodes;
    }

    private function mapBadgesCodes(array $badgesInfos)
    {
        $badgesCodes = [];
        foreach ($badgesInfos as $badgeInfo) {
            $badgesCodes[] = $badgeInfo['code'];
        }

        return $badgesCodes;
    }

    private function sortBadgesInfos(array $badgesInfos)
    {
        usort(
            $badgesInfos,
            function (array $a, array $b) {
                if ($a['date'] == $b['date']) {
                    return 0;
                }
                return ($a['date'] < $b['date']) ? 1 : -1;
            }
        );

        return $badgesInfos;
    }

    private function filterExistingBadges(array $badgesInfos)
    {
        $badgespath = __DIR__ . '/../../../../htdocs/images/badges/';

        $filteredBadges = [];

        foreach ($badgesInfos as $badgeInfos) {
            if (isset($badgeInfos['url'])) {
                $filteredBadges[] = $badgeInfos;
            } else {
                if (!is_file($badgespath . $badgeInfos['code'] . '.png')) {
                    continue;
                }

                $filteredBadges[] = $badgeInfos;
            }
        }

        return $filteredBadges;
    }

    private function getEventsInfos(User $user)
    {
        $events = $this->eventRepository->getAllPastEventWithTegistrationEmail($user->getEmail());

        $eventInfos = [];
        foreach ($events as $event) {
            $eventInfos[] = [
                'path' => $event->getPath(),
                'date' => $event->getDateStart(),
            ];
        }

        return $eventInfos;
    }

    private function getSpeakerYears(User $user)
    {
        $events = $this->eventRepository->getAllPastEventWithSpeakerEmail($user->getEmail());

        $years = [];
        foreach ($events as $event) {
            $years[] = $event->getDateStart()->format('Y');
        }

        $years = array_unique($years);

        rsort($years);

        return $years;
    }
}
