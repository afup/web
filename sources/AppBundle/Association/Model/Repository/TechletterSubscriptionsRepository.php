<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\TechletterSubscription;
use AppBundle\Association\Model\User;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TechletterSubscriptionsRepository extends Repository implements MetadataInitializer
{
    public function subscribe(User $user = null): void
    {
        $subscription = new TechletterSubscription();
        $subscription
            ->setSubscriptionDate(new \DateTime())
            ->setUserId($user->getId())
        ;
        $this->save($subscription);
    }

    public function hasUserSubscribed(User $user = null)
    {
        if (!$user instanceof User) {
            return false;
        }

        $query = $this->getPreparedQuery(
            "SELECT ats.*
                 FROM afup_techletter_subscriptions ats
                 LEFT JOIN afup_personnes_physiques app ON app.id = ats.user_id
                 LEFT JOIN (
                  SELECT afup_techletter_unsubscriptions.email, MAX(afup_techletter_unsubscriptions.unsubscription_date) as max_unsubscriptions_date
                  FROM afup_techletter_unsubscriptions
                  GROUP BY afup_techletter_unsubscriptions.email
                 ) as latest_unsubscriptions ON (app.email = latest_unsubscriptions.email AND latest_unsubscriptions.max_unsubscriptions_date > ats.subscription_date) 
                 WHERE latest_unsubscriptions.email IS NULL
                 AND ats.user_id = :userId
              "
        );

        $query->setParams(['userId' => $user->getId()]);

        return $query->query()->count() > 0;
    }

    public function getAllSubscriptionsWithUser()
    {
        $hydrator = new HydratorSingleObject();
        $hydrator
            ->mapAliasTo('lastsubscription', 'app', 'setLastSubscription')
            ->mapObjectTo('app', 'ats', 'setUser')
        ;

        return $this->getQuery($this->getAllSubscriptionsWithUserQuery())->query($this->getCollection($hydrator));
    }

    public function countAllSubscriptionsWithUser()
    {
        $sql = sprintf("SELECT COUNT(*) as cnt FROM (%s) req", $this->getAllSubscriptionsWithUserQuery());
        $row = $this->getQuery($sql)->execute();

        return $row['cnt'];
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
            GROUP BY app.id
          ';
    }

    /**
     * Returns all members who subscribed to this techletter with a valid membership
     */
    public function getSubscribedEmails()
    {
        return $this->getQuery(
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
            GROUP BY app.id
          ')->query($this->getCollection(new HydratorArray()))
        ;
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TechletterSubscription::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_techletter_subscriptions');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'user_id',
                'fieldName' => 'userId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'subscription_date',
                'fieldName' => 'subscriptionDate',
                'type' => 'datetime',
            ])
        ;

        return $metadata;
    }
}
