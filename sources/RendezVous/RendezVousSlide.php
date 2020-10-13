<?php

namespace App\RendezVous;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class RendezVousSlide implements NotifyPropertyInterface
{
    use NotifyProperty;

    /** @var int */
    private $id;
    /** @var int */
    private $rendezVousId;
    /** @var string */
    private $file;
    /** @var string */
    private $url;

    /** @return int */
    public function getId()
    {
        return $this->id;
    }

    /** @param int $id */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getRendezVousId()
    {
        return $this->rendezVousId;
    }

    public function setRendezVousId($rendezVousId)
    {
        $this->propertyChanged('rendezVousId', $this->rendezVousId, $rendezVousId);
        $this->rendezVousId = $rendezVousId;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->propertyChanged('file', $this->file, $file);
        $this->file = $file;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->propertyChanged('url', $this->url, $url);
        $this->url = $url;
    }
}
