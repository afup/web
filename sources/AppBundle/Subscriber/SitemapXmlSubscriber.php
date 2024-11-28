<?php

namespace AppBundle\Subscriber;

use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapXmlSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event)
    {
        $this->registerTalksUrls($event->getUrlContainer());
        $this->registerNewsUrls($event->getUrlContainer());
        $this->registerMembers($event->getUrlContainer());
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

    public function registerNewsUrls(UrlContainerInterface $urls)
    {
        $news = $this->ting->get(ArticleRepository::class)->findAllPublishedNews();

        /** @var Article $article */
        foreach ($news as $article) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'news_display',
                        ['code' => $article->getSlug(),],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    $article->getPublishedAt()
                ),
                'news'
            );
        }
    }

    private function registerMembers(UrlContainerInterface $urls)
    {
        /**
         * @var CompanyMemberRepository $companyRepository
         */
        $companyRepository = $this->ting->get(CompanyMemberRepository::class);
        $displayableCompanies = $companyRepository->findDisplayableCompanies();

        foreach ($displayableCompanies as $member) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'company_public_profile',
                        [
                            'id' => $member->getId(),
                            'slug' => $member->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'members'
            );
        }
    }
}
