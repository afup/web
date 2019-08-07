<?php

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;

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

    public function __construct(SeniorityComputer $seniorityComputer, EventRepository $eventRepository)
    {
        $this->seniorityComputer = $seniorityComputer;
        $this->eventRepository = $eventRepository;
    }

    public function getBadges(User $user)
    {
        $badgesInfos = $this->prepareBadgesInfos($user);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        $badgesCodes = $this->mapBadgesCodes($badgesInfos);

        $badges = $this->filterExistingBadges($badgesCodes);

        return $badges;
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

    private function filterExistingBadges(array $badgesCodes)
    {
        $badgespath = __DIR__ . '/../../../../htdocs/images/badges/';

        $filteredBadges = [];

        foreach ($badgesCodes as $badgesCode) {
            if (!is_file($badgespath . $badgesCode . '.png')) {
                continue;
            }

            $filteredBadges[] = $badgesCode;
        }

        return $filteredBadges;
    }

    private function getEventsInfos(User $user)
    {
        $events = $this->eventRepository->getAllEventWithTegistrationEmail($user->getEmail());

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
        $events = $this->eventRepository->getAllEventWithSpeakerEmail($user->getEmail());

        $years = [];
        foreach ($events as $event) {
            $years[] = $event->getDateStart()->format('Y');
        }

        $years = array_unique($years);

        rsort($years);

        return $years;
    }
}
