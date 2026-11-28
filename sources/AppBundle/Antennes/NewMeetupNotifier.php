<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Event\Entity\Meetup;
use AppBundle\Slack\MembresClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockAwareTrait;

/**
 * Annonce un nouveau meetup sur le canal Slack Membres de son antenne.
 */
final class NewMeetupNotifier
{
    use ClockAwareTrait;

    public function __construct(
        private readonly MembresClient $membresClient,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * Ne lève jamais d'exception : une erreur Slack ne doit pas bloquer l'import des meetups.
     */
    public function notify(Meetup $meetup, Antenne $antenne): void
    {
        try {
            if ($meetup->date < $this->now()) {
                return;
            }

            if ($antenne->slackMembresIdCanal === null || $antenne->slackMembresIdCanal === '') {
                return;
            }

            $url = $meetup->getUrl($antenne);

            $this->membresClient->postMessage(
                $antenne->slackMembresIdCanal,
                $this->buildText($meetup, $antenne, $url),
                $this->buildBlocks($meetup, $antenne, $url),
            );
        } catch (\Throwable $e) {
            $this->logError($meetup, $e);
        }
    }

    private function logError(Meetup $meetup, \Throwable $e): void
    {
        $this->logger->error('Impossible d\'annoncer le nouveau meetup {meetup} sur Slack : {message}', [
            'meetup' => $meetup->id,
            'antenne' => $meetup->codeAntenne,
            'message' => $e->getMessage(),
            'exception' => $e,
        ]);
    }

    private function buildText(Meetup $meetup, Antenne $antenne, string $url): string
    {
        return sprintf(
            'Nouveau meetup AFUP %s : %s, le %s – %s',
            $antenne->libelle,
            $meetup->titre ?? 'Meetup',
            $this->formatDate($meetup->date),
            $url,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildBlocks(Meetup $meetup, Antenne $antenne, string $url): array
    {
        $lines = [
            sprintf(':tada: Nouveau meetup AFUP %s', $this->escape($antenne->libelle)),
            sprintf('*<%s|%s>*', $url, $this->escape($meetup->titre ?? 'Meetup')),
            sprintf(':date: %s', $this->formatDate($meetup->date)),
        ];

        if ($meetup->lieu !== null && $meetup->lieu !== '') {
            $lines[] = sprintf(':round_pushpin: %s', $this->escape($meetup->lieu));
        }

        $section = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => implode("\n", $lines),
            ],
        ];

        if ($meetup->photoUrl !== null && $meetup->photoUrl !== '') {
            $section['accessory'] = [
                'type' => 'image',
                'image_url' => $meetup->photoUrl,
                'alt_text' => $meetup->titre ?? 'Meetup',
            ];
        }

        return [
            $section,
            [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => ['type' => 'plain_text', 'text' => 'S\'inscrire sur Meetup'],
                        'url' => $url,
                    ],
                ],
            ],
        ];
    }

    private function formatDate(\DateTimeImmutable $date): string
    {
        return $date->format('d/m/Y à H\hi');
    }

    private function escape(string $text): string
    {
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);
    }
}
