<?php

namespace AppBundle\Controller;

use Afup\Site\Corporate\Article;
use Afup\Site\Corporate\Rubrique;

class CmsPageController extends SiteBaseController
{
    public function displayAction($code)
    {
        $articleRepository = new Article(null, $GLOBALS['AFUP_DB']);
        $articleRepository->chargerDepuisRaccourci($code);
        $article = $articleRepository->exportable();

        if (null === $article['id']) {
            throw $this->createNotFoundException();
        }

        $rubriqueRepository = new Rubrique($article['id_site_rubrique'], $GLOBALS['AFUP_DB']);
        $rubriqueRepository->charger();
        $rubrique = $rubriqueRepository->exportable();

        if (!$this->isRubriqueAllowed($rubrique)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            ':site:cms_page/display.html.twig',
            [
                'article' => $article,
                'rubrique' => $rubrique,
            ]
        );
    }

    protected function isRubriqueAllowed($rubrique)
    {
        return $rubrique['id'] == Rubrique::ID_RUBRIQUE_ASSOCIATION || $rubrique['id'] == Rubrique::ID_RUBRIQUE_ANTENNES || $rubrique['id'] == Rubrique::ID_RUBRIQUE_INFORMATIONS_PRATIQUES;
    }
}
