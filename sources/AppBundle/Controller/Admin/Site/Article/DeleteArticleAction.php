<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class DeleteArticleAction extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly Audit $audit,
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
        $this->audit->log('Suppression de l\'article ' . $name);
        $this->addFlash('notice', 'L\'article ' . $name . ' a été supprimé');
        return $this->redirectToRoute('admin_site_articles_list');
    }
}
