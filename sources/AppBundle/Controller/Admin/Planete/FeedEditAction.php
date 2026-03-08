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

class FeedEditAction extends AbstractController
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = $request->query->getInt('id');
        $feed = $this->feedRepository->get($id);
        $data = new FeedFormData();
        $data->name = $feed->name;
        $data->feed = $feed->feed;
        $data->url = $feed->url;
        $data->userId = $feed->userId;
        $data->status = $feed->status;
        $form = $this->createForm(FeedFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $this->feedRepository->update(
                $id,
                $data->name,
                $data->url,
                $data->feed,
                $data->status,
                $data->userId,
            );

            if ($ok) {
                $this->audit->log(sprintf("Modification du flux %s (%d)", $data->name, $id));
                $this->addFlash('notice', 'Le flux a été modifié');

                return $this->redirectToRoute('admin_planete_feed_list');
            }
            $this->addFlash('error', 'Une erreur est survenue lors de la modification du flux');
        }

        return $this->render('admin/planete/feed_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
