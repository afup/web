<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Antennes;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Antennes\Form\AntenneFormData;
use AppBundle\Antennes\Form\AntenneType;
use AppBundle\AuditLog\Audit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANTENNES_WRITER')]
final class AntenneEditAction extends AbstractController
{
    public function __construct(
        private readonly AntenneRepository $antenneRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(string $code, Request $request): Response
    {
        $antenne = $this->antenneRepository->find($code);

        if (!$antenne instanceof Antenne) {
            throw $this->createNotFoundException('Antenne non trouvée');
        }

        $data = AntenneFormData::fromEntity($antenne);
        $form = $this->createForm(AntenneType::class, $data, ['creation' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data->appliquerA($antenne);
            $this->antenneRepository->save($antenne);
            $this->audit->log("Modification de l'antenne " . $antenne->code);
            $this->addFlash('notice', "L'antenne " . $antenne->libelle . ' a été modifiée');

            return $this->redirectToRoute('admin_antennes_list');
        }

        return $this->render('admin/antennes/form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => "Modifier l'antenne " . $antenne->libelle,
            'submitLabel' => 'Modifier',
            'antenne' => $antenne,
        ]);
    }
}
