<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Entity\Question;
use AppBundle\AssembleeGenerale\Entity\Vote;
use AppBundle\AssembleeGenerale\Enum\VoteValeur;
use AppBundle\Association\Entity\Utilisateur;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Vote>
 */
class VoteRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    public function buildVote(int $questionId, int $userId, int $weight, string $value): Vote
    {
        $vote = new Vote();
        $vote->question = $this->getEntityManager()->getReference(Question::class, $questionId);
        $vote->utilisateur = $this->getEntityManager()->getReference(Utilisateur::class, $userId);
        $vote->poids = $weight;
        $vote->valeur = $value;
        $vote->creeLe = new \DateTime();
        return $vote;
    }

    public function loadByQuestionIdAndUserId(int $questionId, int $userId): ?Vote
    {
        return $this->createQueryBuilder('v')
            ->where('v.question = :question')
            ->andWhere('v.utilisateur = :user')
            ->setParameter('question', $questionId)
            ->setParameter('user', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<string, int>
     */
    public function getResultsForQuestionId(int $questionId): array
    {
        $results = [
            VoteValeur::Oui->value => 0,
            VoteValeur::Non->value => 0,
            VoteValeur::Abstention->value => 0,
        ];

        $rows = $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT `value`, SUM(weight) AS weight_sum
             FROM afup_vote_assemblee_generale
             WHERE afup_assemblee_generale_question_id = :question_id
             GROUP BY `value`',
            ['question_id' => $questionId],
        );

        foreach ($rows as $row) {
            $results[$row['value']] = (int) $row['weight_sum'];
        }

        return $results;
    }
}
