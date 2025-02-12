<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Offices\OfficesCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OfficesExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('office_name', function ($code) {
                $collection = new OfficesCollection();
                return $collection->findByCode($code)['label'];
            }),
            new TwigFunction('office_logo', function ($code) {
                $collection = new OfficesCollection();
                return $collection->findByCode($code)['logo_url'];
            }),
            new TwigFunction('office_meetup_urlname', function ($code) {
                $collection = new OfficesCollection();
                return $collection->findByCode($code)['meetup_urlname'];
            }),
        ];
    }

    public function getName(): string
    {
        return 'offices';
    }
}
