<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Model\Meetup;
use AppBundle\Indexation\Meetups\GraphQL\QueryGroupsResponse;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MeetupClient
{
    private const QUANTITY_PAST_EVENTS = 2;
    private const QUANTITY_UPCOMING_EVENTS = 10;

    public function __construct(
        private HttpClientInterface $httpClient,
        private AntenneRepository $antenneRepository,
        private MapperBuilder $mapperBuilder,
    ) {}

    /**
     * @return Meetup[]
     */
    public function getEvents(): array
    {
        $response = $this->httpClient->request('POST', '/gql-ext', [
            'body' => json_encode([
                'query' => $this->getEventsQuery(),
                'variables' => [
                    'quantityUpcoming' => self::QUANTITY_UPCOMING_EVENTS,
                    'quantityPast' => self::QUANTITY_PAST_EVENTS,
                ],
            ]),
        ]);

        /** @var QueryGroupsResponse $groupResponse */
        $groupResponse = $this->mapperBuilder
            ->allowSuperfluousKeys()
            ->supportDateFormats('Y-m-d\TH:i:sP')
            ->mapper()
            ->map(QueryGroupsResponse::class, Source::array($response->toArray()));

        $meetups = [];

        foreach ($groupResponse->data as $nameAntenne => $group) {
            $edges = array_merge($group->upcomingEvents->edges, $group->pastEvents->edges);

            foreach ($edges as $edge) {
                $meetup = new Meetup();
                $meetup->setId((int) $edge->node->id);
                $meetup->setTitle($edge->node->title);
                $meetup->setDescription($edge->node->description);
                $meetup->setDate($edge->node->dateTime);
                $meetup->setAntenneName($nameAntenne);

                if (($edge->node->venues[0] ?? null) !== null) {
                    $meetup->setLocation($edge->node->venues[0]->name);
                }

                $meetups[] = $meetup;
            }
        }

        return $meetups;
    }

    private function getEventsQuery(): string
    {
        $queries = [];

        foreach ($this->antenneRepository->getAll() as $antenne) {
            if ($antenne->meetup === null) {
                continue;
            }

            $queries[] = sprintf(
                "%s: group(id: %s) { ...GroupFragment }\n",
                $antenne->code,
                $antenne->meetup->id,
            );
        }

        $query = 'query($quantityUpcoming: Int, $quantityPast: Int) {
    %s
}

fragment EventFragment on Event {
    id
    title
    description
    dateTime
    venues { name }
}

fragment GroupFragment on Group {
    upcomingEvents: events(first: $quantityUpcoming, filter: {status: ACTIVE}) {
        edges {
            node { ... EventFragment }
        }
    }

    pastEvents: events(first: $quantityPast, sort: DESC, filter: {status: PAST}) {
        edges {
            node { ... EventFragment }
        }
    }
}';

        return sprintf($query, implode("\n", $queries));
    }
}
