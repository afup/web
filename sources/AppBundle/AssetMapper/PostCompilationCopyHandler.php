<?php

declare(strict_types=1);

namespace AppBundle\AssetMapper;

use Symfony\Component\AssetMapper\Compressor\CompressorInterface;
use Symfony\Component\AssetMapper\Path\PublicAssetsFilesystemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

/**
 * cf. https://symfony.com/doc/current/frontend/asset_mapper.html#serving-assets-in-dev-vs-prod
 */
class PostCompilationCopyHandler implements PublicAssetsFilesystemInterface
{
    private Filesystem $filesystem;

    /**
     * @param string[] $extensionsToCompress
     */
    public function __construct(
        #[Autowire('%kernel.project_dir%/../htdocs')]
        private readonly string $publicDir,
        private readonly ?CompressorInterface $compressor = null,
        private readonly array $extensionsToCompress = [],
    ) {
        $this->filesystem = new Filesystem();
    }

    public function write(string $path, string $contents): void
    {
        $targetPath = $this->publicDir . '/' . ltrim($path, '/');

        $this->filesystem->dumpFile($targetPath, $contents);
        $this->compress($targetPath);
    }

    public function copy(string $originPath, string $path): void
    {
        $targetPath = $this->publicDir . '/' . ltrim($path, '/');

        $this->filesystem->copy($originPath, $targetPath, true);
        $this->compress($targetPath);
    }

    public function getDestinationPath(): string
    {
        return $this->publicDir;
    }

    private function compress(string $targetPath): void
    {
        foreach ($this->extensionsToCompress as $ext) {
            if (!str_ends_with($targetPath, ".$ext")) {
                continue;
            }

            $this->compressor?->compress($targetPath);

            return;
        }
    }
}
