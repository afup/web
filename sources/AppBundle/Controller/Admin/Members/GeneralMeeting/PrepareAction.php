<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use AppBundle\AssembleeGenerale\Form\PrepareFormType;
use AppBundle\AuditLog\Audit;
use AppBundle\AssembleeGenerale\Entity\Repository\AssembleeGeneraleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrepareAction extends AbstractController
{
    public function __construct(
        private readonly AssembleeGeneraleRepository $assembleGeneraleRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(PrepareFormType::class, ['date' => new DateTime()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->assembleGeneraleRepository->prepare($data['date'], $data['description'])) {
                $this->audit->log('Ajout de la préparation des personnes physiques à l\'assemblée générale');
                $this->addFlash('notice', 'La préparation des personnes physiques a été ajoutée');

                return new RedirectResponse($request->getRequestUri());
            }
            $this->addFlash('error', 'Une erreur est survenue lors de la préparation des personnes physiques');
        }

        return $this->render('admin/members/general_meeting/prepare.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
