<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Global;

use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Entity\Repository\CompanyMemberRepository;
use AppBundle\Event\Talk\PublishedTalkRepository;
use AppBundle\Event\Talk\EventTalksSummary;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Site\Entity\Repository\FeuilleRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HtmlSitemapAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly PublishedTalkRepository $publishedTalkRepository,
        private readonly FeuilleRepository $feuilleRepository,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/sitemap.html.twig', [
            'pages' => $this->buildPages(Feuille::ID_FEUILLE_HEADER),
            'association' => $this->buildPages(Feuille::ID_FEUILLE_ANTENNES),
            'members' => $this->members(),
            'news' => $this->news(),
            'summaries_by_year' => EventTalksSummary::groupByYear($this->publishedTalkRepository->getEventSummaries()),
        ]);
    }

    private function buildPages(int $id): array
    {
        $feuilles = $this->feuilleRepository->getFeuillesEnfant($id);

        $pages = [];
        foreach ($feuilles as $feuille) {
            if (!$feuille->lien || str_starts_with((string) $feuille->lien, 'http')) {
                continue;
            }
            $pages[] = [
                'name' => $feuille->nom,
                'url' => $feuille->lien,
            ];
        }

        return $pages;
    }

    private function members(): array
    {
        $displayableCompanies = $this->companyMemberRepository->findDisplayableCompanies();

        $members = [];
        foreach ($displayableCompanies as $member) {
            $url = $this->generateUrl('company_public_profile', [
                'id' => $member->id,
                'slug' => $member->getSlug(),
            ]);

            $members[] = [
                'name' => $member->companyName,
                'url' => $url,
            ];
        }
        return $members;
    }

    private function news(): array
    {
        $news = [];
        $newsList = $this->articleRepository->findAllPublishedArticles();
        foreach ($newsList as $newsItem) {
            $url = $this->generateUrl('news_display', [
                'code' => $newsItem->getSlug(),
            ]);

            $news[] = [
                'name' => $newsItem->titre,
                'url' => $url,
            ];
        }

        return $news;
    }
}
