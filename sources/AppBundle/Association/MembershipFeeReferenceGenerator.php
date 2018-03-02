<?php

namespace AppBundle\Association;

require_once __DIR__ . '/../../Afup/fonctions.php';

class MembershipFeeReferenceGenerator
{
    /**
     * @param \DateTimeImmutable $currentDate
     * @param string $typePersonne
     * @param string $idPersonne
     * @param string $nomPersonne
     *
     * @return mixed|string
     */
    public function generate(\DateTimeImmutable $currentDate, $typePersonne, $idPersonne, $nomPersonne)
    {
        $reference = strtoupper('C' . $currentDate->format('Y') . '-' . $currentDate->format('dmYHi') . '-' . $typePersonne . '-' . $idPersonne . '-' . substr($nomPersonne, 0, 5));
        $reference = supprimerAccents($reference);
        $reference = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', $reference);
        $reference .= '-' . strtoupper(substr(md5($reference), - 3));

        return $reference;
    }
}
