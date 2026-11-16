<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Antennes\OfficesMapSvgGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'antennes:generate-map',
    description: 'Génère la carte SVG de la page des antennes, à relancer après chaque modification de AntenneRepository',
)]
class GenerateOfficesMapCommand extends Command
{
    public const string MAP_TEMPLATE = 'site/partials/_offices_map.svg';

    public function __construct(
        private readonly AntenneRepository $antenneRepository,
        private readonly OfficesMapSvgGenerator $generator,
        private readonly Filesystem $filesystem,
        #[Autowire('%kernel.project_dir%/templates/')]
        private readonly string $templatesDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $this->templatesDir . self::MAP_TEMPLATE;

        $this->filesystem->dumpFile(
            $path,
            $this->generator->generate($this->antenneRepository->getAllSortedByLabels()),
        );

        new SymfonyStyle($input, $output)->success(sprintf('Carte générée dans %s', $path));

        return Command::SUCCESS;
    }
}
