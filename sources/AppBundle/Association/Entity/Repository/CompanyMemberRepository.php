<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity\Repository;

use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Enum\CompanyMemberStatus;
use AppBundle\Association\MemberType;
use AppBundle\Doctrine\EntityRepository;
use AppBundle\MembershipFee\Entity\Cotisation;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends EntityRepository<CompanyMember>
 */
final class CompanyMemberRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyMember::class);
    }

    /**
     * @return list<CompanyMember>
     */
    public function findDisplayableCompanies(): array
    {
        $rows = $this->createQueryBuilderWithLastSubscription()
            ->where('c.publicProfileEnabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();

        return array_values(array_filter(
            $this->hydrateLastSubscription($rows),
            static fn(CompanyMember $companyMember): bool => $companyMember->hasUpToDateMembershipFee(),
        ));
    }

    /**
     * Contrairement à find(), renseigne la date de fin de dernière cotisation (lastSubscription).
     */
    public function findById(int $id): ?CompanyMember
    {
        $rows = $this->createQueryBuilderWithLastSubscription()
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        return $this->hydrateLastSubscription($rows)[0] ?? null;
    }

    /**
     * @return list<CompanyMember>
     */
    public function loadAll(): array
    {
        return $this->hydrateLastSubscription(
            $this->createQueryBuilderWithLastSubscription()->getQuery()->getResult(),
        );
    }

    /**
     * @return list<CompanyMember>
     */
    public function search(string $sort = 'name', string $direction = 'asc', ?string $filter = null, bool $onlyDisplayActive = true): array
    {
        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException(sprintf('Invalid sort direction "%s".', $direction));
        }
        $sorts = [
            'name' => ['c.companyName'],
            'status' => ['c.status', 'c.companyName'],
        ];
        if (!array_key_exists($sort, $sorts)) {
            throw new InvalidArgumentException(sprintf('Invalid sort "%s".', $sort));
        }

        $queryBuilder = $this->createQueryBuilderWithLastSubscription();
        foreach ($sorts[$sort] as $field) {
            $queryBuilder->addOrderBy($field, $direction);
        }

        // On filtre sur tous les mots possibles. Donc plus on a de mots dans la recherche plus on aura de résultats.
        // Mais ça peut aussi permettre de trouver des personnes en entrant par exemple "Prénom email" dans le champ de recherche :
        //   Même si l'email ne colle pas on pourra trouver la personne.
        // C'est un peu barbare mais généralement on ne met qu'un seul terme dans la recherche… du coup c'est pas bien grave.
        if ($filter) {
            $filters = array_filter(array_map(trim(...), explode(' ', $filter)));
            $ors = $queryBuilder->expr()->orX();
            foreach (array_values($filters) as $i => $value) {
                $ors->add("LOWER(c.companyName) LIKE LOWER(:filter$i) OR c.city LIKE :filter$i");
                $queryBuilder->setParameter('filter' . $i, '%' . $value . '%');
            }
            if ($ors->count() > 0) {
                $queryBuilder->andWhere($ors);
            }
        }

        if ($onlyDisplayActive) {
            $queryBuilder->andWhere('c.status = :status')
                ->setParameter('status', CompanyMemberStatus::Active);
        }

        return $this->hydrateLastSubscription($queryBuilder->getQuery()->getResult());
    }

    /**
     * Nombre de personnes physiques rattachées à chaque personne morale.
     *
     * @return array<int, int>
     */
    public function countActiveByCompany(): array
    {
        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id_personne_morale', 'COUNT(id) AS nb')
            ->from('afup_personnes_physiques')
            ->groupBy('id_personne_morale')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            if (!is_numeric($row['id_personne_morale']) || !is_numeric($row['nb'])) {
                throw new \UnexpectedValueException('Unexpected non-numeric value.');
            }
            $result[(int) $row['id_personne_morale']] = (int) $row['nb'];
        }

        return $result;
    }

    public function remove(CompanyMember $companyMember): void
    {
        $nbCotisations = $this->getEntityManager()->getRepository(Cotisation::class)->count([
            'typePersonne' => MemberType::MemberCompany,
            'idPersonne' => $companyMember->id,
        ]);
        if (0 < $nbCotisations) {
            throw new InvalidArgumentException('Impossible de supprimer une personne morale qui a des cotisations');
        }

        $nbUsers = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('afup_personnes_physiques')
            ->where('id_personne_morale = :id')
            ->setParameter('id', $companyMember->id)
            ->executeQuery()
            ->fetchOne();
        if (!is_numeric($nbUsers)) {
            throw new \UnexpectedValueException('Unexpected non-numeric value.');
        }
        if (0 < (int) $nbUsers) {
            throw new InvalidArgumentException('Impossible de supprimer une personne morale qui a des membres');
        }

        $this->delete($companyMember);
    }

    /** @return array<int, string> */
    public function getList(): array
    {
        $result = [];
        foreach ($this->findBy([], ['companyName' => 'ASC']) as $companyMember) {
            $result[(int) $companyMember->id] = sprintf('%s (id : %d)', $companyMember->companyName, $companyMember->id);
        }

        return $result;
    }

    public function countByStatus(CompanyMemberStatus $status): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function searchCompanyMemberSubscriptions(string $search): array
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();

        return $queryBuilder
            ->select('pers.nom', 'pers.prenom', 'pers.email', 'pers.raison_sociale', 'cotis.*')
            ->from('afup_personnes_morales', 'pers')
            ->leftJoin('pers', 'afup_cotisations', 'cotis', 'pers.id = cotis.id_personne')
            ->where('cotis.type_personne = :memberType')
            ->andWhere($queryBuilder->expr()->or(
                'cotis.informations_reglement LIKE :like',
                'cotis.numero_facture LIKE :like',
                'cotis.commentaires LIKE :like',
                'pers.email LIKE :like',
                'pers.nom LIKE :like',
                'pers.prenom LIKE :like',
            ))
            ->setParameter('memberType', MemberType::MemberCompany->value)
            ->setParameter('like', '%' . $search . '%')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    private function createQueryBuilderWithLastSubscription(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'MAX(f.dateFin) AS lastSubscription')
            ->leftJoin(Cotisation::class, 'f', 'WITH', 'f.typePersonne = :memberType AND f.idPersonne = c.id')
            ->setParameter('memberType', MemberType::MemberCompany)
            ->groupBy('c.id');
    }

    /**
     * Les lignes sont de la forme [0 => CompanyMember, 'lastSubscription' => timestamp Unix|null].
     *
     * @param list<array{0: CompanyMember, lastSubscription: mixed}> $rows
     *
     * @return list<CompanyMember>
     */
    private function hydrateLastSubscription(array $rows): array
    {
        $companies = [];
        foreach ($rows as $row) {
            $company = $row[0];
            $lastSubscription = $row['lastSubscription'];
            if (null !== $lastSubscription && !is_numeric($lastSubscription)) {
                throw new \UnexpectedValueException('Unexpected non-numeric value.');
            }
            $company->lastSubscription = $lastSubscription !== null
                ? new \DateTimeImmutable('@' . (int) $lastSubscription)
                : null;
            $companies[] = $company;
        }

        return $companies;
    }
}
