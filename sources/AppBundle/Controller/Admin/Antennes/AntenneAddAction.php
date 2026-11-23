<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Antennes;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Antennes\Form\AntenneFormData;
use AppBundle\Antennes\Form\AntenneType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANTENNES_WRITER')]
final class AntenneAddAction extends AbstractController
{
    public function __construct(
        private readonly AntenneRepository $antenneRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $data = new AntenneFormData();
        $form = $this->createForm(AntenneType::class, $data, ['creation' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $antenne = $data->toEntity();
            $this->antenneRepository->save($antenne);
            $this->audit->log("Ajout de l'antenne " . $antenne->code);
            $this->addFlash('notice', "L'antenne " . $antenne->libelle . ' a été ajoutée');

            return $this->redirectToRoute('admin_antennes_list');
        }

        return $this->render('admin/antennes/form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter une antenne',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
