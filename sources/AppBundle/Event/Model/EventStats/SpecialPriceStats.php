<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

final readonly class SpecialPriceStats
{
    /**
     * Place(s) achetée(s) sur le token d'une billetterie privée
     */
    public const string BUCKET_BILLETTERIE_PRIVEE = 'billetterie_privee';

    /**
     * Place(s) achetée(s) via un token visiteur hors billetterie privée
     */
    public const string BUCKET_TOKEN_VISITEUR = 'token_visiteur';

    public const array BUCKETS = [self::BUCKET_BILLETTERIE_PRIVEE, self::BUCKET_TOKEN_VISITEUR];

    public function __construct(
        /** @var array<string, int> */
        public array $confirmed = [],
        /** @var array<string, int> */
        public array $registered = [],
        /** @var array<string, int> */
        public array $paying = [],
        /** @var array<string, float> montant réel encaissé ou en attente */
        public array $realAmounts = [],
        /** @var array<string, int> nombre de montants distincts (règlés ou en attente) */
        public array $distinctAmounts = [],
    ) {}
}
