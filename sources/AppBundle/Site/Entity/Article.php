<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use AppBundle\Doctrine\Entity;
use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\Site\Enum\ArticleTheme;
use AppBundle\Site\Enum\ArticleEtat;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_article')]
class Article extends Entity
{
    #[ORM\ManyToOne(targetEntity: Rubrique::class)]
    #[ORM\JoinColumn(name: 'id_site_rubrique', referencedColumnName: 'id', nullable: true)]
    public ?Rubrique $rubrique = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $raccourci = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $chapeau = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $contenu = null;

    #[ORM\Column(nullable: true, enumType: ArticleTheme::class)]
    public ?ArticleTheme $theme = null;

    #[ORM\Column(name: 'id_forum', nullable: true)]
    public ?int $idEvent = null;

    #[ORM\Column(name: 'date', type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $datePublication = null;

    #[ORM\Column(nullable: false, enumType: ArticleEtat::class)]
    public ArticleEtat $etat = ArticleEtat::EnAttente;

    #[ORM\Column(nullable: true)]
    public ?int $position = 0;

    #[ORM\Column(nullable: true)]
    public ?int $idPersonnePhysique = null;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }

    public function getSlug(): string
    {
        return $this->id . '-' . $this->raccourci;
    }

    public function getContenuFormate(): string
    {
        return new \Parsedown()->text((string) $this->contenu);
    }

    public function getChapeauFormate(): string
    {
        return new \Parsedown()->text((string) $this->chapeau);
    }

    public function getResume(): string
    {
        if (strlen((string) $this->chapeau) !== 0) {
            return strip_tags((string) $this->chapeau);
        }

        return substr(strip_tags((string) $this->contenu), 0, 200);
    }

    public function getResumeTexte(): string
    {
        return html_entity_decode($this->getResume());
    }
}
