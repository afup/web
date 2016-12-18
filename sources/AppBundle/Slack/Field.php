<?php


namespace AppBundle\Slack;


class Field
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $short = true;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Field
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Field
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShort()
    {
        return $this->short;
    }

    /**
     * @param boolean $short
     * @return Field
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }
}
