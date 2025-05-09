<?php

declare(strict_types=1);

namespace AppBundle\Association\Model\Repository;

use AppBundle\Association\Model\CompanyMember;
use Assert\Assertion;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Driver\Mysqli\Serializer\Boolean;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorArray;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\MetadataInitializer;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use InvalidArgumentException;

/**
 * @extends Repository<CompanyMember>
 */
class CompanyMemberRepository extends Repository implements MetadataInitializer
{
    public function findDisplayableCompanies(): array
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteCompanyMember();
        $queryBuilder->where('apm.public_profile_enabled = 1');

        $companiesCollection = $this
            ->getQuery($queryBuilder->getStatement())
            ->query($this->getCollection($this->getHydratorForCompanyMember()));

        $companies = iterator_to_array($companiesCollection->getIterator());

        return array_filter(
            $companies,
            fn (CompanyMember $companyMember): bool => $companyMember->hasUpToDateMembershipFee()
        );
    }

    public function findById($id):? CompanyMember
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteCompanyMember();
        $queryBuilder->where('apm.id = :id');

        return $this
            ->getPreparedQuery($queryBuilder->getStatement())
            ->setParams(['id' => $id])
            ->query($this->getCollection($this->getHydratorForCompanyMember()))
            ->first();
    }

    public function loadAll()
    {
        $queryBuilder = $this->getQueryBuilderWithCompleteCompanyMember();

        return $this
            ->getQuery($queryBuilder->getStatement())
            ->query($this->getCollection($this->getHydratorForCompanyMember()));
    }

    /**
     * @param string      $sort
     * @param string      $direction
     * @param string|null $filter
     * @param bool        $onlyDisplayActive
     *
     * @return CollectionInterface
     */
    public function search($sort = 'name', $direction = 'asc', $filter = null, $onlyDisplayActive = true)
    {
        Assertion::inArray($direction, ['asc', 'desc']);
        $sorts = [
            'name' => ['raison_sociale'],
            'status' => ['etat', 'raison_sociale'],
        ];
        Assertion::keyExists($sorts, $sort);
        $queryBuilder = $this->getQueryBuilderWithCompleteCompanyMember()
            ->orderBy(array_map(static fn ($field): string => $field . ' ' . $direction, $sorts[$sort]));

        // On filtre sur tous les mots possibles. Donc plus on a de mots dans la recherche plus on aura de résultats.
        // Mais ça peut aussi permettre de trouver des personnes en entrant par exemple "Prénom email" dans le champ de recherche :
        //   Même si l'email ne colle pas on pourra trouver la personne.
        // C'est un peu barbare mais généralement on ne met qu'un seul terme dans la recherche… du coup c'est pas bien grave.
        if ($filter) {
            $filters = explode(' ', $filter);
            $filters = array_filter(array_map('trim', $filters));
            $ors = [];
            foreach ($filters as $i => $value) {
                $ors[] = "apm.raison_sociale LIKE :filter$i OR apm.ville LIKE :filter$i";
                $queryBuilder->bindValue('filter' . $i, '%' . $value . '%');
            }
            $queryBuilder->where('(' . implode(' OR ', $ors) . ')');
        }
        if ($onlyDisplayActive) {
            $queryBuilder->where('apm.etat = :status')
                ->bindValue('status', CompanyMember::STATUS_ACTIVE);
        }

        return $this
            ->getQuery($queryBuilder->getStatement())
            ->setParams($queryBuilder->getBindValues())
            ->query($this->getCollection($this->getHydratorForCompanyMember()));
    }

    /**
     * @return array<int, int>
     */
    public function countActiveByCompany(): array
    {
        $result = [];
        $query = $this->getQuery('SELECT id_personne_morale, COUNT(id) AS nb FROM afup_personnes_physiques GROUP BY id_personne_morale');
        foreach ($query->query($this->getCollection(new HydratorArray())) as $row) {
            $result[(int) $row['id_personne_morale']] = (int) $row['nb'];
        }

        return $result;
    }

    public function remove(CompanyMember $companyMember): void
    {
        $nbCotisations = (int) $this->getQuery('SELECT COUNT(*) nb FROM afup_cotisations WHERE type_personne = :memberType AND id_personne = :id')
            ->setParams(['memberType' => AFUP_PERSONNES_MORALES, 'id' => $companyMember->getId()])
            ->query()->first()[0]->nb;
        if (0 < $nbCotisations) {
            throw new InvalidArgumentException('Impossible de supprimer une personne morale qui a des cotisations');
        }
        $nbUsers = $this->getQuery('SELECT COUNT(*) nb FROM afup_personnes_physiques WHERE id_personne_morale = :id')
            ->setParams(['id' => $companyMember->getId()])
            ->query()->first()[0]->nb;
        if (0 < $nbUsers) {
            throw new InvalidArgumentException('Impossible de supprimer une personne morale qui a des membres');
        }

        $this->delete($companyMember);
    }

    /** @return array<int, string> */
    public function getList(): array
    {
        $result = [];
        $query = $this->getQuery('SELECT id, raison_sociale FROM afup_personnes_morales ORDER BY raison_sociale');
        foreach ($query->query($this->getCollection(new HydratorArray())) as $row) {
            $result[(int) $row['id']] = sprintf('%s (id : %d)', $row['raison_sociale'], $row['id']);
        }

        return $result;
    }

    /**
     * @param int $status
     *
     * @return int
     */
    public function countByStatus($status)
    {
        return (int) $this->getQuery('SELECT COUNT(id) AS nb FROM afup_personnes_physiques WHERE etat = :status')
            ->setParams(['status' => $status])
            ->query($this->getCollection())->first()[0]->nb;
    }

    private function getHydratorForCompanyMember()
    {
        return (new HydratorSingleObject())
            ->mapAliasTo('lastsubcription', 'apm', 'setLastSubscription');
    }

    private function getQueryBuilderWithCompleteCompanyMember()
    {
        return $this
            ->getQueryBuilderWithSubscriptions()
            ->cols([
                'apm.*',
                "MAX(ac.date_fin) AS lastsubcription",
            ]);
    }

    private function getQueryBuilderWithSubscriptions()
    {
        /**
         * @var SelectInterface $queryBuilder
         */
        $queryBuilder = $this->getQueryBuilder(self::QUERY_SELECT);
        $queryBuilder
            ->from('afup_personnes_morales apm')
            ->join('LEFT', 'afup_cotisations ac', 'ac.type_personne = 1 AND ac.id_personne = apm.id')
            ->groupBy(['apm.`id`']);

        return $queryBuilder;
    }

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
                'primary' => true,
                'autoincrement' => true,
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'prenom',
                'fieldName' => 'firstName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'nom',
                'fieldName' => 'lastName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'email',
                'fieldName' => 'email',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'raison_sociale',
                'fieldName' => 'companyName',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'siret',
                'fieldName' => 'siret',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'adresse',
                'fieldName' => 'address',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'code_postal',
                'fieldName' => 'zipCode',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'ville',
                'fieldName' => 'city',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'id_pays',
                'fieldName' => 'country',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'telephone_fixe',
                'fieldName' => 'phone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'telephone_portable',
                'fieldName' => 'cellphone',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'etat',
                'fieldName' => 'status',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'max_members',
                'fieldName' => 'maxMembers',
                'type' => 'int',
            ])
            ->addField([
                'columnName' => 'public_profile_enabled',
                'fieldName' => 'publicProfileEnabled',
                'type' => 'bool',
                'serializer' => Boolean::class,
            ])
            ->addField([
                'columnName' => 'description',
                'fieldName' => 'description',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'logo_url',
                'fieldName' => 'logoUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'website_url',
                'fieldName' => 'websiteUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'contact_page_url',
                'fieldName' => 'contactPageUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'careers_page_url',
                'fieldName' => 'careersPageUrl',
                'type' => 'string',
            ])
            ->addField([
                'columnName' => 'twitter_handle',
                'fieldName' => 'twitterHandle',
                'type' => 'string',
            ])
            ->addField(([
                'columnName' => 'related_afup_offices',
                'fieldName' => 'relatedAfupOffices',
                'type' => 'string',
            ]))
            ->addField(([
                'columnName' => 'membership_reason',
                'fieldName' => 'membershipReason',
                'type' => 'string',
            ]));

        return $metadata;
    }
}
