<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\TechLetter\Form\SendingType;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly SendingRepository $sendingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $techLetters = $this->sendingRepository->getAllOrderedByDateDesc();
        $form = $this->createForm(SendingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $techletter = $form->getData();
            $this->sendingRepository->save($techletter);

            return $this->redirectToRoute('admin_techletter_generate', [
                'techletterId' => $techletter->getId(),
            ]);
        }

        return $this->render('admin/techletter/index.html.twig', [
            'title' => "Veille de l'AFUP",
            'techletters' => $techLetters,
            'form' => $form->createView(),
        ]);
    }
}
