<?php


namespace AppBundle\Association\Model\Repository;


use AppBundle\Association\Model\CompanyMember;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class CompanyMemberRepository extends Repository implements MetadataInitializer
{
    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(CompanyMember::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_personnes_morales');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'raison_sociale',
                'fieldName' => 'companyName',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'siret',
                'fieldName' => 'siret',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipCode',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'country',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'telephone_fixe',
                'fieldName' => 'phone',
                'type' => 'string'
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int'
            ])
            ->addField([
                'columnName' => 'max_members',
                'fieldName' => 'maxMembers',
                'type' => 'int'
            ])
        ;

        return $metadata;
    }
}
