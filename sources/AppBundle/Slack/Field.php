<?php

declare(strict_types=1);

namespace AppBundle\Slack;

final class Field
{
    private ?string $title;
    private ?string $value;
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

    public function setValue(string $value): self
    {
        $this->value = $value;
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
