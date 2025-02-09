<?php

declare(strict_types=1);

namespace AppBundle\Slack;

final class Attachment
{
    private ?string $fallback = null;
    private ?string $pretext = null;
    private ?string $authorName = null;
    private ?string $authorLink = null;
    private ?string $authorIcon = null;
    private ?string $title = null;
    private ?string $titleLink = null;
    private ?string $text = null;
    private ?string $color = null;

    /** @var Field[] */
    private array $fields = [];

    /** @var string[] */
    private array $mrkdwnIn = [];

    public function getFallback(): ?string
    {
        return $this->fallback;
    }

    public function setFallback(string $fallback): self
    {
        $this->fallback = $fallback;
        return $this;
    }

    public function getPretext(): ?string
    {
        return $this->pretext;
    }

    public function setPretext(string $pretext): self
    {
        $this->pretext = $pretext;
        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): self
    {
        $this->authorName = $authorName;
        return $this;
    }

    public function getAuthorLink(): ?string
    {
        return $this->authorLink;
    }

    public function setAuthorLink(string $authorLink): self
    {
        $this->authorLink = $authorLink;
        return $this;
    }

    public function getAuthorIcon(): ?string
    {
        return $this->authorIcon;
    }

    public function setAuthorIcon(string $authorIcon): self
    {
        $this->authorIcon = $authorIcon;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitleLink(): ?string
    {
        return $this->titleLink;
    }

    public function setTitleLink(string $titleLink): self
    {
        $this->titleLink = $titleLink;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getMrkdwnIn(): array
    {
        return $this->mrkdwnIn;
    }

    /**
     * @param string[] $mrkdwnIn
     */
    public function setMrkdwnIn(array $mrkdwnIn): self
    {
        $this->mrkdwnIn = $mrkdwnIn;
        return $this;
    }
}
