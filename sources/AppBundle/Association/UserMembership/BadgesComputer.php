<?php

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\GeneralMeetingResponseRepository;
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

    /**
     * @var GeneralMeetingResponseRepository
     */
    private $generalMeetingResponseRepository;

    public function __construct(SeniorityComputer $seniorityComputer, EventRepository $eventRepository, UserBadgeRepository $userBadgeRepository, GeneralMeetingResponseRepository $generalMeetingResponseRepository)
    {
        $this->seniorityComputer = $seniorityComputer;
        $this->eventRepository = $eventRepository;
        $this->userBadgeRepository = $userBadgeRepository;
        $this->generalMeetingResponseRepository = $generalMeetingResponseRepository;
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
                'id' => $userBadge->getBadge()->getId(),
                'tooltip' => $userBadge->getBadge()->getLabel(),
            ];
        }

        return $specific;
    }

    public function getCompanyBadges(CompanyMember $companyMember)
    {
        $badgesInfos = $this->prepareCompanyBadgesInfos($companyMember);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        $badges = $this->filterExistingBadges($badgesInfos);

        $badges = $this->mapBadgesCodes($badges);

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
                'tooltip' => 'Membre depuis ' . $i . ' an' . ($i > 1 ? 's' : ''),
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
                'code' => 'speaker' . $year,
                'tooltip' => 'Speaker de l\'année ' . $year,
            ];
        }

        foreach ($this->getEventsInfos($user) as $eventInfo) {
            // First use afup_forum.path as is
            $code = 'jy-etais-' . $eventInfo['path'];

            // a partir de 2022 les badges pour l'AFUP Day ne sont plus par ville mais
            // identiques pour toutes les villes (cela permet de les créer en amont et
            // en simplifie la maintenance).
            $isolateTownPattern = '/afupday(?P<year>[0-9]{4})/';
            if (preg_match($isolateTownPattern, $eventInfo['path'], $pathMatches)) {
                if ($pathMatches['year'] >= 2022) {
                    $code = 'jy-etais-afupday' . $pathMatches['year'];
                }
            }

            $badgesCodes[$code] = [
                'date' => $eventInfo['date']->format('Y-m-d'),
                'code' => $code,
                'tooltip' => 'Participation à l\'évènement ' . $eventInfo['title'],
            ];
        }


        foreach ($this->getGeneralMeetingYears($user) as $date) {
            $badgesCodes[] = [
                'date' => $date->format('Y-m-d'),
                'code' => 'ag-' . $date->format('Y'),
                'tooltip' => 'Participation à l\'AG de ' . $date->format('Y'),
            ];
        }

        $seniorityInfos = $this->seniorityComputer->computeAndReturnInfos($user);
        $maxBadgesSeniority = 10;

        for ($i = min($seniorityInfos['years'], $maxBadgesSeniority); $i > 0; $i--) {
            $badgesCodes[] = [
                'date' => ($seniorityInfos['first_year'] + $i) . '-01-01',
                'code' => $i . 'ans',
                'tooltip' => 'Membre depuis ' . $i . ' an' . ($i > 1 ? 's' : ''),
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
            fn (array $a, array $b) => $b['date'] <=> $a['date']
        );

        return $badgesInfos;
    }

    private function filterExistingBadges(array $badgesInfos)
    {
        $badgespath = __DIR__ . '/../../../../htdocs/images/badges/';

        $filteredBadges = [];

        foreach ($badgesInfos as $badgeInfos) {
            if (isset($badgeInfos['id'])) {
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
                'title' => $event->getTitle(),
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

    private function getGeneralMeetingYears(User $user)
    {
        $responses = $this->generalMeetingResponseRepository->getByUser($user);
        $currentTimestamp = (new \DateTime())->format('U');

        $dates = [];
        foreach ($responses as $response) {
            if (false === $response->isPresent()) {
                continue;
            }

            $date = $response->getDate();

            if ($date->format('U') > $currentTimestamp) {
                continue;
            }

            $dates[] = $date;
        }

        return $dates;
    }
}
