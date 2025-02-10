<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\tests\units;

use AppBundle\Event\Model\Speaker as TestedClass;

class Speaker extends \atoum
{
    public function testMastodon(): void
    {
        $data = [
            ['', '', ''],
            ['https://phpc.social/@username', 'username', 'https://phpc.social/@username'],
            ['https://mastodon.social/@username', 'username', 'https://mastodon.social/@username'],
            ['https://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
            ['http://@username@mastodon.social', 'username', 'https://mastodon.social/@username'],
        ];

        foreach ($data as $expected) {
            $this
                ->given($speaker = new TestedClass())
                ->when($speaker->setMastodon($expected[0]))
                ->then
                ->string($speaker->getUsernameMastodon())
                    ->isEqualTo($expected[1])
                ->string($speaker->getUrlMastodon())
                    ->isEqualTo($expected[2]);
        }
    }


    public function testTwitter(): void
    {
        $data = [
            ['', '', ''],
            ['https://twitter.com/username', 'username', 'https://x.com/username'],
            ['https://x.com/username', 'username', 'https://x.com/username'],
            ['http://twitter.com/username', 'username', 'https://x.com/username'],
        ];

        foreach ($data as $expected) {
            $this
                ->given($speaker = new TestedClass())
                ->when($speaker->setTwitter($expected[0]))
                ->then
                ->string($speaker->getUsernameTwitter())
                    ->isEqualTo($expected[1])
                ->string($speaker->getUrlTwitter())
                    ->isEqualTo($expected[2]);
        }
    }
}
