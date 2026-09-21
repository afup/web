<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\MembershipFee\Entity\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\MemberType;
use AppBundle\MembershipFee\Entity\Repository\CotisationRepository;
use Doctrine\DBAL\Connection;

final class CotisationRepositoryTest extends IntegrationTestCase
{
    public function testGenerateInvoiceNumberReturnsFirstNumberOnEmptyTable(): void
    {
        $cotisationRepository = self::getContainer()->get(CotisationRepository::class);

        self::assertSame('COTIS-' . date('Y') . '-1', $cotisationRepository->generateInvoiceNumber());
    }

    public function testGenerateInvoiceNumberIncrementsLastPrefixedInvoiceNumber(): void
    {
        $cotisationRepository = self::getContainer()->get(CotisationRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertCotisation($connection, 'COTIS-' . date('Y') . '-42');

        self::assertSame('COTIS-' . date('Y') . '-43', $cotisationRepository->generateInvoiceNumber());
    }

    public function testGenerateInvoiceNumberTakesOldFormatInvoiceNumbersIntoAccount(): void
    {
        $cotisationRepository = self::getContainer()->get(CotisationRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        // Ancien format : numéro commençant directement par l'année (comme la compta générale)
        $this->insertCotisation($connection, date('Y') . '-04-14-7');
        $this->insertCotisation($connection, 'COTIS-' . date('Y') . '-20');

        self::assertSame('COTIS-' . date('Y') . '-21', $cotisationRepository->generateInvoiceNumber());
    }

    public function testGenerateInvoiceNumberIgnoresInvoiceNumbersFromOtherYears(): void
    {
        $cotisationRepository = self::getContainer()->get(CotisationRepository::class);
        $connection = self::getContainer()->get(Connection::class);

        $this->insertCotisation($connection, 'COTIS-' . (date('Y') - 1) . '-99');
        $this->insertCotisation($connection, (date('Y') - 1) . '-04-14-99');

        self::assertSame('COTIS-' . date('Y') . '-1', $cotisationRepository->generateInvoiceNumber());
    }

    private function insertCotisation(Connection $connection, string $numeroFacture): void
    {
        $connection->insert('afup_cotisations', [
            'type_personne' => MemberType::MemberPhysical->value,
            'id_personne' => 42,
            'montant' => 50.0,
            'date_debut' => strtotime('now'),
            'date_fin' => strtotime('+1 year'),
            'numero_facture' => $numeroFacture,
        ]);
    }
}
