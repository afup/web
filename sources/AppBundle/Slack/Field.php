<?php

declare(strict_types=1);

namespace AppBundle\Slack;

final class Field
{
    private ?string $title = null;
    private ?string $value = null;
    private bool $short = true;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = (string) $value;
        return $this;
    }

    public function isShort(): bool
    {
        return $this->short;
    }

    public function setShort(bool $short): self
    {
        $this->short = $short;
        return $this;
    }
}
