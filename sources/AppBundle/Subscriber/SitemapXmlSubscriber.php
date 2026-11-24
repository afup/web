<?php

declare(strict_types=1);

namespace AppBundle\Subscriber;

use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Controller\Website\News\ListAction;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Talk\PublishedTalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use AppBundle\Site\Entity\Repository\ArticleRepository;
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
        private readonly TalkRepository $talkRepository,
        private readonly PublishedTalkRepository $publishedTalkRepository,
        private readonly SpeakerProfileResolver $speakerProfileResolver,
        private readonly ArticleRepository $articleRepository,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly FeuilleRepository $feuilleRepository,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerTalksUrls($event->getUrlContainer());
        $this->registerSpeakersUrls($event->getUrlContainer());
        $this->registerEventsTalksUrls($event->getUrlContainer());
        $this->registerNewsUrls($event->getUrlContainer());
        $this->registerMembers($event->getUrlContainer());
        $this->registerDefaultPages($event->getUrlContainer());
    }

    public function registerTalksUrls(UrlContainerInterface $urls): void
    {
        // Une entrée par talk publié, même planifié sur plusieurs créneaux.
        $publishedTalkIds = $this->publishedTalkRepository->getIds();
        $talks = $publishedTalkIds === [] ? [] : $this->talkRepository->findList($publishedTalkIds);

        foreach ($talks as $talk) {

            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_show',
                    ['id' => $talk->getId(), 'slug' => $talk->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                $talk->getSubmittedOn(),
            );
            $urls->addUrl($url, 'talks');

            if ($talk->hasYoutubeId()) {
                $video = new GoogleVideo(
                    sprintf('https://img.youtube.com/vi/%s/0.jpg', $talk->getYoutubeId()),
                    $talk->getTitle(),
                    strip_tags(html_entity_decode((string) $talk->getDescription())),
                    ['player_location' => $talk->getYoutubeUrl()],
                );
                $decoratedUrl = new GoogleVideoUrlDecorator($url);
                $decoratedUrl->addVideo($video);
                $urls->addUrl($decoratedUrl, 'video');
            }
        }
    }

    /**
     * La page de chaque speaker ayant au moins un talk publié ; l'index des speakers
     * est déclaré par l'option sitemap de sa route.
     */
    public function registerSpeakersUrls(UrlContainerInterface $urls): void
    {
        foreach ($this->speakerProfileResolver->getIdentities() as $identity) {
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_speaker',
                    ['slug' => $identity->slug],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                $identity->latestTalkAt,
            );
            $urls->addUrl($url, 'talks');
        }
    }

    /**
     * La page de chaque édition ayant au moins un talk publié ; l'index des éditions
     * est déclaré par l'option sitemap de sa route.
     */
    public function registerEventsTalksUrls(UrlContainerInterface $urls): void
    {
        foreach ($this->publishedTalkRepository->getEventSummaries() as $summary) {
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_event',
                    ['path' => $summary->event->path],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                $summary->event->dateEnd,
                UrlConcrete::CHANGEFREQ_MONTHLY,
            );
            $urls->addUrl($url, 'talks');
        }
    }

    public function registerNewsUrls(UrlContainerInterface $urls): void
    {
        $news = $this->articleRepository->findAllPublishedArticles();

        foreach ($news as $article) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'news_display',
                        ['code' => $article->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    $article->datePublication,
                    UrlConcrete::CHANGEFREQ_DAILY,
                    1,
                ),
                'news',
            );
        }

        $total = $this->articleRepository->countPublishedArticles([]);
        $byPage = ListAction::ARTICLES_PER_PAGE;
        $lastPage = ceil($total / $byPage);

        for ($page = max($lastPage, 1); $page <= $lastPage; $page++) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'news_list',
                        ['page' => $page],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
                'news',
            );
        }
    }

    private function registerMembers(UrlContainerInterface $urls): void
    {
        $members = $this->companyMemberRepository->findDisplayableCompanies();

        foreach ($members as $member) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'company_public_profile',
                        [
                            'id' => $member->id,
                            'slug' => $member->getSlug(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
                'members',
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
        $feuilles = $this->feuilleRepository->getFeuillesEnfant($id);
        foreach ($feuilles as $feuille) {
            if (!$feuille->lien || !str_starts_with((string) $feuille->lien, 'http')) {
                continue;
            }

            $urls->addUrl(
                new UrlConcrete($feuille->lien),
                'default',
            );
        }
    }
}
