<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\AuditLog\Audit;
use AppBundle\MembershipFee\Model\MembershipFee;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PayboxCallbackAction
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UserRepository $userRepository,
        private CompanyMemberRepository $companyMemberRepository,
        private Cotisations $cotisations,
        private Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);
        $this->cotisations->setCompanyMemberRepository($this->companyMemberRepository);

        $status = $payboxResponse->getStatus();
        $etat = AFUP_COTISATIONS_PAIEMENT_ERREUR;

        if ($status === '00000') {
            $etat = AFUP_COTISATIONS_PAIEMENT_REGLE;
        } elseif ($status === '00015') {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($status === '00117') {
            $etat = AFUP_COTISATIONS_PAIEMENT_ANNULE;
        } elseif (str_starts_with($status, '001')) {
            $etat = AFUP_COTISATIONS_PAIEMENT_REFUSE;
        }

        if ($etat == AFUP_COTISATIONS_PAIEMENT_REGLE) {
            $account = $this->cotisations->getAccountFromCmd($payboxResponse->getCmd());
            $lastCotisation = $this->cotisations->getLastestByUserTypeAndId(MemberType::from($account['type']), $account['id']);

            if (!$lastCotisation instanceof MembershipFee && $account['type'] == MemberType::MemberPhysical->value) {
                $user = $this->userRepository->get($account['id']);
                $this->eventDispatcher->dispatch(new NewMemberEvent($user));
            }

            $this->cotisations->validerReglementEnLigne($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $this->cotisations->notifierReglementEnLigneAuTresorier($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId(), $this->userRepository);
            $this->audit->log("Ajout de la cotisation " . $payboxResponse->getCmd() . " via Paybox.");
        }
        return new Response();
    }
}
