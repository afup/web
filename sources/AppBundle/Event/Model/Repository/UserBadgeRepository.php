<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\UserBadge;
use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;

class UserBadgeRepository extends Repository implements MetadataInitializer
{
    public function findByUserId($userId)
    {
        $sql = 'SELECT afup_personnes_physiques_badge.*,
                       afup_badge.*
        FROM afup_personnes_physiques_badge
        JOIN afup_badge ON (afup_personnes_physiques_badge.badge_id = afup_badge.id)
        WHERE afup_personnes_physiques_badge.afup_personne_physique_id = :user_id
        ORDER BY afup_personnes_physiques_badge.issued_at';

        return $this
            ->getPreparedQuery($sql)
            ->setParams(['user_id' => $userId])
            ->query($this->getCollection($this->getHydratorForUserBadge()))
        ;
    }

    private function getHydratorForUserBadge()
    {
        return (new HydratorSingleObject())
            ->mapObjectTo('afup_badge', 'afup_personnes_physiques_badge', 'setBadge')
        ;
    }


    /**
     *
     * @return Metadata
     *
     * @throws Exception
     */
    public static function initMetadata(SerializerFactoryInterface $serializerFactory, array $options = [])
    {
        $metadata = new Metadata($serializerFactory);
        $metadata->setEntity(UserBadge::class);
        $metadata->setConnectionName('main');
        $metadata->setDatabase($options['database']);
        $metadata->setTable('afup_personnes_physiques_badge');

        $metadata
            ->addField([
                'columnName' => 'afup_personne_physique_id',
                'fieldName' => 'userId',
                'primary' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'badge_id',
                'fieldName' => 'badgeId',
                'primary' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'issued_at',
                'fieldName' => 'issuedAt',
                'type' => 'datetime',
                'serializer_options' => [
                    'unserialize' => ['unSerializeUseFormat' => false],
                ],
            ])
        ;

        return $metadata;
    }
}
