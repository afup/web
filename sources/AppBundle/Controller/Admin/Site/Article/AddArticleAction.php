<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Site\Article;

use AppBundle\AuditLog\Audit;
use AppBundle\Site\ArticleImageStorage;
use AppBundle\Site\Entity\Article;
use AppBundle\Site\Entity\Repository\ArticleRepository;
use AppBundle\Site\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class AddArticleAction extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly Audit $audit,
        private readonly ArticleImageStorage $articleImageStorage,
    ) {}

    public function __invoke(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($article->titre && $article->raccourci === null) {
                $article->raccourci = (new AsciiSlugger())->slug($article->titre)->lower()->toString();
            }

            $uploadedImage = $form->get('image')->getData();
            if ($uploadedImage instanceof UploadedFile) {
                $article->image = $this->articleImageStorage->store($uploadedImage, $article);
            }

            $this->articleRepository->save($article);
            $this->audit->log('Ajout de l\'article ' . $article->titre);
            $this->addFlash('notice', 'L\'article ' . $article->titre . ' a été ajouté');
            return $this->redirectToRoute('admin_site_articles_list');
        }

        return $this->render('admin/site/article_form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter un article',
            'submitLabel' => 'Ajouter',
            'article' => $article,
            'imageUrl' => null,
        ]);
    }
}
