<?php


namespace AppBundle\Slack;


class Attachment
{
    /**
     * @var string
     */
    private $fallback;

    /**
     * @var string
     */
    private $pretext;

    /**
     * @var string
     */
    private $author_name;

    /**
     * @var string
     */
    private $author_link;

    /**
     * @var string
     */
    private $author_icon;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $title_link;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $color;

    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var array
     */
    private $mrkdwn_in = [];

    /**
     * @return string
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * @param string $fallback
     * @return Attachment
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * @return string
     */
    public function getPretext()
    {
        return $this->pretext;
    }

    /**
     * @param string $pretext
     * @return Attachment
     */
    public function setPretext($pretext)
    {
        $this->pretext = $pretext;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * @param string $author_name
     * @return Attachment
     */
    public function setAuthorName($author_name)
    {
        $this->author_name = $author_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorLink()
    {
        return $this->author_link;
    }

    /**
     * @param string $author_link
     * @return Attachment
     */
    public function setAuthorLink($author_link)
    {
        $this->author_link = $author_link;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorIcon()
    {
        return $this->author_icon;
    }

    /**
     * @param string $author_icon
     * @return Attachment
     */
    public function setAuthorIcon($author_icon)
    {
        $this->author_icon = $author_icon;
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
     * @return Attachment
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitleLink()
    {
        return $this->title_link;
    }

    /**
     * @param string $title_link
     * @return Attachment
     */
    public function setTitleLink($title_link)
    {
        $this->title_link = $title_link;
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
     * @return Attachment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Attachment
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field $field
     * @return Attachment
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return array
     */
    public function getMrkdwnIn()
    {
        return $this->mrkdwn_in;
    }

    /**
     * @param array $mrkdwn_in
     * @return Attachment
     */
    public function setMrkdwnIn($mrkdwn_in)
    {
        $this->mrkdwn_in = $mrkdwn_in;
        return $this;
    }
}
