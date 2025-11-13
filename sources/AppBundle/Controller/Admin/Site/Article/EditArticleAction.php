<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\ArticleType;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditArticleAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $article = $this->articleRepository->get($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->save($article);
            $this->log('Modification de l\'article ' . $article->getTitle());
            $this->addFlash('notice', 'L\'article ' . $article->getTitle() . ' a été modifié');
            return $this->redirectToRoute('admin_site_articles_list');
        }

        return $this->render('admin/site/article_form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'formTitle' => 'Modifier un article',
            'submitLabel' => 'Modifier',
        ]);
    }
}
