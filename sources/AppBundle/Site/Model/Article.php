<?php

declare(strict_types=1);

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Article implements NotifyPropertyInterface
{
    use NotifyProperty;

    private ?int $id = null;

    /**
     * @var int
     */
    private $rubricId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $leadParagraph;

    /***
     * @var string
     */
    private $content;

    /***
     * @var string
     */
    private $contentType;

    /**
     * @var int
     */
    private $theme;

    /**
     * @var int
     */
    private $eventId;

    private ?\DateTime $publishedAt = null;

    private ?int $state = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id): self
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRubricId()
    {
        return $this->rubricId;
    }

    /**
     * @param $rubricId $rubricId
     *
     * @return $this
     */
    public function setRubricId($rubricId): self
    {
        $this->propertyChanged('rubricId', $this->rubricId, $rubricId);
        $this->rubricId = $rubricId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;

        return $this;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path): self
    {
        $this->propertyChanged('path', $this->path, $path);

        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getLeadParagraph()
    {
        $leadParagraph = $this->leadParagraph;

        if ($this->isContentTypeMarkdown()) {
            $parseDown = new \Parsedown();
            $leadParagraph = $parseDown->parse($leadParagraph);
        }

        return $leadParagraph;
    }

    /**
     * @param string $leadParagraph
     *
     * @return $this
     */
    public function setLeadParagraph($leadParagraph): self
    {
        $this->propertyChanged('leadParagraph', $this->leadParagraph, $leadParagraph);
        $this->leadParagraph = $leadParagraph;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $content = $this->content;

        if ($this->isContentTypeMarkdown()) {
            $parseDown = new \Parsedown();
            $content = $parseDown->parse($content);
        }

        return $content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->propertyChanged('content', $this->content, $content);
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType): self
    {
        $this->propertyChanged('contentType', $this->contentType, $contentType);
        $this->contentType = $contentType;

        return $this;
    }

    public function isContentTypeMarkdown(): bool
    {
        return $this->contentType == \Afup\Site\Corporate\Article::TYPE_CONTENU_MARKDOWN;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt): self
    {
        $this->propertyChanged('publishedAt', $this->publishedAt, $publishedAt);
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param int $theme
     *
     * @return $this
     */
    public function setTheme($theme): self
    {
        $this->propertyChanged('theme', $this->theme, $theme);
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThemeLabel()
    {
        if (null === ($theme = $this->getTheme())) {
            return null;
        }

        return \Afup\Site\Corporate\Article::getThemeLabel($this->getTheme());
    }

    /**
     * @return int|null
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     *
     * @return $this
     */
    public function setEventId($eventId): self
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getTeaser()
    {
        if (strlen($leadParagraph = $this->getLeadParagraph()) !== 0) {
            return strip_tags($leadParagraph);
        }

        return  substr(strip_tags($this->getContent()), 0, 200);
    }

    public function getTextTeaser(): string
    {
        return html_entity_decode($this->getTeaser());
    }

    public function getSlug(): string
    {
        return $this->getId() . '-' . $this->getPath();
    }
    /**
     * @return int
     */
    public function getState(): ?int
    {
        return $this->state;
    }

    /**
     * @param int $state
     *
     * @return $this
     */
    public function setState($state): self
    {
        $state = (int) $state;
        $this->propertyChanged('state', $this->state, $state);
        $this->state = $state;
        return $this;
    }
}
