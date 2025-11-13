<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Site\Form\ArticleType;
use AppBundle\Site\Model\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddArticleAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->save($article);
            $this->log('Ajout de l\'article ' . $article->getTitle());
            $this->addFlash('notice', 'L\'article ' . $article->getTitle() . ' a été ajouté');
            return $this->redirectToRoute('admin_site_articles_list');
        }

        return $this->render('admin/site/article_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter un article',
            'submitLabel' => 'Ajouter',
            'article' => $article,
        ]);
    }
}
