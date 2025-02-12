<?php

declare(strict_types=1);


namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension implements GlobalsInterface
{
    private $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('asset_md5_start', function (string $url) {
                $path = $this->kernelRootDir . '/../htdocs/' . $url;

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
