<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Cms;

use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DisplayAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly ArticleRepository $articleRepository,
        private readonly RubriqueRepository $rubriqueRepository,
    ) {}

    public function __invoke(string $code): Response
    {
        $article = $this->articleRepository->findBySlug($code);

        if (!$article instanceof Article) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted('ROLE_ADMIN') && $article->getState() !== 1) {
            throw $this->createAccessDeniedException();
        }

        $rubrique = $this->rubriqueRepository->get($article->getRubricId());

        if (!$this->isRubriqueAllowed($rubrique)) {
            throw $this->createNotFoundException();
        }

        return $this->view->render('site/cms_page/display.html.twig', [
            'article' => $article,
            'rubrique' => $rubrique,
        ]);
    }

    protected function isRubriqueAllowed(Rubrique $rubrique): bool
    {
        return in_array($rubrique->getId(), [Rubrique::ID_RUBRIQUE_ASSOCIATION, Rubrique::ID_RUBRIQUE_ANTENNES, Rubrique::ID_RUBRIQUE_INFORMATIONS_PRATIQUES, Rubrique::ID_RUBRIQUE_NOS_ACTIONS]);
    }
}
