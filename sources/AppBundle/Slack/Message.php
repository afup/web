<?php

declare(strict_types=1);

namespace AppBundle\Slack;

final class Message
{
    private ?string $channel = null;
    private ?string $username = null;
    private ?string $text = null;
    private ?string $iconUrl = null;

    /** @var Attachment[] */
    private array $attachments = [];

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
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

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(string $iconUrl): self
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        $this->attachments[] = $attachment;
        return $this;
    }
}
