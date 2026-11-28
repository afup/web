<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * Un speaker d'un talk publié (une ligne afup_conferenciers d'une édition), lu en DBAL.
 */
final readonly class PublishedSpeaker
{
    public string $label;

    public function __construct(
        public int $id,
        public string $firstname,
        public string $lastname,
        public ?int $personneId = null,
    ) {
        // Le même libellé que Speaker::$label.
        $this->label = $firstname . ' ' . ('' !== $lastname ? mb_strtoupper($lastname) : '');
    }
}
