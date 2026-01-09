<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Global;

use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Site\Model\Repository\SheetRepository;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HtmlSitemapAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly TalkRepository $talkRepository,
        private readonly SheetRepository $sheetRepository,
    ) {}

    public function __invoke(): Response
    {
        return $this->view->render('site/sitemap.html.twig', [
            'pages' => $this->buildLeafs(Feuille::ID_FEUILLE_HEADER),
            'association' => $this->buildLeafs(Feuille::ID_FEUILLE_ANTENNES),
            'members' => $this->members(),
            'news' => $this->news(),
            'talks' => $this->talks(),
        ]);
    }

    private function buildLeafs(int $id): array
    {
        $leafs = $this->sheetRepository->getActiveChildrenByParentId($id);

        $pages = [];
        foreach ($leafs as $leaf) {
            if (!$leaf['lien'] || str_starts_with((string) $leaf['lien'], 'http')) {
                continue;
            }
            $pages[] = [
                'name' => $leaf['nom'],
                'url' => $leaf['lien'],
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
                'id' => $member->getId(),
                'slug' => $member->getSlug(),
            ]);

            $members[] = [
                'name' => $member->getCompanyName(),
                'url' => $url,
            ];
        }
        return $members;
    }

    private function news(): array
    {
        $news = [];
        $newsList = $this->articleRepository->findAllPublishedNews();
        foreach ($newsList as $newsItem) {
            $url = $this->generateUrl('news_display', [
                'code' => $newsItem->getSlug(),
            ]);

            $news[] = [
                'name' => $newsItem->getTitle(),
                'url' => $url,
            ];
        }

        return $news;
    }

    private function talks(): array
    {
        $talks = [];
        $talkList = $this->talkRepository->getAllPastTalks((new \DateTime())->setTime(29,59,59));

        /** @var Talk $talk */
        foreach ($talkList as $talk) {
            $url = $this->generateUrl(
                'talks_show',
                ['id' => $talk->getId(), 'slug' => $talk->getSlug()],
            );

            $talks[] = [
                'name' => $talk->getTitle(),
                'url' => $url,
            ];
        }

        return $talks;
    }
}
