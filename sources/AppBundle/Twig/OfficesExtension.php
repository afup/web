<?php

declare(strict_types=1);

namespace AppBundle\Twig;

use AppBundle\Offices\OfficesCollection;

class OfficesExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('office_name', function ($code) {
                $collection = new OfficesCollection();
                return $collection->findByCode($code)['label'];
            }),
            new \Twig_SimpleFunction('office_logo', function ($code) {
                $collection = new OfficesCollection();
                return $collection->findByCode($code)['logo_url'];
            }),
            new \Twig_SimpleFunction('office_meetup_urlname', function ($code) {
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
