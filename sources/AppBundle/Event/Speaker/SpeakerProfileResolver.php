<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Cocur\Slugify\Slugify;
use Symfony\Contracts\Service\ResetInterface;

use function Symfony\Component\String\u;

/**
 * Les personnes (afup_speaker_person) qui ont au moins un talk publié, avec le slug enregistré en base.
 *
 * Une personne regroupe ses lignes speaker (une par édition) par afup_conferenciers.person_id.
 * Une ligne sans personne n'a pas de page : elle n'apparaît pas tant qu'elle n'est pas rattachée.
 *
 * L'index repose sur une requête légère (sans hydratation d'entités), faite une fois
 * par requête HTTP ; le profil complet d'une personne est chargé à la demande, par ses lignes speaker.
 */
final class SpeakerProfileResolver implements ResetInterface
{
    /**
     * @var array{
     *     bySlug: array<string, SpeakerIdentity>,
     *     byPersonId: array<int, string>,
     *     byLabel: array<string, string|null>,
     * }|null
     */
    private ?array $index = null;

    private readonly Slugify $slugify;

    public function __construct(private readonly SpeakerRepository $speakerRepository)
    {
        // Le même slugifier que Talk::getSlug(), pour le tri.
        $this->slugify = new Slugify();
    }

    /**
     * Libellé normalisé : sans espaces superflus, sans accents, en minuscules.
     */
    public static function normalizeLabel(string $label): string
    {
        return u($label)->ascii()->lower()->collapseWhitespace()->toString();
    }

    /**
     * Toutes les personnes ayant au moins un talk publié, triées par nom puis prénom.
     *
     * @return list<SpeakerIdentity>
     */
    public function getIdentities(): array
    {
        return array_values($this->getIndex()['bySlug']);
    }

    public function getIdentityBySlug(string $slug): ?SpeakerIdentity
    {
        return $this->getIndex()['bySlug'][$slug] ?? null;
    }

    /**
     * La personne qui porte ce libellé sur l'une de ses lignes speaker (par exemple la facette Algolia
     * `speakers.label`), aux accents, à la casse et aux espaces près. Null si personne ou plusieurs personnes le portent.
     */
    public function getIdentityByLabel(string $label): ?SpeakerIdentity
    {
        $index = $this->getIndex();
        $slug = $index['byLabel'][self::normalizeLabel($label)] ?? null;

        return null !== $slug ? $index['bySlug'][$slug] : null;
    }

    /**
     * La personne d'une ligne speaker de n'importe quelle édition, y compris une édition dont
     * les talks ne sont pas encore publiés. Null si la ligne n'est rattachée à aucune personne
     * ou si sa personne n'a aucun talk publié.
     */
    public function getIdentityForSpeaker(Speaker $speaker): ?SpeakerIdentity
    {
        $personId = $speaker->getPersonId();
        if (null === $personId) {
            return null;
        }

        $index = $this->getIndex();
        $slug = $index['byPersonId'][$personId] ?? null;

        return null !== $slug ? $index['bySlug'][$slug] : null;
    }

    /**
     * Le profil consolidé et les talks publiés de la personne qui porte ce slug.
     */
    public function getProfileBySlug(string $slug): ?SpeakerProfile
    {
        $identity = $this->getIdentityBySlug($slug);
        if (null === $identity) {
            return null;
        }

        $speakerTalks = $this->speakerRepository->getPublishedSpeakerTalks($identity->speakerIds);
        if ($speakerTalks === []) {
            return null;
        }

        return $this->buildProfile($identity, $speakerTalks);
    }

    public function reset(): void
    {
        $this->index = null;
    }

    /**
     * @return array{
     *     bySlug: array<string, SpeakerIdentity>,
     *     byPersonId: array<int, string>,
     *     byLabel: array<string, string|null>,
     * }
     */
    private function getIndex(): array
    {
        return $this->index ??= $this->buildIndex($this->speakerRepository->getPublishedTalkAppearances());
    }

