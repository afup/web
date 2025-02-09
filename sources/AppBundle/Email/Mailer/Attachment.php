<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

class Attachment
{
    /** @var string */
    private $name;
    /** @var string */
    private $encoding;
    /** @var string */
    private $path;
    /** @var string */
    private $type;

    /**
     * @param string $name
     */
    public function __construct($path, $name, $encoding, $type)
    {
        $this->path = $path;
        $this->name = $name;
        $this->encoding = $encoding;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
