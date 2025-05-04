<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Planete;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Planete\FeedFormData;
use AppBundle\Planete\FeedFormType;
use PlanetePHP\FeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FeedAddAction extends AbstractController
{
    use DbLoggerTrait;

    public function __construct(private FeedRepository $feedRepository)
    {
    }

    public function __invoke(Request $request)
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
                $data->userId
            );

            if ($ok) {
                $this->log('Ajout du flux ' . $data->name);
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
