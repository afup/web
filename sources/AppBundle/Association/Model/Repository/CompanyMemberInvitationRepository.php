<?php

declare(strict_types=1);


namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class CompanyMemberInvitationRepository extends Repository implements MetadataInitializer
{
    /**
     * @return CollectionInterface&iterable<CompanyMemberInvitation>
     */
    public function loadPendingInvitationsByCompany(CompanyMember $companyMember)
    {
        return $this->getBy([
            'companyId' => $companyMember->getId(),
            'status' => CompanyMemberInvitation::STATUS_PENDING,
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(CompanyMemberInvitation::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_personnes_morales_invitations');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'company_id',
                'fieldName' => 'companyId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'token',
                'fieldName' => 'token',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'manager',
                'fieldName' => 'manager',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'submitted_on',
                'fieldName' => 'submittedOn',
                'type' => 'datetime',
            ])
            ->addField([
                'columnName' => 'status',
                'fieldName' => 'status',
                'type' => 'int',
            ])
        ;

        return $metadata;
    }
}
