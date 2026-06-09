<?php

declare(strict_types=1);

namespace PlanetePHP;

use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'afup_planete_billet')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Feed::class)]
    #[ORM\JoinColumn(name: 'afup_planete_flux_id', nullable: true)]
    public ?Feed $feed = null;

    #[ORM\Column(name: 'clef', length: 255, nullable: true)]
    public ?string $key = null;

    #[ORM\Column(name: 'titre', type: 'text', nullable: true)]
    public ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $url = null;

    #[ORM\Column(name: 'maj', type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $updatedAt = null;

    #[ORM\Column(name: 'auteur', type: 'text', nullable: true)]
    public ?string $author = null;

    #[ORM\Column(name: 'resume', type: 'text', nullable: true)]
    public ?string $summary = null;

    #[ORM\Column(name: 'contenu', type: 'text', nullable: true)]
    public ?string $content = null;

    #[ORM\Column(name: 'etat', nullable: false)]
    public bool $isRelevant = false;
}
