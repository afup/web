<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity;

use AppBundle\Event\Entity\Event;
use AppBundle\Site\ArticleContentType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_site_article')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'titre', nullable: false)]
    public string $title;

    #[ORM\Column(name: 'raccourci', nullable: false)]
    public string $path;

    #[ORM\Column(name: 'chapeau', nullable: true)]
    public ?string $leadParagraph = null;

    #[ORM\Column(name: 'contenu', nullable: false)]
    public string $content;

    #[ORM\Column(name: 'type_contenu')]
    public ArticleContentType $contentType = ArticleContentType::Markdown;

    #[ORM\Column(nullable: true)]
    public ?int $theme = null;

    #[ORM\Column(name: 'date', type: 'timestamp_immutable', nullable: true)]
    public ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(name: 'etat', nullable: true)]
    public ?int $state = 0;

    #[ORM\Column(nullable: true)]
    public ?int $position = 0;

    #[ORM\Column(name: 'id_personne_physique', nullable: true)]
    public ?int $authorId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_site_rubrique', referencedColumnName: 'id', nullable: false)]
    public Rubric $rubric;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_forum', referencedColumnName: 'id', nullable: true)]
    public ?Event $event = null;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
    }

    public function isContentTypeMarkdown(): bool
    {
        return $this->contentType === ArticleContentType::Markdown;
    }

    public function getFormatedLeadParagraph(): ?string
    {
        $leadParagraph = $this->leadParagraph;

        if ($this->isContentTypeMarkdown()) {
            $parseDown = new \Parsedown();
            $leadParagraph = $parseDown->parse($leadParagraph);
        }

        return $leadParagraph;
    }

    public function getFormatedContent(): ?string
    {
        $content = $this->content;

        if ($this->isContentTypeMarkdown()) {
            $parseDown = new \Parsedown();
            $content = $parseDown->parse($content);
        }

        return $content;
    }

    public function getThemeLabel(): ?string
    {
        if (null === $this->theme) {
            return null;
        }

        return \Afup\Site\Corporate\Article::getThemeLabel($this->theme);
    }

    public function getTeaser(): string
    {
        if (strlen((string) $this->leadParagraph) !== 0) {
            return strip_tags((string) $this->leadParagraph);
        }

        return  substr(strip_tags($this->content), 0, 200);
    }

    public function getTextTeaser(): string
    {
        return html_entity_decode($this->getTeaser());
    }

    public function getSlug(): string
    {
        return $this->id . '-' . $this->path;
    }
}
