<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * @readonly
 */
final class Antenne
{
    public string $code;
    public string $label;
    public ?Meetup $meetup;
    public string $logoUrl;
    public Socials $socials;
    public ?Map $map;

    /** @var array<string>|null */
    public ?array $departments;

    /** @var array<string>|null */
    public ?array $pays;

    public bool $hideOnOfficesPage;

    /**
     * @param string[] $departments
     * @param string[]|null $pays
     */
    public function __construct(
        string $code,
        string $label,
        ?Meetup $meetup,
        string $logoUrl,
        Socials $socials,
        ?Map $map,
        ?array $departments = null,
        ?array $pays = null,
        bool $hideOnOfficesPage = false
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->meetup = $meetup;
        $this->logoUrl = $logoUrl;
        $this->socials = $socials;
        $this->map = $map;
        $this->hideOnOfficesPage = $hideOnOfficesPage;
        $this->departments = $departments;
        $this->pays = $pays;
    }
}
