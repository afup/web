<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\tests\units;

use AppBundle\Event\Model\Talk as TestedClass;

class Talk extends \atoum
{
    public function testYoutubeUrl(): void
    {
        $this
            ->given($talk = new TestedClass())
            ->then
                ->variable($talk->getYoutubeUrl())
                    ->isNull()
                ->when($talk->setYoutubeId("bWi9h2PmBn0"))
                    ->string($talk->getYoutubeUrl())
                        ->isEqualTo("https://www.youtube.com/watch?v=bWi9h2PmBn0")
        ;
    }

    public function testSlug(): void
    {
        $this
            ->given($talk = new TestedClass())
            ->and(
                $talk->setId(1007),
                $talk->setTitle('Utiliser PostgreSQL en 2014')
            )
            ->then
                ->variable($talk->getSlug())
                ->isEqualTo('utiliser-postgresql-en-2014')
        ;
    }
}
