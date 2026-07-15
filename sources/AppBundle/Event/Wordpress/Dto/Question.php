<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress\Dto;

final readonly class Question
{
    public function __construct(
        public string $question,
        public string $reponse,
    ) {}
}
