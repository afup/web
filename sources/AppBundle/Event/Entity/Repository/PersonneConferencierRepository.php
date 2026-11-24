<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\PersonneConferencier;
use AppBundle\Event\Speaker\SpeakerSearchResult;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * Les requêtes renvoient les ids des lignes conférencier, que l'appelant charge avec SpeakerRepository::find().
 *
 * @extends EntityRepository<PersonneConferencier>
 */
class PersonneConferencierRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonneConferencier::class);
    }

    public function slugExists(string $slug): bool
    {
        return $this->count(['slug' => $slug]) > 0;
    }

    /**
     * Personne de la ligne la plus récente rattachée à ce compte GitHub.
     */
    public function findLatestIdByGithubUser(int $githubUserId): ?int
    {
        $personneId = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT c.personne_id
            FROM afup_conferenciers c
            LEFT JOIN afup_forum f ON f.id = c.id_forum
            WHERE c.user_github = :githubUserId AND c.personne_id IS NOT NULL
            ORDER BY f.date_debut DESC, c.conferencier_id DESC
            LIMIT 1',
            ['githubUserId' => $githubUserId],
        );

        return $personneId === false ? null : self::toInt($personneId);
    }

    /**
     * Ligne de la personne dans l'événement ; avec $withoutGithubUser, seulement une ligne saisie sans compte
     * GitHub (par exemple par l'admin).
     */
    public function findSpeakerIdInEvent(int $personneId, int $eventId, bool $withoutGithubUser = false): ?int
    {
        $speakerId = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT conferencier_id FROM afup_conferenciers
            WHERE personne_id = :personneId AND id_forum = :eventId'
            . ($withoutGithubUser ? ' AND user_github IS NULL' : '') . '
            ORDER BY conferencier_id
            LIMIT 1',
            ['personneId' => $personneId, 'eventId' => $eventId],
        );

        return $speakerId === false ? null : self::toInt($speakerId);
    }

    /**
     * Lignes de la personne, de la plus récente à la plus ancienne.
     *
     * @return list<int>
     */
    public function findSpeakerIds(int $personneId): array
    {
        return array_map(self::toInt(...), $this->getEntityManager()->getConnection()->fetchFirstColumn(
            'SELECT c.conferencier_id FROM afup_conferenciers c
            LEFT JOIN afup_forum f ON f.id = c.id_forum
            WHERE c.personne_id = :personneId
            ORDER BY f.date_debut DESC, c.conferencier_id DESC',
            ['personneId' => $personneId],
        ));
    }

    /**
     * Lignes non rattachées qui ressemblent à la personne : même compte GitHub ou même « prénom nom »
     * (comparaison insensible à la casse et aux accents). Le rattachement reste une décision humaine.
     *
     * @return list<int>
     */
    public function findSuggestedSpeakerIds(int $personneId): array
    {
        return array_map(self::toInt(...), $this->getEntityManager()->getConnection()->fetchFirstColumn(
            "SELECT c.conferencier_id FROM afup_conferenciers c
            LEFT JOIN afup_forum f ON f.id = c.id_forum
            WHERE c.personne_id IS NULL
            AND EXISTS (
                SELECT 1 FROM afup_conferenciers p
                WHERE p.personne_id = :personneId
                AND (
                    p.user_github = c.user_github
                    OR CONCAT_WS(' ', TRIM(p.prenom), TRIM(p.nom)) COLLATE utf8mb4_unicode_ci = CONCAT_WS(' ', TRIM(c.prenom), TRIM(c.nom)) COLLATE utf8mb4_unicode_ci
                )
            )
            ORDER BY f.date_debut DESC, c.conferencier_id DESC",
            ['personneId' => $personneId],
        ));
    }

    /**
     * Recherche tous événements confondus : une ligne par personne, plus une ligne par conférencier non rattaché.
     * Chaque résultat pointe vers la ligne la plus récente.
     *
     * @return list<SpeakerSearchResult>
     */
    public function searchAcrossEvents(string $search, int $limit = 50): array
    {
        $search = trim($search);
        if ($search === '') {
            return [];
        }

        $match = "CONCAT_WS(' ', m.prenom, m.nom, m.nom, m.prenom, m.email, m.societe) COLLATE utf8mb4_unicode_ci LIKE :search";
        /** @var list<array{conferencier_id: int|string, id_forum: int|string, personne_id: int|string|null, prenom: string, nom: string, societe: string|null, email: string|null, titre: string|null, slug: string|null}> $rows */
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            "SELECT c.conferencier_id, c.id_forum, c.personne_id, c.prenom, c.nom, c.societe, c.email, f.titre, p.slug
            FROM afup_conferenciers c
            LEFT JOIN afup_forum f ON f.id = c.id_forum
            LEFT JOIN afup_conferenciers_personnes p ON p.id = c.personne_id
            WHERE c.conferencier_id IN (SELECT m.conferencier_id FROM afup_conferenciers m WHERE $match)
            OR c.personne_id IN (SELECT m.personne_id FROM afup_conferenciers m WHERE m.personne_id IS NOT NULL AND $match)
            ORDER BY f.date_debut DESC, c.conferencier_id DESC",
            ['search' => '%' . $search . '%'],
        );

        $groups = [];
        foreach ($rows as $row) {
            $key = $row['personne_id'] !== null ? 'p' . $row['personne_id'] : 's' . $row['conferencier_id'];
            if (!isset($groups[$key])) {
                $groups[$key] = ['row' => $row, 'events' => []];
            }
            $groups[$key]['events'][(int) $row['id_forum']] = true;
        }

        $results = [];
        foreach (array_slice($groups, 0, $limit) as $group) {
            $row = $group['row'];
            $results[] = new SpeakerSearchResult(
                (int) $row['conferencier_id'],
                $row['personne_id'] !== null ? (int) $row['personne_id'] : null,
                $row['slug'],
                $row['prenom'],
                $row['nom'],
                $row['societe'],
                $row['email'],
                $row['titre'],
                count($group['events']),
            );
        }

        return $results;
    }

    /**
     * Déplace les lignes conférencier de $source vers $target puis supprime $source (son slug est perdu).
     */
    public function merge(PersonneConferencier $target, PersonneConferencier $source): void
    {
        $this->getEntityManager()->wrapInTransaction(function () use ($target, $source): void {
            $this->getEntityManager()->getConnection()->executeStatement(
                'UPDATE afup_conferenciers SET personne_id = :target WHERE personne_id = :source',
                ['target' => $target->id, 'source' => $source->id],
            );
            $this->getEntityManager()->remove($source);
        });
    }

    private static function toInt(mixed $value): int
    {
        Assert::integerish($value);

        return (int) $value;
    }
}
