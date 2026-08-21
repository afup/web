<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\ArticleImageStorage;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Site\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditArticleAction extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly Audit $audit,
        private readonly ArticleImageStorage $articleImageStorage,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $article = $this->articleRepository->find($id);
        if ($article === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedImage = $form->get('image')->getData();
            if ($uploadedImage instanceof UploadedFile) {
                $article->image = $this->articleImageStorage->store($uploadedImage, $article);
            } elseif ($article->image && $form->get('supprimerImage')->getData() === true) {
                $this->articleImageStorage->remove($article);
                $article->image = null;
            }

            $this->articleRepository->save($article);
            $this->audit->log('Modification de l\'article ' . $article->titre);
            $this->addFlash('notice', 'L\'article ' . $article->titre . ' a été modifié');
            return $this->redirectToRoute('admin_site_articles_list');
        }

        return $this->render('admin/site/article_form.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'formTitle' => 'Modifier un article',
            'submitLabel' => 'Modifier',
            'imageUrl' => $this->articleImageStorage->getUrl($article),
        ]);
    }
}
