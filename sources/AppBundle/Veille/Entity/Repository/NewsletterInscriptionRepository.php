<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity\Repository;

use AppBundle\Veille\Entity\NewsletterInscription;
use AppBundle\Association\Entity\Utilisateur;
use AppBundle\Association\Model\User;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<NewsletterInscription>
 */
class NewsletterInscriptionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterInscription::class);
    }

    public function subscribe(User $user): void
    {
        $inscription = new NewsletterInscription();
        $inscription->user = $this->getEntityManager()->getReference(Utilisateur::class, $user->getId());
        $inscription->dateInscription = new \DateTime();
        $this->save($inscription);
    }

    public function hasUserSubscribed(User $user): bool
    {
        $sql = 'SELECT COUNT(*)
            FROM afup_techletter_subscriptions ats
            LEFT JOIN afup_personnes_physiques app ON app.id = ats.user_id
            LEFT JOIN (
              SELECT afup_techletter_unsubscriptions.email, MAX(afup_techletter_unsubscriptions.unsubscription_date) as max_unsubscriptions_date
              FROM afup_techletter_unsubscriptions
              GROUP BY afup_techletter_unsubscriptions.email
            ) as latest_unsubscriptions ON (app.email = latest_unsubscriptions.email AND latest_unsubscriptions.max_unsubscriptions_date > ats.subscription_date)
            WHERE latest_unsubscriptions.email IS NULL
            AND ats.user_id = :userId';

        return (int) $this->getEntityManager()->getConnection()->fetchOne($sql, ['userId' => $user->getId()]) > 0;
    }

    /**
     * @return list<array{login: string, email: string, nom: string, prenom: string, lastsubscription: string, subscription_date: string, id: int, user_id: int}>
     */
    public function getAllSubscriptionsWithUser(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative($this->getAllSubscriptionsWithUserQuery());
    }

    public function countAllSubscriptionsWithUser(): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM (%s) req', $this->getAllSubscriptionsWithUserQuery());
        return (int) $this->getEntityManager()->getConnection()->fetchOne($sql);
    }

    private function getAllSubscriptionsWithUserQuery(): string
    {
        return 'SELECT app.login, app.email, app.nom, app.prenom, MAX(ac.date_fin) AS lastsubscription, ats.subscription_date, ats.id, ats.user_id
            FROM afup_techletter_subscriptions ats
            LEFT JOIN afup_personnes_physiques app ON app.id = ats.user_id
            LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
            LEFT JOIN afup_cotisations ac ON ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)
            LEFT JOIN (
              SELECT afup_techletter_unsubscriptions.email, MAX(afup_techletter_unsubscriptions.unsubscription_date) as max_unsubscriptions_date
              FROM afup_techletter_unsubscriptions
              GROUP BY afup_techletter_unsubscriptions.email
            ) as latest_unsubscriptions ON (app.email = latest_unsubscriptions.email AND latest_unsubscriptions.max_unsubscriptions_date > ats.subscription_date)
            WHERE latest_unsubscriptions.email IS NULL
            AND ac.date_fin > UNIX_TIMESTAMP()
            GROUP BY app.id';
    }

    /**
     * @return list<array{email: string}>
     */
    public function getSubscribedEmails(): array
    {
        return $this->getEntityManager()->getConnection()->fetchAllAssociative(
            'SELECT app.email
            FROM afup_techletter_subscriptions ats
            LEFT JOIN afup_personnes_physiques app ON app.id = ats.user_id
            LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
            LEFT JOIN afup_cotisations ac ON ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)
            LEFT JOIN (
              SELECT afup_techletter_unsubscriptions.email, MAX(afup_techletter_unsubscriptions.unsubscription_date) as max_unsubscriptions_date
              FROM afup_techletter_unsubscriptions
              GROUP BY afup_techletter_unsubscriptions.email
            ) as latest_unsubscriptions ON (app.email = latest_unsubscriptions.email AND latest_unsubscriptions.max_unsubscriptions_date > ats.subscription_date)
            WHERE latest_unsubscriptions.email IS NULL
            AND ac.date_fin > UNIX_TIMESTAMP()
            GROUP BY app.id',
        );
    }
}
