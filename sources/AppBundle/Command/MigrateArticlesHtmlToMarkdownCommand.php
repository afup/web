<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Site\Enum\ArticleContentType;
use AppBundle\Site\Model\Repository\ArticleRepository;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MigrateArticlesHtmlToMarkdownCommand extends Command
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:articles:migrate-html-to-markdown')
            ->setDescription('Converts HTML articles to Markdown and updates their contentType field.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview changes without saving to database')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Maximum number of articles to process (0 = all)', 0)
            ->addOption('article-id', null, InputOption::VALUE_REQUIRED, 'Process a single article by its ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Migration des articles HTML vers Markdown');

        $dryRun = (bool) $input->getOption('dry-run');
        $limit = (int) $input->getOption('limit');
        $articleId = (int) $input->getOption('article-id');

        if ($dryRun) {
            $io->note('Mode dry-run activé : aucune modification ne sera sauvegardée.');
        }

        $converter = new HtmlConverter([
            'strip_tags' => false,
            'remove_nodes' => 'script style',
            'hard_break' => true,
            'use_autolinks' => false,
            'preserve_comments' => false,
        ]);

        if ($articleId !== 0) {
            $article = $this->articleRepository->findOneHtmlArticleById($articleId);
            if ($article === null) {
                $io->warning(sprintf('Article #%d introuvable ou déjà en Markdown.', $articleId));
                return Command::SUCCESS;
            }
            $articles = [$article];
        } else {
            $articles = $this->articleRepository->findAllHtmlArticles($limit);
        }

        $processed = 0;
        $errors = 0;

        foreach ($articles as $article) {
            try {
                $convertedContent = $this->convertField($converter, (string) $article->getContent());
                $convertedLead = $this->convertLeadParagraph($converter, $article->getLeadParagraph());

                if ($output->isVerbose()) {
                    $io->section(sprintf('Article #%d — %s', $article->getId(), $article->getTitle()));
                    $io->text('Aperçu contenu : ' . substr($convertedContent, 0, 200));
                }

                $article->setContent($convertedContent);
                $article->setLeadParagraph($convertedLead);
                $article->setContentType(ArticleContentType::Markdown->value);

                if (!$dryRun) {
                    $this->articleRepository->save($article);
                }

                $processed++;
            } catch (\Throwable $e) {
                $io->error(sprintf(
                    'Erreur lors de la conversion de l\'article #%d (%s) : %s',
                    $article->getId(),
                    $article->getTitle(),
                    $e->getMessage(),
                ));
                $errors++;
            }
        }

        $io->definitionList(
            ['Articles convertis' => $processed],
            ['Erreurs' => $errors],
        );

        if ($errors > 0) {
            $io->warning(sprintf('%d article(s) n\'ont pas pu être convertis.', $errors));
        }

        if ($dryRun) {
            $io->note('Aucune modification n\'a été sauvegardée (dry-run).');
        } else {
            $io->success(sprintf('%d article(s) migrés avec succès.', $processed));
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function convertField(HtmlConverter $converter, string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        return trim($converter->convert($html));
    }

    private function convertLeadParagraph(HtmlConverter $converter, mixed $leadParagraph): string
    {
        if ($leadParagraph === null || trim((string) $leadParagraph) === '') {
            return '';
        }

        return trim($converter->convert((string) $leadParagraph));
    }
}
