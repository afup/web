<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\Fee;

use AppBundle\MembershipFee\MembershipFeeMailer;
use Afup\Site\Droits;
use AppBundle\AuditLog\Audit;
use AppBundle\Security\MembershipFeeVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class SendMailAction extends AbstractController
{
    public function __construct(
        private readonly MembershipFeeMailer $membershipFeeMailer,
        private readonly Droits $droits,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $identifiant = $this->droits->obtenirIdentifiant();
        $id = $request->query->getInt('id');

        if (false === $this->isGranted(MembershipFeeVoter::READ_INVOICE, $id)) {
            $this->audit->log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        if ($this->membershipFeeMailer->envoyerFacture($id)) {
            $this->audit->log('Envoi par email de la facture pour la cotisation n°' . $id);
            $this->addFlash('success', 'La facture a été envoyée par mail');
        } else {
            $this->addFlash('error', "La facture n'a pas pu être envoyée par mail");
        }

        return $this->redirectToRoute('member_membership_fee');
    }
}
