<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Speaker;
use AppBundle\SocialNetwork\Bluesky\BlueskyTransport;
use AppBundle\SocialNetwork\Embed;
use AppBundle\SocialNetwork\Status;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class VideoNotifierCommand extends Command
{
    private BlueskyTransport $blueskyNotifier;

    public function __construct(BlueskyTransport $blueskyNotifier)
    {
        parent::__construct();

        $this->blueskyNotifier = $blueskyNotifier;
    }

    protected function configure(): void
    {
        $this
            ->setName('plop')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $speaker = new Speaker();
        $speaker->setId(1);
        $speaker->setBluesky('mopolo.dev');

        $this->blueskyNotifier->send(new Status(
            'Lorem ipsum @mopolo.dev @maheurt.bsky.social dolor si amet',
            new Embed(
                'https://example.com',
                'Piochons dans les pratiques de DDD, programmation fonctionnelle and co. pour notre bien à toutes et tous !',
                "On n’a pas besoin d’utiliser 100% de « X » pour utiliser des pratiques/techniques de « X ».\n Remplacez « X » par DDD, programmation fonctionnelle, CQRS, CQS, Hexagonal/Clean/Onion architecture et bien d’autres. Il n'y a pas de méthode ou technique qui soit LA réponse dans tous les cas.\nChacun(e) a ses avantages et inconvénients. Chacun(e) impacte notre façon de penser, notre façon de communiquer au sein de l’équipe. Chacun(e) nous apprend des choses, techniques ou humaines etc..",
                'https://i.ytimg.com/vi_webp/reN01m9Gato/maxresdefault.webp',
            ),
        ));

        return 0;
    }
}
