<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\SuperApero;

use AppBundle\AuditLog\Audit;
use AppBundle\SuperApero\Entity\SuperApero;
use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use AppBundle\SuperApero\Form\SuperAperoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddAction extends AbstractController
{
    public function __construct(
        private readonly SuperAperoRepository $superAperoRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $superApero = new SuperApero();
        $form = $this->createForm(SuperAperoType::class, $superApero);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->superAperoRepository->save($superApero);
            $this->audit->log('Ajout du Super Apéro ' . $superApero->annee());
            $this->addFlash('notice', 'Le Super Apéro ' . $superApero->annee() . ' a été ajouté');

            return $this->redirectToRoute('admin_super_apero_list');
        }

        return $this->render('admin/super_apero/form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Ajouter un Super Apéro',
            'submitLabel' => 'Ajouter',
        ]);
    }
}
