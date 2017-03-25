<?php


namespace AppBundle\Slack;

class Message
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $icon_url;

    /**
     * @var Attachment[]
     */
    private $attachments = [];

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     * @return Message
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Message
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getIconUrl()
    {
        return $this->icon_url;
    }

    /**
     * @param string $icon_url
     * @return Message
     */
    public function setIconUrl($icon_url)
    {
        $this->icon_url = $icon_url;
        return $this;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param Attachment $attachment
     * @return Message
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }
}
