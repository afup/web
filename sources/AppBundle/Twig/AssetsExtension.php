<?php

declare(strict_types=1);


namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension implements GlobalsInterface
{
    private $kernelProjectDir;

    public function __construct($kernelProjectDir)
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('asset_md5_start', function (string $url) {
                $path = $this->kernelProjectDir . '/../htdocs/' . $url;

                return substr(md5_file($path), 0, 8);
            }, ['is_safe' => ['html']])
        ];
    }

    public function getName(): string
    {
        return 'assets';
    }

    public function getGlobals(): array
    {
        return [];
    }
}
