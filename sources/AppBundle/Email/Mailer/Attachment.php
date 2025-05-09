<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

class Attachment
{
    /**
     * @param string $name
     * @param string $encoding
     * @param string $path
     * @param string $type
     */
    public function __construct(
        private $path,
        private $name,
        private $encoding,
        private $type,
    ) {
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
