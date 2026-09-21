<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Vote;
use AppBundle\Event\Model\Event;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Vote>
 */
final class VoteRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    /**
     * @return array{votes: int}
     */
    public function getNumberOfVotesByEvent(Event $event): array
    {
        $row = $this->getEntityManager()->getConnection()->fetchAssociative(
            'SELECT COUNT(vote.id) AS votes
            FROM afup_sessions_vote_github AS vote
            LEFT JOIN afup_sessions AS session ON session.session_id = vote.session_id
            WHERE session.id_forum = :eventId',
            ['eventId' => $event->getId()],
        );

        return ['votes' => (int) ($row['votes'] ?? 0)];
    }

    /**
     * Votes d'un évènement avec le titre du talk et le login du votant places
     * sous forme de tableaux imbriques consommables par le template Twig.
     *
     * @return list<array<string, mixed>>
     */
    public function getVotesByEvent(int $eventId): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT vote.id, vote.session_id, vote.user, vote.comment, vote.vote,
                session.titre AS talk_titre,
                github_user.login AS github_user_login
            FROM afup_sessions_vote_github AS vote
            LEFT JOIN afup_sessions AS session ON session.session_id = vote.session_id
            LEFT JOIN afup_user_github AS github_user ON github_user.id = vote.user
            WHERE session.id_forum = :eventId
            ORDER BY vote.session_id, vote.submitted_on',
            ['eventId' => $eventId],
        );

        $votes = [];
        foreach ($rows as $row) {
            $votes[] = [
                'id' => (int) $row['id'],
                'sessionId' => (int) $row['session_id'],
                'userId' => (int) $row['user'],
                'comment' => $row['comment'],
                'vote' => (int) $row['vote'],
                'talk' => ['title' => $row['talk_titre']],
                'githubUser' => ['login' => $row['github_user_login']],
            ];
        }

        return $votes;
    }

    /**
     * Votes d'une proposition avec l'information "membre du staff" du votant.
     *
     * @return list<array<string, mixed>>
     */
    public function getVotesByTalkWithUser(int $talkId): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT vote.id, vote.submitted_on, vote.comment, vote.vote,
                github_user.afup_crew
            FROM afup_sessions_vote_github AS vote
            LEFT JOIN afup_user_github AS github_user ON github_user.id = vote.user
            WHERE vote.session_id = :talkId
            ORDER BY vote.submitted_on DESC',
            ['talkId' => $talkId],
        );

        $votes = [];
        foreach ($rows as $row) {
            $votes[] = [
                'id' => (int) $row['id'],
                'submittedOn' => new \DateTimeImmutable($row['submitted_on']),
                'comment' => $row['comment'],
                'vote' => (int) $row['vote'],
                'githubUser' => ['afupCrew' => (bool) $row['afup_crew']],
            ];
        }

        return $votes;
    }

    /**
     * Votes d'un évènement avec l'information "membre du staff" du votant,
     * groupés par identifiant de proposition, identiques à la sortie de
     * getVotesByTalkWithUser() mais en une seule requête.
     *
     * @return array<int, list<array<string, mixed>>>
     */
    public function getVotesByEventGroupedBySession(int $eventId): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT vote.id, vote.session_id, vote.submitted_on, vote.comment, vote.vote,
                github_user.afup_crew
            FROM afup_sessions_vote_github AS vote
            LEFT JOIN afup_sessions AS session ON session.session_id = vote.session_id
            LEFT JOIN afup_user_github AS github_user ON github_user.id = vote.user
            WHERE session.id_forum = :eventId
            ORDER BY vote.session_id, vote.submitted_on DESC',
            ['eventId' => $eventId],
        );

        $votes = [];
        foreach ($rows as $row) {
            $id = $row['id'];
            $sessionId = $row['session_id'];
            $submittedOn = $row['submitted_on'];
            $comment = $row['comment'];
            $voteNote = $row['vote'];
            $afupCrew = $row['afup_crew'];

            if (!is_int($id) || !is_int($sessionId) || !is_string($submittedOn) || !is_int($voteNote)
                || ($comment !== null && !is_string($comment))
                || ($afupCrew !== null && !is_bool($afupCrew) && !is_int($afupCrew))) {
                throw new \RuntimeException('Données inattendues dans la table afup_sessions_vote_github.');
            }

            $votes[$sessionId][] = [
                'id' => $id,
                'submittedOn' => new \DateTimeImmutable($submittedOn),
                'comment' => $comment,
                'vote' => $voteNote,
                'githubUser' => ['afupCrew' => (bool) ($afupCrew ?? false)],
            ];
        }

        return $votes;
    }

    /**
     * Votes déposés par un utilisateur sur tout un évènement, indexés par
     * l'identifiant de la proposition.
     *
     * @return array<int, Vote>
     */
    public function getUserVotesByEvent(int $userId, int $eventId): array
    {
        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT vote.*
            FROM afup_sessions_vote_github AS vote
            JOIN afup_sessions AS session ON session.session_id = vote.session_id
            WHERE vote.user = :userId AND session.id_forum = :eventId',
            ['userId' => $userId, 'eventId' => $eventId],
        );

        $votes = [];
        foreach ($rows as $row) {
            $id = $row['id'];
            $sessionId = $row['session_id'];
            $rawUser = $row['user'];
            $comment = $row['comment'];
            $voteNote = $row['vote'];
            $submittedOn = $row['submitted_on'];

            if (!is_int($id) || !is_int($sessionId) || !is_int($rawUser) || !is_int($voteNote)
                || ($comment !== null && !is_string($comment))
                || ($submittedOn !== null && !is_string($submittedOn))) {
                throw new \RuntimeException('Données inattendues dans la table afup_sessions_vote_github.');
            }

            $vote = new Vote();
            $vote->id = $id;
            $vote->sessionId = $sessionId;
            $vote->userId = $rawUser;
            $vote->comment = $comment;
            $vote->vote = $voteNote;
            $vote->submittedOn = $submittedOn !== null ? new \DateTime($submittedOn) : null;
            $votes[$sessionId] = $vote;
        }

        return $votes;
    }

    public function upsert(Vote $vote): void
    {
        /** @var Vote|null $previousVote */
        $previousVote = $this->findOneBy(['userId' => $vote->userId, 'sessionId' => $vote->sessionId]);
        if ($previousVote !== null) {
            $previousVote->comment = $vote->comment;
            $previousVote->submittedOn = $vote->submittedOn;
            $previousVote->vote = $vote->vote;
            $vote = $previousVote;
        }
        $this->save($vote);
    }
}
