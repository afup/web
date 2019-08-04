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
        $badgesCodes = $this->prepareBadgesCodes($user);

        $badges = $this->filterExistingBadges($badgesCodes);

        return $badges;
    }

    private function prepareBadgesCodes(User $user)
    {
        $badgesCodes = [];

        foreach ($this->getSpeakerYears($user) as $year) {
            $badgesCodes[] = 'speaker' . $year;
        }

        foreach ($this->getEvents($user) as $eventPath) {
            $badgesCodes[] = 'jy-etais-' . $eventPath;
        }

        $seniority = $this->seniorityComputer->compute($user);
        $maxBadgesSeniority = 10;

        for ($i = min($seniority, $maxBadgesSeniority); $i > 0; $i--) {
            $badgesCodes[] = $i . 'ans';
        }

        return $badgesCodes;
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

    private function getEvents(User $user)
    {
        $events = $this->eventRepository->getAllEventWithTegistrationEmail($user->getEmail());

        $eventsPaths = [];
        foreach ($events as $event) {
            $eventsPaths[] = $event->getPath();
        }

        return $eventsPaths;
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
