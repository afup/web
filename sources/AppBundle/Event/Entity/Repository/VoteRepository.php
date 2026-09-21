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
