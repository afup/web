<?php

declare(strict_types=1);


namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $kernelProjectDir,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset_md5_start', function (string $url) {
                $path = $this->kernelProjectDir . '/../htdocs/' . $url;

                return substr(md5_file($path), 0, 8);
            }, ['is_safe' => ['html']]),
        ];
    }

    public function getName(): string
    {
        return 'assets';
    }
}
