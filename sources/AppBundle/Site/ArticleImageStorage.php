<?php

declare(strict_types=1);

namespace AppBundle\Site;

use AppBundle\Site\Entity\Article;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

final readonly class ArticleImageStorage
{
    public const string PUBLIC_PATH = '/uploads/articles/';

    private Filesystem $filesystem;

    public function __construct(
        #[Autowire('%kernel.project_dir%/htdocs/uploads/articles')]
        private string $basePath,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function store(UploadedFile $file, Article $article): string
    {
        // On supprime d'abord l'image précédente si elle existe
        $this->remove($article);

        $this->createDirectory();

        $fileName = $this->generateFileName($file, $article);
        $file->move($this->basePath, $fileName);

        return $fileName;
    }

    public function remove(Article $article): void
    {
        if ($article->image === null) {
            return;
        }

        $this->filesystem->remove($this->basePath . '/' . $article->image);
    }

    public function getUrl(Article $article): ?string
    {
        if ($article->image === null) {
            return null;
        }

        if (!$this->filesystem->exists($this->basePath . '/' . $article->image)) {
            return null;
        }

        return self::PUBLIC_PATH . $article->image;
    }

    private function generateFileName(UploadedFile $file, Article $article): string
    {
        $slug = (new AsciiSlugger())->slug((string) $article->titre)
            ->lower()
            ->truncate(60)
            ->trim('-')
            ->toString();

        if ($slug === '') {
            $slug = 'article';
        }

        // Pour faire sauter le cache quand l'image est modifiée
        $randomString = bin2hex(random_bytes(4));

        return sprintf('%s-%s.%s', $slug, $randomString, $file->guessExtension() ?? 'jpg',);
    }

    private function createDirectory(): void
    {
        try {
            $this->filesystem->mkdir($this->basePath, 0755);
        } catch (IOException $exception) {
            throw new FileException('Could not create directory for storage', 0, $exception);
        }
    }
}
