<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final readonly class Antenne
{
    /**
     * @param string[]|null $departments
     * @param string[]|null $pays
     */
    public function __construct(
        public string $code,
        public string $label,
        public ?Meetup $meetup,
        public string $logoUrl,
        public Socials $socials,
        public ?Map $map,
        public ?array $departments = null,
        public ?array $pays = null,
        public bool $hideOnOfficesPage = false,
    ) {
    }
}
