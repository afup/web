<?php

declare(strict_types=1);

namespace AppBundle\Association;

use Symfony\Component\String\Slugger\AsciiSlugger;

class MembershipFeeReferenceGenerator
{
    public function generate(\DateTimeImmutable $currentDate, MemberType $typePersonne, int $idPersonne, string $nomPersonne): string
    {
        $slugger = new AsciiSlugger();

        $reference = strtoupper('C' . $currentDate->format('Y') . '-' . $currentDate->format('dmYHi') . '-' . $typePersonne->value . '-' . $idPersonne . '-' . substr($slugger->slug($nomPersonne)->toString(), 0, 5));
        $reference = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', $reference);

        return $reference . ('-' . strtoupper(substr(md5((string) $reference), -3)));
    }
}
