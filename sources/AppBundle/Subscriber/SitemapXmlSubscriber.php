<?php

declare(strict_types=1);

namespace AppBundle\Subscriber;

use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
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
        private readonly PlanningRepository $planningRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly EventRepository $eventRepository,
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
        $publishedTalks = $this->getPublishedTalks();

        $this->registerTalksUrls($event->getUrlContainer(), $publishedTalks);
        $this->registerSpeakersTalksUrls($event->getUrlContainer(), $publishedTalks);
        $this->registerEventsTalksUrls($event->getUrlContainer(), $publishedTalks);
        $this->registerNewsUrls($event->getUrlContainer());
        $this->registerMembers($event->getUrlContainer());
        $this->registerDefaultPages($event->getUrlContainer());
    }

    /**
     * Talks passés visibles dans l'historique, indexés par identifiant
     * (un talk planifié sur plusieurs créneaux n'apparaît qu'une fois).
     *
     * @return array<int, Talk>
     */
    private function getPublishedTalks(): array
    {
        $publishedTalks = [];

        /** @var iterable<Talk> $pastTalks */
        $pastTalks = $this->talkRepository->getAllPastTalks(new \DateTime());

        foreach ($pastTalks as $talk) {
            $talkId = $talk->getId();
            if ($talkId !== null && $talk->isDisplayedOnHistory()) {
                $publishedTalks[$talkId] = $talk;
            }
        }

        return $publishedTalks;
    }

    /**
     * @param array<int, Talk> $publishedTalks
     */
    private function registerTalksUrls(UrlContainerInterface $urls, array $publishedTalks): void
    {
        $plannings = $this->planningRepository->findIndexedByTalkIds(array_keys($publishedTalks));

        foreach ($publishedTalks as $talkId => $talk) {
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'talks_show',
                    ['id' => $talkId, 'slug' => $talk->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                ),
                $plannings[$talkId]->end ?? null,
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
     * Facettes speaker de la liste des talks : uniquement les speakers ayant
     * au moins un talk publié, une seule URL par label normalisé.
     *
     * @param array<int, Talk> $publishedTalks
     */
    private function registerSpeakersTalksUrls(UrlContainerInterface $urls, array $publishedTalks): void
    {
        $labels = [];
        foreach ($this->speakerRepository->getSpeakersByTalkIds(array_keys($publishedTalks)) as $speaker) {
            $label = self::normalizeLabel($speaker->getLabel());
            if ($label !== '') {
                $labels[$label] ??= $speaker->getLabel();
            }
        }

        foreach ($labels as $label) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'talks_list',
                        ['fR' => ['speakers.label' => [$label]]],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ),
                'talks',
            );
        }
    }

    /**
     * Facettes événement de la liste des talks : uniquement les événements
     * ayant au moins un talk publié, une seule URL par titre.
     *
     * @param array<int, Talk> $publishedTalks
     */
    private function registerEventsTalksUrls(UrlContainerInterface $urls, array $publishedTalks): void
    {
        $eventIds = array_map(static fn(Talk $talk): ?int => $talk->getForumId(), $publishedTalks);

        $registeredTitles = [];
        /** @var Event $event */
        foreach ($this->eventRepository->getAll() as $event) {
            $title = $event->getTitle();
            if (!in_array($event->getId(), $eventIds, true) || in_array($title, $registeredTitles, true)) {
                continue;
            }
            $registeredTitles[] = $title;

            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'talks_list',
                        ['fR' => ['event.title' => [$title]]],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                    $event->getDateEnd(),
                ),
                'talks',
            );
        }
    }

    private function registerNewsUrls(UrlContainerInterface $urls): void
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
    }

    private static function normalizeLabel(string $label): string
    {
        return (string) preg_replace('/\s+/u', ' ', trim($label));
    }

    private function registerMembers(UrlContainerInterface $urls): void
    {
        /** @var CompanyMember[] $members */
        $members = $this->companyMemberRepository->findDisplayableCompanies();

        foreach ($members as $member) {
            $urls->addUrl(
                new UrlConcrete(
                    $this->urlGenerator->generate(
                        'company_public_profile',
                        [
                            'id' => $member->getId(),
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