    /**
     * @param list<array{speaker_id: int, person_id: int, slug: string, firstname: string, lastname: string, talk_id: int, starts_at: int}> $appearances
     *
     * @return array{
     *     bySlug: array<string, SpeakerIdentity>,
     *     byPersonId: array<int, string>,
     *     byLabel: array<string, string|null>,
     * }
     */
    private function buildIndex(array $appearances): array
    {
        // Du plus récent au plus ancien, quel que soit l'ordre de lecture.
        usort($appearances, static fn(array $a, array $b): int => [$b['starts_at'], $b['talk_id'], $b['speaker_id']] <=> [$a['starts_at'], $a['talk_id'], $a['speaker_id']]);

        /** @var array<int, non-empty-list<array{speaker_id: int, person_id: int, slug: string, firstname: string, lastname: string, talk_id: int, starts_at: int}>> $groups */
        $groups = [];
        $slugsByLabel = [];
        foreach ($appearances as $appearance) {
            $groups[$appearance['person_id']][] = $appearance;
            $slugsByLabel[self::normalizeLabel($appearance['firstname'] . ' ' . $appearance['lastname'])][$appearance['slug']] = true;
        }

        $index = ['bySlug' => [], 'byPersonId' => [], 'byLabel' => []];
        $identities = [];
        foreach ($groups as $personId => $personAppearances) {
            $latest = $personAppearances[0];
            $identities[] = new SpeakerIdentity(
                $latest['slug'],
                $latest['firstname'],
                $latest['lastname'],
                array_values(array_unique(array_column($personAppearances, 'speaker_id'))),
                new \DateTimeImmutable('@' . $latest['starts_at'])->setTimezone(new \DateTimeZone(date_default_timezone_get())),
            );
            $index['byPersonId'][$personId] = $latest['slug'];
        }

        // Un libellé porté par deux personnes distinctes ne désigne personne.
        foreach ($slugsByLabel as $label => $slugs) {
            $index['byLabel'][$label] = count($slugs) === 1 ? array_key_first($slugs) : null;
        }

        $sortKeys = array_map(fn(SpeakerIdentity $identity): string => $this->slugify->slugify($identity->lastname . ' ' . $identity->firstname) . ' ' . $identity->slug, $identities);
        array_multisort($sortKeys, SORT_STRING, $identities);

        foreach ($identities as $identity) {
            $index['bySlug'][$identity->slug] = $identity;
        }

        return $index;
    }

    /**
     * @param non-empty-list<SpeakerTalk> $speakerTalks
     */
    private function buildProfile(SpeakerIdentity $identity, array $speakerTalks): SpeakerProfile
    {
        // Du plus récent au plus ancien, quel que soit l'ordre de lecture.
        usort($speakerTalks, static fn(SpeakerTalk $a, SpeakerTalk $b): int => [
            $b->startsAt->getTimestamp(),
            $b->talk->getId(),
            $b->speaker->getId(),
        ] <=> [
            $a->startsAt->getTimestamp(),
            $a->talk->getId(),
            $a->speaker->getId(),
        ]);

        /** @var non-empty-array<int, Speaker> $speakers lignes speaker, de la plus récente à la plus ancienne */
        $speakers = [];
        $githubLogins = [];
        $talks = [];
        foreach ($speakerTalks as $speakerTalk) {
            $speakers[(int) $speakerTalk->speaker->getId()] ??= $speakerTalk->speaker;
            if (null !== $speakerTalk->githubLogin) {
                $githubLogins[] = $speakerTalk->githubLogin;
            }
            // Deux lignes de la même personne sur un même talk : le talk n'est listé qu'une fois.
            $talks[(int) $speakerTalk->talk->getId()] ??= $speakerTalk;
        }

        $socialLinks = array_filter([
            'twitter' => self::first($speakers, static fn(Speaker $speaker): string => $speaker->getUrlTwitter()),
            'mastodon' => self::first($speakers, static fn(Speaker $speaker): string => (string) $speaker->getUrlMastodon()),
            'bluesky' => self::first($speakers, static fn(Speaker $speaker): string => trim((string) $speaker->getBluesky()) !== '' ? (string) $speaker->getUrlBluesky() : ''),
            'linkedin' => self::first($speakers, static fn(Speaker $speaker): string => (string) $speaker->getUrlLinkedin()),
            'github' => self::first($githubLogins, static fn(string $login): string => '' !== $login ? 'https://github.com/' . $login : ''),
        ], static fn(?string $url): bool => null !== $url);

        $photoSpeakers = array_filter($speakers, static fn(Speaker $speaker): bool => trim((string) $speaker->getPhoto()) !== '');

        return new SpeakerProfile(
            $identity,
            reset($speakers),
            self::first($speakers, static fn(Speaker $speaker): string => trim((string) $speaker->getBiography())),
            reset($photoSpeakers) ?: null,
            $socialLinks,
            array_values($talks),
        );
    }

    /**
     * La première valeur non vide, dans l'ordre des éléments.
     *
     * @template T
     *
     * @param array<T> $items
     * @param callable(T): string $value
     */
    private static function first(array $items, callable $value): ?string
    {
        foreach ($items as $item) {
            $result = $value($item);
            if ('' !== $result) {
                return $result;
            }
        }

        return null;
    }
}
