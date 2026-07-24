<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee\Entity\Repository;

use AppBundle\Association\MemberType;
use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use AppBundle\Doctrine\EntityRepository;
use AppBundle\MembershipFee\Entity\Cotisation;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Cotisation>
 */
class CotisationRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cotisation::class);
    }

    public function getMembershipStartingDate(MemberType $typePersonne, int $idPersonne): DateTime
    {
        return $this->getLatestByUserTypeAndId($typePersonne, $idPersonne)->dateFin ?? new DateTime();
    }

    public function generateInvoiceNumber(): string
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery(
            <<<'SQL'
SELECT MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 as number
FROM afup_cotisations
WHERE LEFT(numero_facture, 4) = :date
  OR LEFT(numero_facture, 10) = :prefixed_date
SQL,
            ['date' => date('Y'), 'prefixed_date' => 'COTIS-' . date('Y')],
        )->fetchOne();

        return 'COTIS-' . date('Y') . '-' . (is_numeric($result) ? (int) $result : 1);
    }

    public function updatePayment(int $id, MembershipFeePayment $typeReglement, string $informationsReglement): bool
    {
        $cotisation = $this->find($id);
        if (!$cotisation instanceof Cotisation) {
            return false;
        }

        $cotisation->typeReglement = $typeReglement;
        $cotisation->informationsReglement = $informationsReglement;
        $this->save($cotisation);

        return true;
    }

    public function getLatestByUserTypeAndId(MemberType $typePersonne, int $idPersonne): ?Cotisation
    {
        return $this->findOneBy(
            ['typePersonne' => $typePersonne, 'idPersonne' => $idPersonne],
            ['dateFin' => 'DESC'],
        );
    }

    /**
     * @return Cotisation[]
     */
    public function getListByUserTypeAndId(MemberType $typePersonne, int $idPersonne): array
    {
        return $this->findBy(
            ['typePersonne' => $typePersonne, 'idPersonne' => $idPersonne],
            ['dateFin' => 'DESC'],
        );
    }
}
