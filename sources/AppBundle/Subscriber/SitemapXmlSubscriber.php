<?php

declare(strict_types=1);

namespace AppBundle\Subscriber;

use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Controller\Website\NewsController;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideo;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapXmlSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RepositoryFactory $ting,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerTalksUrls($event->getUrlContainer());
        $this->registerSpeakersTalksUrls($event->getUrlContainer());
        $this->registerEventsTalksUrls($event->getUrlContainer());
        $this->registerNewsUrls($event->getUrlContainer());
        $this->registerMembers($event->getUrlContainer());
        $this->registerDefaultPages($event->getUrlContainer());
    }

    public function registerTalksUrls(UrlContainerInterface $urls): void
    {
        /** @var Talk[] $talks */
        $talks = $this->ting->get(TalkRepository::class)->getAllPastTalks(new \DateTime());

        foreach ($talks as $talk) {
            if (!$talk->isDisplayedOnHistory()) {
                continue;
            }

            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_show',
                    ['id' => $talk->getId(), 'slug' => $talk->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $talk->getSubmittedOn()
            );
            $urls->addUrl($url,'talks');

            if ($talk->hasYoutubeId()) {
                $video = new GoogleVideo(
                    sprintf('https://img.youtube.com/vi/%s/0.jpg', $talk->getYoutubeId()),
                    $talk->getTitle(),
                    strip_tags(html_entity_decode((string) $talk->getDescription())),
                    ['player_location' => $talk->getYoutubeUrl()]
                );
                $decoratedUrl = new GoogleVideoUrlDecorator($url);
                $decoratedUrl->addVideo($video);
                $urls->addUrl($decoratedUrl,'video');
            }
        }
    }

    public function registerSpeakersTalksUrls(UrlContainerInterface $urls): void
    {
        /** @var Speaker[] $speakers */
        $speakers = $this->ting->get(SpeakerRepository::class)->getAll();
        foreach ($speakers as $speaker) {
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_list',
                    ['fR' => ['speakers.label' => [$speaker->getLabel()]]],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            );
            $urls->addUrl($url,'talks');
        }
    }

    public function registerEventsTalksUrls(UrlContainerInterface $urls): void
    {
        /** @var Event[] $events */
        $events = $this->ting->get(EventRepository::class)->getAll();
        foreach ($events as $event) {
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_list',
                    ['fR' => ['event.title' => [$event->getTitle()]]],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $event->getDateEnd(),
                UrlConcrete::CHANGEFREQ_DAILY,
                1
            );
            $urls->addUrl($url,'talks');
        }
    }

    public function registerNewsUrls(UrlContainerInterface $urls): void
    {
        $articleRepository = $this->ting->get(ArticleRepository::class);

        /** @var Article[] $news */
        $news = $articleRepository->findAllPublishedNews();

        foreach ($news as $article) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'news_display',
                        ['code' => $article->getSlug(),],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    $article->getPublishedAt(),
                    UrlConcrete::CHANGEFREQ_DAILY,
                    1
                ),
                'news'
            );
        }

        $total = $articleRepository->countPublishedNews([]);
        $byPage = NewsController::ARTICLES_PER_PAGE;
        $lastPage = ceil($total / $byPage);

        for ($page = max($lastPage, 1); $page <= $lastPage; $page++) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'news_list',
                        ['page' => $page],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'news'
            );
        }
    }

    private function registerMembers(UrlContainerInterface $urls): void
    {
        /** @var CompanyMember[] $members */
        $members = $this->ting->get(CompanyMemberRepository::class)->findDisplayableCompanies();

        foreach ($members as $member) {
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

    private function registerDefaultPages(UrlContainerInterface $urls): void
    {
        $this->fromFeuilleId(Feuille::ID_FEUILLE_HEADER, $urls);
        $this->fromFeuilleId(Feuille::ID_FEUILLE_FOOTER, $urls);
        $this->fromFeuilleId(Feuille::ID_FEUILLE_NOS_ACTIONS, $urls);
        $this->fromFeuilleId(Feuille::ID_FEUILLE_ANTENNES, $urls);
        $this->fromFeuilleId(Feuille::ID_FEUILLE_ASSOCIATION, $urls);
    }

    private function fromFeuilleId(int $id, UrlContainerInterface $urls): void
    {
        $branche = new Branche();

        $leafs = $branche->feuillesEnfants($id);
        foreach ($leafs as $leaf) {
            if (!$leaf['lien'] || !str_starts_with((string) $leaf['lien'], 'http')) {
                continue;
            }

            $urls->addUrl(
                new UrlConcrete($leaf['lien']),
                'default'
            );
        }
    }
}
