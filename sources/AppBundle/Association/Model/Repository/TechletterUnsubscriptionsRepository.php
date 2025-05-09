<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\TechletterUnsubscription;
use AppBundle\Association\Model\User;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class TechletterUnsubscriptionsRepository extends Repository implements MetadataInitializer
{
    public function createFromWebhookData(array $data): TechletterUnsubscription
    {
        $techletterUnsubscription = new TechletterUnsubscription();
        $techletterUnsubscription
            ->setEmail($data['email'])
            ->setReason($data['reason'])
            ->setMailchimpId($data['id'])
            ->setUnsubscriptionDate(new \DateTime())
        ;

        return $techletterUnsubscription;
    }

    public function createFromUser(User $user): TechletterUnsubscription
    {
        $techletterUnsubscription = new TechletterUnsubscription();
        $techletterUnsubscription
            ->setEmail($user->getEmail())
            ->setReason("MANUAL")
            ->setMailchimpId(null)
            ->setUnsubscriptionDate(new \DateTime())
        ;

        return $techletterUnsubscription;
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(TechletterUnsubscription::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_techletter_unsubscriptions');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'unsubscription_date',
                'fieldName' => 'unsubscriptionDate',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'reason',
                'fieldName' => 'reason',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'mailchimp_id',
                'fieldName' => 'mailchimpId',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
