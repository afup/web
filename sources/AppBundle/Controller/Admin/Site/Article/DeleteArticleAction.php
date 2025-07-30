<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteArticleAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private ArticleRepository $articleRepository,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function __invoke(int $id, string $token): RedirectResponse
    {
        if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('article_delete', $token))) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('admin_site_articles_list');
        }
        $article = $this->articleRepository->get($id);
        $name = $article->getTitle();
        $this->articleRepository->delete($article);
        $this->log('Suppression de l\'article ' . $name);
        $this->addFlash('notice', 'L\'article ' . $name . ' a été supprimé');
        return $this->redirectToRoute('admin_site_articles_list');
    }
}
