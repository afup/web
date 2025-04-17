<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website;

use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Twig\ViewRenderer;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HtmlSitemapController extends AbstractController
{
    private ViewRenderer $view;
    private RepositoryFactory $repositoryFactory;

    public function __construct(ViewRenderer $view,
                                RepositoryFactory $repositoryFactory
    ) {
        $this->view = $view;
        $this->repositoryFactory = $repositoryFactory;
    }

    public function display(): Response
    {
        $branche = new Branche();

        return $this->view->render('site/sitemap.html.twig', [
            'pages' => $this->buildLeafs($branche, Feuille::ID_FEUILLE_HEADER),
            'association' => $this->buildLeafs($branche, Feuille::ID_FEUILLE_ANTENNES),
            'members' => $this->members(),
            'news' => $this->news(),
            'talks' => $this->talks(),
        ]);
    }

    private function buildLeafs(Branche $branche, int $id): array
    {
        $leafs = $branche->feuillesEnfants($id);

        $pages = [];
        foreach ($leafs as $leaf) {
            if (!$leaf['lien'] || str_starts_with($leaf['lien'], 'http')) {
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
        /**
         * @var CompanyMemberRepository $companyRepository
         */
        $companyRepository = $this->repositoryFactory->get(CompanyMemberRepository::class);
        $displayableCompanies = $companyRepository->findDisplayableCompanies();

        $members = [];
        foreach ($displayableCompanies as $member) {
            $url = $this->generateUrl('company_public_profile', [
                'id' => $member->getId(),
                'slug' => $member->getSlug(),
            ]);

            $members[] = [
                'name' => $member->getCompanyName(),
                'url' => $url
            ];
        }
        return $members;
    }

    private function news(): array
    {
        $repository = $this->repositoryFactory->get(ArticleRepository::class);

        $news = [];
        $newsList = $repository->findAllPublishedNews();
        foreach ($newsList as $newsItem) {
            $url = $this->generateUrl('news_display', [
                'code' => $newsItem->getSlug(),
            ]);

            $news[] = [
                'name' => $newsItem->getTitle(),
                'url' => $url
            ];
        }

        return $news;
    }

    private function talks(): array
    {
        $repository = $this->repositoryFactory->get(TalkRepository::class);

        $talks = [];
        $talkList = $repository->getAllPastTalks((new \DateTime())->setTime(29,59,59));

        /** @var Talk $talk */
        foreach ($talkList as $talk) {
            $url = $this->generateUrl(
                'talks_show',
                ['id' => $talk->getId(), 'slug' => $talk->getSlug()]
            );

            $talks[] = [
                'name' => $talk->getTitle(),
                'url' => $url
            ];
        }

        return $talks;
    }
}
