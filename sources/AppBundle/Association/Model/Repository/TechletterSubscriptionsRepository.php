<?php

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
    public function subscribe(User $user = null)
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
        $subscription = null;
        if ($user !== null) {
            $subscription = $this->getOneBy(['userId' => $user->getId()]);
        }

        if ($user === null or $subscription === null) {
            return false;
        }

        return true;
    }

    public function getAllSubscriptionsWithUser()
    {
        $hydrator = new HydratorSingleObject();
        $hydrator
            ->mapAliasTo('lastsubscription', 'app', 'setLastSubscription')
            ->mapObjectTo('app', 'ats', 'setUser')
        ;

        return $this->getQuery(
            'SELECT app.login, app.email, app.nom, app.prenom, MAX(ac.date_fin) AS lastsubscription, ats.subscription_date, ats.id, ats.user_id
            FROM afup_techletter_subscriptions ats
            LEFT JOIN afup_personnes_physiques app ON app.id = ats.user_id
            LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
            LEFT JOIN afup_cotisations ac ON ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)
            GROUP BY app.id
          ')->query($this->getCollection($hydrator))
            ;
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
            WHERE ac.date_fin > UNIX_TIMESTAMP()
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
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'user_id',
                'fieldName' => 'userId',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'subscription_date',
                'fieldName' => 'subscriptionDate',
                'type' => 'datetime'
            ])
        ;

        return $metadata;
    }
}
