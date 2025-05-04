<?php

declare(strict_types=1);


namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\SubscriptionReminderLog;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class SubscriptionReminderLogRepository extends Repository implements MetadataInitializer
{
    public function getPaginatedLogs($page = 1, $limit = 50)
    {
        return $this->getQuery('
            SELECT asrl.id, asrl.user_id, asrl.user_type, asrl.email, asrl.reminder_key, asrl.reminder_date, asrl.mail_sent,
            app.id, apm.id, app.nom, apm.nom, app.prenom, apm.prenom, apm.raison_sociale
            FROM `afup_subscription_reminder_log` asrl
            LEFT JOIN afup_personnes_physiques app ON app.id = asrl.user_id
            LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
            ORDER BY asrl.reminder_date DESC
            LIMIT ' . (((int) $page-1) * $limit) . ', ' . (int) ($limit + 1)
        )->query();
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(SubscriptionReminderLog::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_subscription_reminder_log');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'user_id',
                'fieldName' => 'userId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'user_type',
                'fieldName' => 'userType',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'reminder_key',
                'fieldName' => 'reminderKey',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'reminder_date',
                'fieldName' => 'reminderDate',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'mail_sent',
                'fieldName' => 'mailSent',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
