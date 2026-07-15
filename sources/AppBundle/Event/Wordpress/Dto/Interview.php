<?php

declare(strict_types=1);

namespace AppBundle\Event\Wordpress\Dto;

final readonly class Interview
{
    public function __construct(
        /** @var array<Question> */
        public array $questions,
        public int $talkId,
    ) {}
}
