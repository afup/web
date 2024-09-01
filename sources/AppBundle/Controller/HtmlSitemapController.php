<?php

namespace AppBundle\Controller;

use Afup\Site\Corporate\Branche;
use Afup\Site\Corporate\Feuille;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HtmlSitemapController extends SiteBaseController
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function displayAction()
    {
        $branche = new Branche();

        return $this->render(
            ':site:sitemap.html.twig',
            [
                'pages' => $this->buildLeafs($branche, Feuille::ID_FEUILLE_HEADER),
                'association' => $this->buildLeafs($branche, Feuille::ID_FEUILLE_ANTENNES),
                'members' => $this->members(),
                'news' => $this->news(),
                'talks' => $this->talks(),
            ]
        );
    }

    private function buildLeafs(Branche $branche, int $id): array
    {
        $leafs = $branche->feuillesEnfants($id);

        $pages = [];
        foreach ($leafs as $leaf) {
            if (!$leaf['lien'] || starts_with($leaf['lien'], 'http')) {
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
        $companyRepository = $this->get('ting')->get(CompanyMemberRepository::class);
        $displayableCompanies = $companyRepository->findDisplayableCompanies();

        $members = [];
        foreach ($displayableCompanies as $member) {
            $url = $this->urlGenerator->generate('company_public_profile', [
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
        $itemPerPage = 100;
        $page = 1;

        $repository = $this->get('ting')->get(ArticleRepository::class);

        $news = [];
        do {
            $newsList = $repository->findPublishedNews($page++, $itemPerPage, []);
            foreach ($newsList as $newsItem) {
                $url = $this->urlGenerator->generate('news_display', [
                    'code' => $newsItem->getSlug(),
                ]);

                $news[] = [
                    'name' => $newsItem->getTitle(),
                    'url' => $url
                ];
            }
        } while (count($newsList) >= $itemPerPage);

        return $news;
    }

    private function talks(): array
    {
        $repository = $this->get('ting')->get(TalkRepository::class);

        $talks = [];
        $talkList = $repository->getAllPastTalks((new \DateTime())->setTime(29,59,59));

        /** @var Talk $talk */
        foreach ($talkList as $talk) {
            $url = $this->urlGenerator->generate(
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
