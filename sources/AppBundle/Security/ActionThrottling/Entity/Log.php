<?php

declare(strict_types=1);

namespace AppBundle\Security\ActionThrottling\Entity;

use AppBundle\Security\ActionThrottling\Entity\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'afup_throttling')]
class Log
{
    public const string ACTION_SPONSOR_TOKEN = 'sponsor_token';
    public const array LIMITATIONS = [
        self::ACTION_SPONSOR_TOKEN => ['delay' => 'PT1H', 'limit' => 10],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $ip = null;

    #[ORM\Column(length: 64)]
    public string $action;

    #[ORM\Column(name: 'object_id', type: 'integer', nullable: true)]
    public ?int $idObjet = null;

    #[ORM\Column(name: 'created_on', type: 'datetime')]
    public \DateTimeInterface $dateCreation;
}
