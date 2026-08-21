<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Entity\Meetup;
use AppBundle\Indexation\Meetups\GraphQL\QueryGroupsResponse;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MeetupClient
{
    private const int QUANTITY_PAST_EVENTS = 2;
    private const int QUANTITY_UPCOMING_EVENTS = 10;

    public function __construct(
        private HttpClientInterface $httpClient,
        private AntenneRepository $antenneRepository,
        private MapperBuilder $mapperBuilder,
    ) {}

    /**
     * @return Meetup[]
     */
    public function getEvents(?int $quantityOfPastEvents = null): array
    {
        $response = $this->httpClient->request('POST', '/gql-ext', [
            'body' => json_encode([
                'query' => $this->getEventsQuery(),
                'variables' => [
                    'quantityUpcoming' => self::QUANTITY_UPCOMING_EVENTS,
                    'quantityPast' => $quantityOfPastEvents ?? self::QUANTITY_PAST_EVENTS,
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

        foreach ($groupResponse->data as $codeAntenne => $group) {
            $edges = array_merge($group->upcomingEvents->edges, $group->pastEvents->edges);

            foreach ($edges as $edge) {
                $meetup = new Meetup();
                $meetup->id = (int) $edge->node->id;
                $meetup->titre = $edge->node->title;
                $meetup->description = $edge->node->description;
                $meetup->date = $edge->node->dateTime;
                $meetup->codeAntenne = $codeAntenne;
                $meetup->lieu = $edge->node->venue->name;
                $meetup->photoUrl = $edge->node->displayPhoto->standardUrl;

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
    venue { name }
    displayPhoto { standardUrl }
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
