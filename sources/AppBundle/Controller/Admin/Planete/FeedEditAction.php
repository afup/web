<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Planete\FeedFormData;
use AppBundle\Planete\FeedFormType;
use PlanetePHP\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FeedEditAction extends AbstractController
{
    use DbLoggerTrait;

    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $feed = $this->feedRepository->get($id);
        $data = new FeedFormData();
        $data->name = $feed->getName();
        $data->feed = $feed->getFeed();
        $data->url = $feed->getUrl();
        $data->userId = $feed->getUserId();
        $data->status = $feed->getStatus();
        $form = $this->createForm(FeedFormType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $this->feedRepository->update(
                $id,
                $data->name,
                $data->url,
                $data->feed,
                $data->status,
                $data->userId
            );

            if ($ok) {
                $this->log(sprintf("Modification du flux %s (%d)", $data->name, $id));
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
