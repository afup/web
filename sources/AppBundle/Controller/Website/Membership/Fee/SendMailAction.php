<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use Afup\Site\Association\Cotisations;
use Afup\Site\Droits;
use Afup\Site\Utils\Logs;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\LegacyModelFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class SendMailAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LegacyModelFactory $legacyModelFactory,
        private readonly Mailer $mailer,
        private readonly Cotisations $cotisations,
        private readonly Droits $droits,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $identifiant = $this->droits->obtenirIdentifiant();
        $id = $request->get('id');

        $logs = $this->legacyModelFactory->createObject(Logs::class);
        $userRepository = $this->userRepository;

        if (false === $this->cotisations->isCurrentUserAllowedToReadInvoice($id)) {
            $logs::log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        if ($this->cotisations->envoyerFacture($id, $this->mailer, $userRepository)) {
            $logs::log('Envoi par email de la facture pour la cotisation n°' . $id);
            $this->addFlash('success', 'La facture a été envoyée par mail');
        } else {
            $this->addFlash('error', "La facture n'a pas pu être envoyée par mail");
        }

        return $this->redirectToRoute('member_membership_fee');
    }
}
