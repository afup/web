<?php

namespace AppBundle\Subscriber;

use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TalksSitemapSubscriber implements EventSubscriberInterface
{
    /** @var RepositoryFactory */
    private $ting;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, RepositoryFactory $ting)
    {
        $this->urlGenerator = $urlGenerator;
        $this->ting = $ting;
    }

    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event)
    {
        $this->registerTalksUrls($event->getUrlContainer());
    }

    public function registerTalksUrls(UrlContainerInterface $urls)
    {
        $talks = $this->ting->get(TalkRepository::class)->getAllPastTalks(new \DateTime());

        /** @var Talk $talk */
        foreach ($talks as $talk) {
            if ($talk->isDisplayedOnHistory()) {
                $urls->addUrl(
                    new UrlConcrete(
                        $this->urlGenerator->generate(
                            'talks_show',
                            ['id' => $talk->getId(), 'slug' => $talk->getSlug()],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                        $talk->getSubmittedOn()
                    ),
                    'talks'
                );
            }
        }
    }
}
