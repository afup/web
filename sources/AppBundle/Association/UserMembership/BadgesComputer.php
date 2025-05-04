<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\GeneralMeetingResponseRepository;
use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;

class BadgesComputer
{
    public function __construct(
        private readonly SeniorityComputer $seniorityComputer,
        private readonly EventRepository $eventRepository,
        private readonly UserBadgeRepository $userBadgeRepository,
        private readonly GeneralMeetingResponseRepository $generalMeetingResponseRepository,
    ) {
    }

    public function getBadges(User $user): array
    {
        $badgesInfos = $this->prepareBadgesInfos($user);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        return $this->filterExistingBadges($badgesInfos);
    }

    /**
     * @return array{date: mixed, id: mixed, tooltip: mixed}[]
     */
    private function getSpecificBadges(User $user): array
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

    public function getCompanyBadges(CompanyMember $companyMember): array
    {
        $badgesInfos = $this->prepareCompanyBadgesInfos($companyMember);

        $badgesInfos = $this->sortBadgesInfos($badgesInfos);

        $badges = $this->filterExistingBadges($badgesInfos);

        return $this->mapBadgesCodes($badges);
    }

    /**
     * @return array{date: (non-falsy-string & uppercase-string), code: non-falsy-string, tooltip: non-falsy-string}[]
     */
    private function prepareCompanyBadgesInfos(CompanyMember $companyMember): array
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

    /**
     * @return mixed[]
     */
    private function prepareBadgesInfos(User $user): array
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
            $isolateTownPattern = '/afupday(?P<year>\d{4})/';
            if (preg_match($isolateTownPattern, (string) $eventInfo['path'], $pathMatches) && $pathMatches['year'] >= 2022) {
                $code = 'jy-etais-afupday' . $pathMatches['year'];
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

        return array_merge($badgesCodes, $this->getSpecificBadges($user));
    }

    /**
     * @return mixed[]
     */
    private function mapBadgesCodes(array $badgesInfos): array
    {
        $badgesCodes = [];
        foreach ($badgesInfos as $badgeInfo) {
            $badgesCodes[] = $badgeInfo['code'];
        }

        return $badgesCodes;
    }

    private function sortBadgesInfos(array $badgesInfos): array
    {
        usort(
            $badgesInfos,
            fn (array $a, array $b): int => $b['date'] <=> $a['date']
        );

        return $badgesInfos;
    }

    /**
     * @return mixed[]
     */
    private function filterExistingBadges(array $badgesInfos): array
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

    /**
     * @return array{path: mixed, date: mixed, title: mixed}[]
     */
    private function getEventsInfos(User $user): array
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

    /**
     * @return mixed[]
     */
    private function getSpeakerYears(User $user): array
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

    /**
     * @return mixed[]
     */
    private function getGeneralMeetingYears(User $user): array
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
