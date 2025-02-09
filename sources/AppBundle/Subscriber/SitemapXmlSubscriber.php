<?php

declare(strict_types=1);

namespace AppBundle\Subscriber;

use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleVideoUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapXmlSubscriber implements EventSubscriberInterface
{
    private RepositoryFactory $ting;
    private UrlGeneratorInterface $urlGenerator;

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

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerTalksUrls($event->getUrlContainer());
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
                $urlVideo = new GoogleVideoUrlDecorator(
                    $url,
                    sprintf('https://img.youtube.com/vi/%s/0.jpg', $talk->getYoutubeId()),
                    $talk->getTitle(),
                    strip_tags(html_entity_decode($talk->getDescription())),
                    ['content_loc' => $talk->getYoutubeUrl()]
                );
                $urls->addUrl($urlVideo,'video');
            }
        }
    }

    public function registerNewsUrls(UrlContainerInterface $urls): void
    {
        /** @var Article[] $news */
        $news = $this->ting->get(ArticleRepository::class)->findAllPublishedNews();

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
            if (!$leaf['lien'] || 0 !== strpos($leaf['lien'], 'http')) {
                continue;
            }

            $urls->addUrl(
                new UrlConcrete($leaf['lien']),
                'default'
            );
        }
    }
}
