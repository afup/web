<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\User;
use AppBundle\Ting\JoinHydrator;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

/**
 * @extends Repository<User>
 */
class SubscriptionRepository extends Repository implements MetadataInitializer
{
    public function getStats(\DateInterval $interval): void
    {
        // Personnes physiques

        // Personnes morales
    }

    public function searchCompanyMemberSubscriptions(string $search)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('cotis', 'compagny', 'getCompanyId');

        $query = $this->getPreparedQuery(
            'SELECT pers.nom, pers.prenom, pers.email, pers.raison_sociale, cotis.*
  FROM afup_cotisations AS cotis
  LEFT JOIN afup_personnes_morales AS pers
    ON pers.id = cotis.id_personne
  WHERE
    cotis.type_personne = 1
    AND (
      cotis.informations_reglement LIKE :like
      OR cotis.numero_facture LIKE :like
      OR cotis.commentaires LIKE :like
      OR pers.email LIKE :like
      OR pers.nom LIKE :like
      OR pers.prenom LIKE :like
    )',
        )->setParams(['like' => "%{$search}%"]);
        return $query->query($this->getCollection($hydrator));
    }

    public function searchMemberSubscriptions(string $search)
    {
        $hydrator = new JoinHydrator();
        $hydrator->aggregateOn('cotis', 'compagny', 'getCompanyId');

        $query = $this->getPreparedQuery(
            'SELECT pers.nom, pers.prenom, pers.email, pers.login, cotis.*
  FROM afup_cotisations AS cotis
  LEFT JOIN afup_personnes_physiques AS pers
    ON pers.id = cotis.id_personne
  WHERE
    cotis.type_personne = 0
    AND (
      cotis.informations_reglement LIKE :like
      OR cotis.numero_facture LIKE :like
      OR cotis.commentaires LIKE :like
      OR pers.login LIKE :like
      OR pers.email LIKE :like
      OR pers.nom LIKE :like
      OR pers.prenom LIKE :like
    )',
        )->setParams(['like' => "%{$search}%"]);
        return $query->query($this->getCollection(new HydratorArray()));
    }

    /**
     * @inheritDoc
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(User::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_cotisations');

        $metadata
            ->addField([
                'columnName' => 'id',
                'fieldName' => 'id',
                'primary'       => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_debut',
                'fieldName' => 'startDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'date_fin',
                'fieldName' => 'endDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'type_personne',
                'fieldName' => 'userType',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'id_personne',
                'fieldName' => 'userId',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'montant',
                'fieldName' => 'amount',
                'type' => 'float',
            ])
            ->addField([
                'columnName' => 'type_reglement',
                'fieldName' => 'paymentType',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'informations_reglement',
                'fieldName' => 'paymentDetails',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'numero_facture',
                'fieldName' => 'invoiceNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'commentaires',
                'fieldName' => 'comments',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'token',
                'fieldName' => 'token',
                'type' => 'string',
            ])
        ;

        return $metadata;
    }
}
