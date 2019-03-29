<?php

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Article implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

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

    /**
     * @var string
     */
    private $description;

    /***
     * @var string
     */
    private $content;

    /***
     * @var string
     */
    private $contentType;

    /**
     * @var \DateTime
     */
    private $publishedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
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
    public function setRubricId($rubricId)
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
    public function setTitle($title)
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
    public function setPath($path)
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
    public function setLeadParagraph($leadParagraph)
    {
        $this->propertyChanged('leadParagraph', $this->leadParagraph, $leadParagraph);
        $this->leadParagraph = $leadParagraph;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $description = $this->description;

        if ($this->isContentTypeMarkdown()) {
            $parseDown = new \Parsedown();
            $description = $parseDown->parse($description);
        }

        return $description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;

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
    public function setContent($content)
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
    public function setContentType($contentType)
    {
        $this->propertyChanged('contentType', $this->contentType, $contentType);
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isContentTypeMarkdown()
    {
        return $this->contentType == \Afup\Site\Corporate\Article::TYPE_CONTENU_MARKDOWN;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->propertyChanged('publishedAt', $this->publishedAt, $publishedAt);
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getTeaser()
    {
        if (strlen($leadParagraph = $this->getLeadParagraph())) {
            return strip_tags($leadParagraph);
        }


        if (strlen($description = $this->getDescription())) {
            return $description;
        }

        return  substr(strip_tags($this->getContent()), 0, 200);
    }

    /**
     * @return string
     */
    public function getTextTeaser()
    {
        return html_entity_decode($this->getTeaser());
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->getId() . '-' . $this->getPath();
    }
}
