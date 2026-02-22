<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\SuperApero;

use AppBundle\AuditLog\Audit;
use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use AppBundle\SuperApero\Form\SuperAperoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditAction extends AbstractController
{
    public function __construct(
        private readonly SuperAperoRepository $superAperoRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $superApero = $this->superAperoRepository->find($id);

        if ($superApero === null) {
            throw $this->createNotFoundException('Super apéro non trouvé');
        }

        $form = $this->createForm(SuperAperoType::class, $superApero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->superAperoRepository->save($superApero);
            $this->audit->log('Modification du Super Apéro ' . $superApero->annee());
            $this->addFlash('notice', 'Le Super Apéro ' . $superApero->annee() . ' a été modifié');

            return $this->redirectToRoute('admin_super_apero_list');
        }

        return $this->render('admin/super_apero/form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Modifier le Super Apéro ' . $superApero->annee(),
            'submitLabel' => 'Modifier',
        ]);
    }
}
