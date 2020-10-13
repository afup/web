<?php

namespace App\RendezVous\Admin\PrepareRendezVous;

use App\RendezVous\RendezVousSlide;
use BadMethodCallException;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PrepareRendezVousFormData
{
    /** @var DateTimeInterface|null */
    public $date;
    /** @var UploadedFile|null */
    public $slide1;
    /** @var UploadedFile|null */
    public $slide2;
    /** @var UploadedFile|null */
    public $slide3;
    /** @var UploadedFile|null */
    public $slide4;
    /** @var string */
    public $slide1Url;
    /** @var string */
    public $slide2Url;
    /** @var string */
    public $slide3Url;
    /** @var string */
    public $slide4Url;
    /** @var string */
    public $start;
    /** @var string */
    public $end;
    /** @var string */
    public $title;
    /** @var string */
    public $theme;
    /** @var string */
    public $externalUrl;
    /** @var string */
    public $plan;
    /** @var string */
    public $place;
    /** @var string */
    public $pitch;
    /** @var string */
    public $address;
    /** @var string */
    public $url;
    /** @var int */
    public $officeId;
    /** @var int */
    public $capacity;
    /** @var bool */
    public $registration = false;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
    }

    public function addSlideUrl(RendezVousSlide $slide)
    {
        foreach ([&$this->slide1Url, &$this->slide2Url, &$this->slide3Url, &$this->slide4Url] as &$slideUrl) {
            if (null === $slideUrl) {
                $slideUrl = $slide->getUrl();

                return;
            }
        }
        throw new BadMethodCallException('Slides full');
    }

    /**
     * @return array<array{file: UploadedFile, url: string}>
     */
    public function getSlides()
    {
        $slides = [];
        foreach ([
                     ['file' => $this->slide1, 'url' => $this->slide1Url],
                     ['file' => $this->slide2, 'url' => $this->slide2Url],
                     ['file' => $this->slide3, 'url' => $this->slide3Url],
                     ['file' => $this->slide4, 'url' => $this->slide4Url],
                 ] as $slide) {
            if (null !== $slide['file'] || null !== $slide['url']) {
                $slides[] = $slide;
            }
        }

        return $slides;
    }
}
