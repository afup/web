<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Entity\Question;
use AppBundle\Doctrine\EntityRepository;
use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Question>
 */
class QuestionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function loadNextOpenedQuestion(\DateTimeInterface $generalMeetingDate): ?Question
    {
        return $this->createQueryBuilder('q')
            ->where('q.dateOuverture IS NOT NULL')
            ->andWhere('q.dateCloture IS NULL')
            ->andWhere('q.date = :date')
            ->setParameter('date', \DateTime::createFromFormat('U', $generalMeetingDate->format('U')), UnixTimestampType::NAME)
            ->orderBy('q.dateOuverture', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Question[]
     */
    public function loadClosedQuestions(\DateTimeInterface $generalMeetingDate): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.dateCloture IS NOT NULL')
            ->andWhere('q.date = :date')
            ->setParameter('date', \DateTime::createFromFormat('U', $generalMeetingDate->format('U')), UnixTimestampType::NAME)
            ->orderBy('q.dateOuverture', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Question[]
     */
    public function loadByDate(\DateTimeInterface $generalMeetingDate): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.date = :date')
            ->setParameter('date', \DateTime::createFromFormat('U', $generalMeetingDate->format('U')), UnixTimestampType::NAME)
            ->getQuery()
            ->getResult();
    }

    public function open(Question $question): void
    {
        $question->dateOuverture = new \DateTime();
        $this->save($question);
    }

    public function close(Question $question): void
    {
        $question->dateCloture = new \DateTime();
        $this->save($question);
    }
}
