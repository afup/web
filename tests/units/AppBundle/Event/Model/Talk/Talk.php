<?php

namespace AppBundle\Event\Model\tests\units;

use AppBundle\Event\Model\Talk as TestedClass;

class Talk extends \atoum
{
    public function testYoutubeUrl()
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

}
