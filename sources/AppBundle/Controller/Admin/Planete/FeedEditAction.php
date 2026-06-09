<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use AppBundle\AuditLog\Audit;
use AppBundle\Planete\FeedFormType;
use Exception;
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
        $feed = $this->feedRepository->find($id);
        if ($feed === null) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(FeedFormType::class, $feed);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->feedRepository->save($feed);

                $this->audit->log(sprintf("Modification du flux %s (%d)", $feed->name, $id));
                $this->addFlash('notice', 'Le flux a été modifié');

                return $this->redirectToRoute('admin_planete_feed_list');
            } catch (Exception $e) {
                $this->addFlash('error', "Une erreur est survenue lors de la modification du flux :\n" . $e->getMessage());
            }
        }

        return $this->render('admin/planete/feed_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
