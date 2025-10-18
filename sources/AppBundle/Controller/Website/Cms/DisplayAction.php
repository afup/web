<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Cms;

use Afup\Site\Corporate\Article;
use Afup\Site\Corporate\Rubrique;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class DisplayAction extends AbstractController
{
    public function __construct(private readonly ViewRenderer $view) {}

    public function __invoke(string $code): Response
    {
        $articleRepository = new Article(null, $GLOBALS['AFUP_DB']);
        $articleRepository->chargerDepuisRaccourci($code);
        $article = $articleRepository->exportable();

        if (null === $article['id']) {
            throw $this->createNotFoundException();
        }

        if (false === $this->isGranted('ROLE_ADMIN') && $article['etat'] !== '1') {
            throw $this->createAccessDeniedException();
        }

        $rubriqueRepository = new Rubrique($article['id_site_rubrique'], $GLOBALS['AFUP_DB']);
        $rubriqueRepository->charger();
        $rubrique = $rubriqueRepository->exportable();

        if (!$this->isRubriqueAllowed($rubrique)) {
            throw $this->createNotFoundException();
        }

        return $this->view->render('site/cms_page/display.html.twig', [
            'article' => $article,
            'rubrique' => $rubrique,
        ]);
    }

    protected function isRubriqueAllowed(array $rubrique): bool
    {
        return in_array($rubrique['id'], [Rubrique::ID_RUBRIQUE_ASSOCIATION, Rubrique::ID_RUBRIQUE_ANTENNES, Rubrique::ID_RUBRIQUE_INFORMATIONS_PRATIQUES, Rubrique::ID_RUBRIQUE_NOS_ACTIONS]);
    }
}
