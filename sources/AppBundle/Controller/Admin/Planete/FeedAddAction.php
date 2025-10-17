<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use AppBundle\AuditLog\Audit;
use AppBundle\Planete\FeedFormData;
use AppBundle\Planete\FeedFormType;
use PlanetePHP\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedAddAction extends AbstractController
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $data = new FeedFormData();
        $form = $this->createForm(FeedFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $this->feedRepository->insert(
                $data->name,
                $data->url,
                $data->feed,
                $data->status,
                $data->userId,
            );

            if ($ok) {
                $this->audit->log('Ajout du flux ' . $data->name);
                $this->addFlash('notice', 'Le flux a été ajouté');

                return $this->redirectToRoute('admin_planete_feed_list');
            }
            $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du flux');
        }

        return $this->render('admin/planete/feed_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
