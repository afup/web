<?php

declare(strict_types=1);

namespace AppBundle\Association;

require_once __DIR__ . '/../../Afup/fonctions.php';

class MembershipFeeReferenceGenerator
{
    /**
     * @param string|int $typePersonne
     * @param string|int $idPersonne
     * @param string $nomPersonne
     *
     * @return mixed|string
     */
    public function generate(\DateTimeImmutable $currentDate, $typePersonne, $idPersonne, $nomPersonne)
    {
        $reference = strtoupper('C' . $currentDate->format('Y') . '-' . $currentDate->format('dmYHi') . '-' . $typePersonne . '-' . $idPersonne . '-' . substr((string) supprimerAccents($nomPersonne), 0, 5));
        $reference = supprimerAccents($reference);
        $reference = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', (string) $reference);

        return $reference . ('-' . strtoupper(substr(md5((string) $reference), - 3)));
    }
}
