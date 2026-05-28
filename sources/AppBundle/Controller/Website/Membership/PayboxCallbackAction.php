<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership;

use AppBundle\Controller\Admin\Membership\MembershipFeePaymentStatus;
use AppBundle\MembershipFee\MembershipFeeService;
use AppBundle\MembershipFee\MembershipFeeMailer;
use AppBundle\MembershipFee\OnlinePaymentHandler;
use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Association\MemberType;
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
        private MembershipFeeService $membershipFeeService,
        private OnlinePaymentHandler $onlinePaymentHandler,
        private MembershipFeeMailer $membershipFeeMailer,
        private Audit $audit,
    ) {}

    public function __invoke(Request $request): Response
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        $status = $payboxResponse->getStatus();

        $etat = MembershipFeePaymentStatus::Error;

        if ($status === '00000') {
            $etat = MembershipFeePaymentStatus::Paid;
        } elseif ($status === '00015') {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($status === '00117') {
            $etat = MembershipFeePaymentStatus::Cancelled;
        } elseif (str_starts_with($status, '001')) {
            $etat = MembershipFeePaymentStatus::Rejected;
        }

        if ($etat == MembershipFeePaymentStatus::Paid) {
            $account = $this->onlinePaymentHandler->getAccountFromCmd($payboxResponse->getCmd());
            $lastCotisation = $this->membershipFeeService->getLatestByUserTypeAndId(MemberType::from($account['type']), $account['id']);

            if (!$lastCotisation instanceof MembershipFee && $account['type'] == MemberType::MemberPhysical->value) {
                $user = $this->userRepository->get($account['id']);
                $this->eventDispatcher->dispatch(new NewMemberEvent($user));
            }

            $this->onlinePaymentHandler->validerReglementEnLigne($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $this->membershipFeeMailer->notifierReglementEnLigneAuTresorier($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $this->audit->log("Ajout de la cotisation " . $payboxResponse->getCmd() . " via Paybox.");
        }
        return new Response();
    }
}
