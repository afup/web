<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee\Model\Repository;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\MembershipFee\Model\MembershipFee;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\BackedEnum;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use DateTime;

class MembershipFeeRepository extends Repository implements MetadataInitializer
{
    public function getMembershipStartingDate(MemberType $memberType, int $idMember): DateTime
    {
        /** @var SelectInterface $qb */
        $qb = $this->getQueryBuilder('SELECT');
        $qb->from('afup_cotisations')
            ->cols(['date_fin'])
            ->where('type_personne = :type_personne')
            ->where('id_personne = :id_personne')
            ->orderBy(['date_fin DESC']);

        $result = $this->getQuery($qb->getStatement())
                       ->setParams(
                           ['type_personne' => $memberType->value, 'id_personne' => $idMember],
                       )->execute();

        return isset($result['date_fin']) ? new DateTime('@' . $result['date_fin']) : new DateTime();
    }

    public function generateInvoiceNumber(): string
    {
        $sql = 'SELECT';
        $sql .= "  MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 as number ";
        $sql .= 'FROM';
        $sql .= '  afup_cotisations ';
        $sql .= 'WHERE';
        $sql .= '  LEFT(numero_facture, 4) = :date';
        $sql .= '  OR LEFT(numero_facture, 10) = :prefixed_date';

        $result = $this->getQuery($sql)
                          ->setParams(['date' => date('Y'), 'prefixed_date' => 'COTIS-' . date('Y')])
                          ->query($this->getCollection())->first()[0];


        return 'COTIS-' . date('Y') . '-' . (is_null($result?->number) ? 1 : $result->number);
    }

    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);

        $metadata->setEntity(MembershipFee::class);
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
                'columnName' => 'type_personne',
                'fieldName' => 'userType',
                'type' => 'int',
                'serializer' => BackedEnum::class,
                'serializer_options' => [
                    'unserialize' => ['enum' => MemberType::class],
                ],
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
                'serializer' => BackedEnum::class,
                'serializer_options' => [
                    'unserialize' => ['enum' => MembershipFeePayment::class],
                ],
            ])
            ->addField([
                'columnName' => 'informations_reglement',
                'fieldName' => 'paymentDetails',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'date_debut',
                'fieldName' => 'startDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'date_fin',
                'fieldName' => 'endDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
            ->addField([
                'columnName' => 'numero_facture',
                'fieldName' => 'invoiceNumber',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'date_facture',
                'fieldName' => 'invoiceDate',
                'type' => 'datetime_immutable',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'Y-m-d H:i:s'],
                    'serialize' => ['serializeUseFormat' => true, 'format' => 'Y-m-d H:i:s'],
                ],
            ])
            ->addField([
                'columnName' => 'reference_client',
                'fieldName' => 'clientReference',
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
            ->addField([
                'columnName' => 'nombre_relances',
                'fieldName' => 'nbReminders',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'date_derniere_relance',
                'fieldName' => 'lastReminderDate',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => true, 'format' => 'U'],
                ],
            ])
        ;

        return $metadata;
    }
}
