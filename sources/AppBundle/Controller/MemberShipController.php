<?php


namespace AppBundle\Controller;

use Afup\Site\Association\Assemblee_Generale;
use Afup\Site\Association\Cotisations;
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Utils;
use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Form\ContactDetailsType;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\LegacyModelFactory;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class MemberShipController extends SiteBaseController
{
    use DbLoggerTrait;

    public function becomeMemberAction()
    {
        return $this->render(
            ':site:become_member.html.twig',
            [
                'membership_fee_natural_person' => AFUP_COTISATION_PERSONNE_PHYSIQUE,
                'membership_fee_legal_entity' => AFUP_COTISATION_PERSONNE_MORALE
            ]
        );
    }

    public function companyAction(Request $request)
    {
        $subscribeForm = $this->createForm(CompanyMemberType::class);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /**
             * @var $member CompanyMember
             */
            $member = $subscribeForm->getData();
            $this->get('ting')->get(CompanyMemberRepository::class)->save($member);
            /**
             * @var $invitationRepository CompanyMemberInvitationRepository
             */
            $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

            foreach ($member->getInvitations() as $index => $invitation) {
                if ($invitation->getEmail() === null) {
                    continue;
                }
                $invitation
                    ->setSubmittedOn(new \DateTime())
                    ->setCompanyId($member->getId())
                    ->setToken(base64_encode(random_bytes(30)))
                    ->setStatus(CompanyMemberInvitation::STATUS_PENDING)
                ;
                if ($index === 0) {
                    // By security, force first employee to be defined as a manager
                    $invitation->setManager(true);
                }

                $invitationRepository->save($invitation);

                // Send mail to the other guy, begging for him to join the company
                $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($member, $invitation) {
                    $this->get(\AppBundle\Association\CompanyMembership\InvitationMail::class)->sendInvitation($member, $invitation);
                });
            }

            $subscriptionManager = $this->get(\AppBundle\Association\CompanyMembership\SubscriptionManagement::class);
            $invoice = $subscriptionManager->createInvoiceForInscription($member, count($member->getInvitations()));

            return $this->redirectToRoute('company_membership_payment', ['invoiceNumber' => $invoice['invoice'], 'token' => $invoice['token']]);
        }

        return $this->render(':site/company_membership:adhesion_entreprise.html.twig', ['form' => $subscribeForm->createView()]);
    }

    public function paymentAction($invoiceNumber, $token)
    {
        /**
         * @var $subscription Cotisations
         */
        $subscription = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Cotisations::class);
        $invoice = $subscription->getByInvoice($invoiceNumber, $token);
        /**
         * @var $company CompanyMember
         */
        $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($invoice['id_personne']);

        if (!$invoice || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        $paybox = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForSubscription(
            'F' . $invoiceNumber,
            (float) $invoice['montant'],
            $company->getEmail()
        );

        return $this->render(':site/company_membership:payment.html.twig', [
            'paybox' => $paybox,
            'invoice' => $invoice,
            'rib' => $this->legacyConfiguration->obtenir('rib'),
            'afup' => $this->legacyConfiguration->obtenir('afup')
        ]);
    }

    public function invoiceAction($invoiceNumber, $token)
    {
        /**
         * @var $subscription Cotisations
         */
        $subscription = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Cotisations::class);
        $invoice = $subscription->getByInvoice($invoiceNumber, $token);

        if (!$invoice) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        ob_start();
        $subscription->genererFacture($invoice['id']);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    public function memberInvitationAction(Request $request, $invitationId, $token)
    {
        /**
         * @var $invitationRepository CompanyMemberInvitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        /**
         * @var $invitation CompanyMemberInvitation
         */
        $invitation = $invitationRepository->getOneBy(['id' => $invitationId, 'token' => $token, 'status' => CompanyMemberInvitation::STATUS_PENDING]);
        $company = null;
        if ($invitation) {
            /**
             * @var $company CompanyMember
             */
            $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($invitation->getCompanyId());
        }

        if ($invitation === null || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find invitation with token "%s"', $token));
        }

        $userForm = $this->createForm(UserType::class);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /**
             * @var $user User
             */
            $user = $userForm->getData();
            $user
                ->setStatus(User::STATUS_ACTIVE)
                ->setCompanyId($company->getId())
                ->setPassword(md5($user->getPassword())) /** @TODO We should change that */
            ;

            if ($invitation->getManager()) {
                $user->setRoles(['ROLE_COMPANY_MANAGER', 'ROLE_USER']);
            }

            $invitation->setStatus(CompanyMemberInvitation::STATUS_ACCEPTED);

            $this->get('ting')->get(UserRepository::class)->save($user);
            $invitationRepository->save($invitation);
            $this->addFlash('success', 'Votre compte a été créé !');

            $event = new NewMemberEvent($user);
            $this->get('event_dispatcher')->dispatch($event::NAME, $event);

            return $this->redirect('/pages/administration/');
        }

        return $this->render(':site/company_membership:member_invitation.html.twig', ['company' => $company, 'form' => $userForm->createView()]);
    }

    public function slackInviteRequestAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canRequestSlackInvite()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorité à demander une invitation");
        }

        $this->get('slack_members_legacy_client')->invite($this->getUser()->getEmail());

        $this->addFlash('success', 'Un email vous a été envoyé pour rejoindre le Slack des membres !');

        $user->setSlackInviteStatus(User::SLACK_INVITE_STATUS_REQUESTED);
        $this->get('ting')->get(UserRepository::class)->save($user);

        $this->log('Demande invitation slack', $this->getUser());

        return $this->redirect('/pages/administration');
    }

    public function payboxCallbackAction(Request $request)
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);
        /**
         * @var $cotisations Cotisations
         */
        $cotisations = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Cotisations::class);
        $logs = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Logs::class);

        $status = $payboxResponse->getStatus();
        $etat = AFUP_COTISATIONS_PAIEMENT_ERREUR;

        if ($status === '00000') {
            $etat = AFUP_COTISATIONS_PAIEMENT_REGLE;
        } elseif ($status === '00015') {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($status === '00117') {
            $etat = AFUP_COTISATIONS_PAIEMENT_ANNULE;
        } elseif (substr($status, 0, 3) === '001') {
            $etat = AFUP_COTISATIONS_PAIEMENT_REFUSE;
        }

        if ($etat == AFUP_COTISATIONS_PAIEMENT_REGLE) {
            $account = $cotisations->getAccountFromCmd($payboxResponse->getCmd());
            $lastCotisation = $cotisations->obtenirDerniere($account['type'], $account['id']);

            if ($lastCotisation === false && $account['type'] == UserRepository::USER_TYPE_PHYSICAL) {
                $user = $this->get(\AppBundle\Association\Model\Repository\UserRepository::class)->get($account['id']);
                $event = new NewMemberEvent($user);
                $this->get('event_dispatcher')->dispatch($event::NAME, $event);
            }

            $cotisations->validerReglementEnLigne($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $cotisations->notifierRegelementEnLigneAuTresorier($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $logs::log("Ajout de la cotisation " . $payboxResponse->getCmd() . " via Paybox.");
        }
        return new Response();
    }

    public function contactDetailsAction(Request $request)
    {
        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);
        $repo = $this->get('ting')->get(UserRepository::class);

        $user = $repo->get($this->getUserId());
        $data = [
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'username' => $user->getUsername(),
            'zipcode' => $user->getZipCode(),
            'city' => $user->getCity(),
            'phone' => $user->getPhone(),
            'mobilephone' => $user->getMobilePhone(),
            'country' => $user->getCountry(),
            'nearest_office' => $user->getNearestOffice(),
        ];

        $userForm = $this->createForm(ContactDetailsType::class, $data);
        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $data = $userForm->getData();

            $user->setEmail($data['email']);
            $user->setAddress($data['address']);
            $user->setZipCode($data['zipcode']);
            $user->setCity($data['city']);
            $user->setUsername($data['username']);
            $user->setPhone($data['phone']);
            $user->setMobilePhone($data['mobilephone']);
            $user->setCountry($data['country']);
            $user->setNearestOffice($data['nearest_office']);
            // Save password if not empty
            if (! empty($data['password'])) {
                $user->setPassword(md5($data['password'])); /** @TODO We should change that */
            }

            $repo->save($user);

            $logs::log("Modification des coordonnées de l'utilisateur " . $user->getUsername() . " effectuée avec succès.");
            $this->addFlash('success', 'Votre compte a été modifié !');
        }

        return $this->render(':admin/association/membership:member_contact_details.html.twig', ['title' => 'Mes coordonnées', 'form' => $userForm->createView()]);
    }

    private function getUserId()
    {
        return $this->getDroits()->obtenirIdentifiant();
    }

    private function getDroits()
    {
        global $bdd;
        return Utils::fabriqueDroits($bdd, $this->get('security.token_storage'), $this->get('security.authorization_checker'));
    }

    public function membershipFeeAction(Request $request)
    {
        global $bdd;
        $personnes_physiques = new Personnes_Physiques($bdd);
        $cotisations = $this->getCotisations();

        $identifiant = $this->getDroits()->obtenirIdentifiant();

        $donnees = $personnes_physiques->obtenir($identifiant);

        $cotisation = $personnes_physiques->obtenirDerniereCotisation($identifiant);

        if (!$cotisation) {
            $message = empty($_GET['hash'])? 'Est-ce vraiment votre première cotisation ?' : '';
        } else {
            $endSubscription = $cotisations->finProchaineCotisation($cotisation);
            $message = sprintf(
                'Votre dernière cotisation -- %s %s -- est valable jusqu\'au %s. <br />
        Si vous renouvellez votre cotisation maintenant, celle-ci sera valable jusqu\'au %s.',
                $cotisation['montant'],
                EURO,
                date("d/m/Y", $cotisation['date_fin']),
                $endSubscription->format('d/m/Y')
            );
        }

        $cotisation_physique = $cotisations->obtenirListe(0 , $donnees['id']);
        $cotisation_morale = $cotisations->obtenirListe(1 , $donnees['id_personne_morale']);

        if (is_array($cotisation_morale) && is_array($cotisation_physique)) {
            $cotisations = array_merge($cotisation_physique, $cotisation_morale);
        } elseif (is_array($cotisation_morale)) {
            $cotisations = $cotisation_morale;
        } elseif (is_array($cotisation_physique)) {
            $cotisations = $cotisation_physique;
        } else {
            $cotisations = array();
        }

        if ($donnees['id_personne_morale'] > 0) {
            $id_personne = $donnees['id_personne_morale'];
            $personne_morale = new \Afup\Site\Association\Personnes_Morales($bdd);
            $type_personne = AFUP_PERSONNES_MORALES;
            $prefixe = 'Personne morale';
            $montant = $personne_morale->getMembershipFee($id_personne);
        } else {
            $id_personne = $identifiant;
            $type_personne = AFUP_PERSONNES_PHYSIQUES;
            $prefixe = 'Personne physique';
            $montant = AFUP_COTISATION_PERSONNE_PHYSIQUE;
        }

        $formattedMontant = number_format($montant, 2, ',', ' ');
        $libelle = sprintf("%s : <strong>%s€</strong>", $prefixe, $formattedMontant);

        $reference = (new \AppBundle\Association\MembershipFeeReferenceGenerator())->generate(new \DateTimeImmutable('now'), $type_personne, $id_personne, $donnees['nom']);

        $paybox = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForSubscription(
            $reference,
            (float) $montant,
            $donnees['email']
        );

        $paybox = str_replace('INPUT TYPE=SUBMIT', 'INPUT TYPE=SUBMIT class="button button--call-to-action"', $paybox);

        return $this->render(
            ':admin/association/membership:membershipfee.html.twig',
            [
                'title' => 'Ma cotisation',
                'cotisations' => $cotisations,
                'time' => time(),
                'montant' => $montant,
                'libelle' => $libelle,
                'paybox' => $paybox,
                'message' => $message,
            ]
        );
    }

    private function getCotisations()
    {
        global $bdd;
        return new Cotisations($bdd, $this->getDroits());
    }

    public function membershipFeeDownloadAction(Request $request)
    {
        $cotisations = $this->getCotisations();
        $identifiant = $this->getDroits()->obtenirIdentifiant();
        $id = $request->get('id');

        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);

        if (false === $cotisations->isCurrentUserAllowedToReadInvoice($id)) {
            $logs::log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        $tempfile = tempnam(sys_get_temp_dir(), 'membership_fee_download');
        $numeroFacture = $cotisations->genererFacture($id, $tempfile);

        $response = new BinaryFileResponse($tempfile, 200, [], false);
        $response->deleteFileAfterSend(true);
        $response->setContentDisposition('attachment', 'facture-' . $numeroFacture . '.pdf');

        return $response;
    }

    public function membershipFeeSendMailAction(Request $request)
    {
        $cotisations = $this->getCotisations();
        $identifiant = $this->getDroits()->obtenirIdentifiant();
        $id = $request->get('id');

        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);

        if (false === $cotisations->isCurrentUserAllowedToReadInvoice($id)) {
            $logs::log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        if ($cotisations->envoyerFacture($id, $this->get(\Afup\Site\Utils\Mail::class))) {
            $logs::log('Envoi par email de la facture pour la cotisation n°' . $id);
            $this->addFlash('success', 'La facture a été envoyée par mail');
        } else {
            $this->addFlash('error', "La facture n'a pas pu être envoyée par mail");
        }

        return $this->redirectToRoute('member_membership_fee');
    }

    public function generalMeetingAction(Request $request)
    {
        $login = $this->getUser()->getUsername();

        $title = 'Présence prochaine AG';

        $assemblee_generale = $this->get(LegacyModelFactory::class)->createObject(Assemblee_Generale::class);
        $timestamp = $assemblee_generale->obternirDerniereDate();
        $date_assemblee_generale = convertirTimestampEnDate($timestamp);
        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);
        $personnes_physiques = $this->get(LegacyModelFactory::class)->createObject(Personnes_Physiques::class);


        $generalMeetingPlanned = $timestamp > strtotime("-1 day", time());

        if (false === $generalMeetingPlanned) {
            return $this->render(
                ':admin/association/membership:generalmeeting_no_meeting.html.twig',
                [
                    'title' => $title,
                ]
            );
        }

        $cotisation = $personnes_physiques->obtenirDerniereCotisation($this->getUser()->getId());
        $needsMembersheepFeePayment = $timestamp > strtotime("+14 day", $cotisation['date_fin']);

        if ($needsMembersheepFeePayment) {
            return $this->render(
                ':admin/association/membership:generalmeeting_membersheepfee.html.twig',
                [
                    'title' => $title,
                    'date_general_meeting' => $date_assemblee_generale,
                ]
            );
        }

        $presents = $assemblee_generale->obtenirPresents($timestamp, ['exclure_login' => $login]);

        list($presence, $id_personne_avec_pouvoir) = $assemblee_generale->obtenirInfos($login, $timestamp);

        $form = $this->createFormBuilder()
            ->add('presence', ChoiceType::class, ['expanded' => true, 'choices' => ['Oui' => 1, 'Non' => 2, 'Je ne sais pas encore' => 0]])
            ->add(
                'id_personne_avec_pouvoir',
                ChoiceType::class,
                [
                    'choices' => array_flip(array_merge([0 => ''], $presents)),
                    'label' => 'Je donne mon pouvoir à',
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Confirmer'])
            ->setData([
                'presence' => $presence,
                'id_personne_avec_pouvoir' => $id_personne_avec_pouvoir,
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $ok = $assemblee_generale->modifier($login,
                $timestamp,
                $data['presence'],
                $data['id_personne_avec_pouvoir']
            );

            if ($ok) {
                $logs::log('Modification de la présence et du pouvoir de la personne physique');
                $this->addFlash('success', 'La présence et le pouvoir ont été modifiés', 'index.php?page=membre_assemblee_generale');

                return $this->redirectToRoute('member_general_meeting');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de la présence et du pouvoir');
            }
        }

        return $this->render(
            ':admin/association/membership:generalmeeting.html.twig',
            [
                'title' => $title,
                'date_general_meeting' => $date_assemblee_generale,
                'form' => $form->createView(),
            ]
        );
    }
}
