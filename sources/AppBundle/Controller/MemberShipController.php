<?php

namespace AppBundle\Controller;

use Afup\Site\Association\Cotisations;
use Afup\Site\Logger\DbLoggerTrait;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\Event\NewMemberEvent;
use AppBundle\Association\Factory\UserFactory;
use AppBundle\Association\Form\CompanyMemberType;
use AppBundle\Association\Form\ContactDetailsType;
use AppBundle\Association\Form\RegisterUserType;
use AppBundle\Association\Form\UserType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\GeneralMeetingVote;
use AppBundle\Association\Model\Repository\CompanyMemberInvitationRepository;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Association\Model\Repository\TechletterUnsubscriptionsRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\LegacyModelFactory;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxResponseFactory;
use AppBundle\Security\LegacyAuthenticator;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use Assert\Assertion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    public function memberAction(Request $request): Response
    {
        $user = $this->get(UserFactory::class)->createForRegister();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Nécessaire pour md5 le mdp qui est fait dans le setPlainPassword => a terme, utiliser les cryptages de sf
            $user->setPlainPassword($user->getPassword());
            $this->get(UserRepository::class)->save($user);

            Logs::initialiser($GLOBALS['AFUP_DB'], $user->getId());
            Logs::log('Ajout de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName());

            $this->get(UserService::class)->sendWelcomeEmail($user);
            $this->addFlash('notice', 'Merci pour votre inscription. Il ne reste plus qu\'à régler votre cotisation.');

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get(LegacyAuthenticator::class),
                    'legacy_secured_area'
                );
        }

        return $this->render('admin/association/membership/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function companyAction(Request $request)
    {
        $data = new CompanyMember();
        $data->setInvitations([
            (new CompanyMemberInvitation())->setManager(true)
        ]);

        $subscribeForm = $this->createForm(CompanyMemberType::class, $data);
        $subscribeForm->handleRequest($request);

        if ($subscribeForm->isSubmitted() && $subscribeForm->isValid()) {
            /**
             * @var CompanyMember $member
             */
            $member = $subscribeForm->getData();
            $this->get('ting')->get(CompanyMemberRepository::class)->save($member);
            /**
             * @var CompanyMemberInvitationRepository $invitationRepository
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
         * @var Cotisations $subscription
         */
        $subscription = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Cotisations::class);
        $invoice = $subscription->getByInvoice($invoiceNumber, $token);
        /**
         * @var CompanyMember $company
         */
        $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($invoice['id_personne']);

        if (!$invoice || $company === null) {
            throw $this->createNotFoundException(sprintf('Could not find the invoice "%s" with token "%s"', $invoiceNumber, $token));
        }

        $payboxBilling = new PayboxBilling($company->getFirstName(), $company->getLastName(), $company->getAddress(), $company->getZipCode(), $company->getCity(), $company->getCountry());

        $paybox = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForSubscription(
            'F' . $invoiceNumber,
            (float) $invoice['montant'],
            $company->getEmail(),
            $payboxBilling
        );

        $bankAccountFactory = new BankAccountFactory();

        return $this->render(':site/company_membership:payment.html.twig', [
            'paybox' => $paybox,
            'invoice' => $invoice,
            'bankAccount' => $bankAccountFactory->createApplyableAt(\DateTimeImmutable::createFromFormat('U', $invoice['date_debut'])),
            'afup' => [
                'raison_sociale' => AFUP_RAISON_SOCIALE,
                'adresse' => AFUP_ADRESSE,
                'code_postal' => AFUP_CODE_POSTAL,
                'ville' => AFUP_VILLE
            ]
        ]);
    }

    public function invoiceAction($invoiceNumber, $token)
    {
        /**
         * @var Cotisations $subscription
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
         * @var CompanyMemberInvitationRepository $invitationRepository
         */
        $invitationRepository = $this->get('ting')->get(CompanyMemberInvitationRepository::class);

        /**
         * @var CompanyMemberInvitation $invitation
         */
        $invitation = $invitationRepository->getOneBy(['id' => $invitationId, 'token' => $token, 'status' => CompanyMemberInvitation::STATUS_PENDING]);
        $company = null;
        if ($invitation) {
            /**
             * @var CompanyMember $company
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
             * @var User $user
             */
            $user = $userForm->getData();
            $user->setCivility('');
            $user->setPlainPassword($user->getPassword());
            $user
                ->setStatus(User::STATUS_ACTIVE)
                ->setCompanyId($company->getId())
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

            return $this->redirectToRoute('member_index');
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

        return $this->redirectToRoute('admin_home');
    }

    public function payboxCallbackAction(Request $request)
    {
        $payboxResponse = PayboxResponseFactory::createFromRequest($request);
        $userRepository = $this->get(UserRepository::class);
        /**
         * @var Cotisations $cotisations
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
                $user = $userRepository->get($account['id']);
                $event = new NewMemberEvent($user);
                $this->get('event_dispatcher')->dispatch($event::NAME, $event);
            }

            $cotisations->validerReglementEnLigne($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId());
            $cotisations->notifierRegelementEnLigneAuTresorier($payboxResponse->getCmd(), round($payboxResponse->getTotal() / 100, 2), $payboxResponse->getAuthorizationId(), $payboxResponse->getTransactionId(), $userRepository);
            $logs::log("Ajout de la cotisation " . $payboxResponse->getCmd() . " via Paybox.");
        }
        return new Response();
    }



    public function contactDetailsAction(Request $request)
    {
        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);
        $repo = $this->get('ting')->get(UserRepository::class);

        /** @var User $user */
        $user = $repo->get($this->getUserId());

        $userForm = $this->createForm(ContactDetailsType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Save password if not empty
            $newPassword = $request->request->get($userForm->getName())['plainPassword']['first'];
            if ($newPassword) {
                $user->setPlainPassword($newPassword);
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
        return Utils::fabriqueDroits($this->get('security.token_storage'), $this->get('security.authorization_checker'));
    }

    public function membershipFeeAction()
    {
        $bdd = $GLOBALS['AFUP_DB'];
        $userRepository = $this->get(UserRepository::class);
        $userService = $this->get(UserService::class);
        $cotisations = $this->getCotisations();

        $identifiant = $this->getDroits()->obtenirIdentifiant();
        /** @var User $user */
        $user = $userRepository->get($identifiant);
        Assertion::notNull($user);
        $cotisation = $userService->getLastSubscription($user);
        $now = new \DateTime('now');
        $isSubjectedToVat = Vat::isSubjectedToVat($now);

        if (!$cotisation) {
            $message = '';
        } else {
            $endSubscription = $cotisations->finProchaineCotisation($cotisation);
            $message = sprintf(
                'Votre dernière cotisation -- %s %s -- est valable jusqu\'au %s. <br />
        Si vous renouvelez votre cotisation maintenant, celle-ci sera valable jusqu\'au %s.',
                number_format((float) $cotisation['montant'], 2, ',', ' '),
                EURO,
                date("d/m/Y", (int) $cotisation['date_fin']),
                $endSubscription->format('d/m/Y')
            );
        }

        $cotisations_physique = $cotisations->obtenirListe(0, $user->getId());
        $cotisations_morale = $cotisations->obtenirListe(1, $user->getCompanyId());

        if (is_array($cotisations_morale) && is_array($cotisations_physique)) {
            $liste_cotisations = array_merge($cotisations_physique, $cotisations_morale);
        } elseif (is_array($cotisations_morale)) {
            $liste_cotisations = $cotisations_morale;
        } elseif (is_array($cotisations_physique)) {
            $liste_cotisations = $cotisations_physique;
        } else {
            $liste_cotisations = [];
        }

        foreach ($liste_cotisations as $k => $cotisation) {
            $liste_cotisations[$k]['telecharger_facture'] = $cotisations->isCurrentUserAllowedToReadInvoice($cotisation['id']);
        }

        if ($user->getCompanyId() > 0) {
            $id_personne = $user->getCompanyId();
            $personne_morale = new \Afup\Site\Association\Personnes_Morales($bdd);
            $type_personne = AFUP_PERSONNES_MORALES;
            $prefixe = 'Personne morale';
            $montant = $personne_morale->getMembershipFee($id_personne);
            if ($isSubjectedToVat) {
                $montant = $montant * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE);
            }
        } else {
            $id_personne = $identifiant;
            $type_personne = AFUP_PERSONNES_PHYSIQUES;
            $prefixe = 'Personne physique';
            $montant = AFUP_COTISATION_PERSONNE_PHYSIQUE;
        }

        $formattedMontant = number_format($montant, 2, ',', ' ');
        $libelle = sprintf("%s : <strong>%s€</strong>", $prefixe, $formattedMontant);

        $reference = (new \AppBundle\Association\MembershipFeeReferenceGenerator())->generate(new \DateTimeImmutable('now'), $type_personne, $id_personne, $user->getLastName());

        $payboxBilling = new PayboxBilling($user->getFirstName(), $user->getLastName(), $user->getAddress(), $user->getZipCode(), $user->getCity(), $user->getCountry());

        $paybox = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForSubscription(
            $reference,
            (float) $montant,
            $user->getEmail(),
            $payboxBilling
        );

        $paybox = str_replace('INPUT TYPE=SUBMIT', 'INPUT TYPE=SUBMIT class="button button--call-to-action"', $paybox);

        return $this->render(
            ':admin/association/membership:membershipfee.html.twig',
            [
                'isSubjectedToVat' => $isSubjectedToVat,
                'title' => 'Ma cotisation',
                'cotisations' => $liste_cotisations,
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
        return new Cotisations($GLOBALS['AFUP_DB'], $this->getDroits());
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
        $cotisation = $cotisations->obtenir($id);

        if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
            /** @var CompanyMember $company */
            $company = $this->get('ting')->get(CompanyMemberRepository::class)->get($cotisation['id_personne']);
            Assertion::isInstanceOf($company, CompanyMember::class);
            $patternPrefix = $company->getCompanyName();
        } else {
            $user = $this->get('ting')->get(UserRepository::class)->get($cotisation['id_personne']);
            Assertion::isInstanceOf($user, User::class);
            $patternPrefix = $user->getLastName();
        }

        $pattern = str_replace(' ', '', $patternPrefix) . '_' . $numeroFacture . '_' . date('dmY', (int) $cotisation['date_debut']) . '.pdf';

        $response = new BinaryFileResponse($tempfile, 200, [], false);
        $response->deleteFileAfterSend(true);
        $response->setContentDisposition('attachment', $pattern);

        return $response;
    }

    public function membershipFeeSendMailAction(Request $request)
    {
        $cotisations = $this->getCotisations();
        $identifiant = $this->getDroits()->obtenirIdentifiant();
        $id = $request->get('id');

        $logs = $this->get(LegacyModelFactory::class)->createObject(Logs::class);
        $userRepository = $this->get(UserRepository::class);

        if (false === $cotisations->isCurrentUserAllowedToReadInvoice($id)) {
            $logs::log("L'utilisateur id: " . $identifiant . ' a tenté de voir la facture id:' . $id);
            throw $this->createAccessDeniedException('Cette facture ne vous appartient pas, vous ne pouvez la visualiser.');
        }

        if ($cotisations->envoyerFacture($id, $this->get(\AppBundle\Email\Mailer\Mailer::class), $userRepository)) {
            $logs::log('Envoi par email de la facture pour la cotisation n°' . $id);
            $this->addFlash('success', 'La facture a été envoyée par mail');
        } else {
            $this->addFlash('error', "La facture n'a pas pu être envoyée par mail");
        }

        return $this->redirectToRoute('member_membership_fee');
    }

    public function generalMeetingAction(Request $request)
    {
        $userService = $this->get(UserService::class);
        /** @var User $user */
        $user = $this->getUser();
        Assertion::isInstanceOf($user, User::class);
        $title = 'Présence prochaine AG';
        $generalMeetingRepository = $this->get(GeneralMeetingRepository::class);
        $latestDate = $generalMeetingRepository->getLatestDate();
        Assertion::notNull($latestDate);
        $generalMeetingPlanned = $generalMeetingRepository->hasGeneralMeetingPlanned();

        $cotisation = $userService->getLastSubscription($user);
        $needsMembersheepFeePayment = $latestDate->getTimestamp() > strtotime("+14 day", (int) $cotisation['date_fin']);

        if ($needsMembersheepFeePayment) {
            return $this->render('admin/association/membership/generalmeeting_membersheepfee.html.twig', [
                'title' => $title,
                'latest_date' => $latestDate,
            ]);
        }

        $attendee = $generalMeetingRepository->getAttendee($user->getUsername(), $latestDate);
        $lastGeneralMeetingDescription = $generalMeetingRepository->obtenirDescription($latestDate);

        $defaultPresence = 0;
        $defaultPowerId = null;
        if (null !== $attendee) {
            $defaultPresence = $attendee->getPresence();
            $defaultPowerId = $attendee->getPowerId();
        }

        $form = $this->createFormBuilder()
            ->add('presence', ChoiceType::class, ['expanded' => true, 'choices' => ['Oui' => 1, 'Non' => 2, 'Je ne sais pas encore' => 0]])
            ->add(
                'id_personne_avec_pouvoir',
                ChoiceType::class,
                [
                    'choices' => array_flip($generalMeetingRepository->getPowerSelectionList($latestDate, $user->getUsername())),
                    'label' => 'Je donne mon pouvoir à',
                    'required' => false,
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Confirmer'])
            ->setData([
                'presence' => $defaultPresence,
                'id_personne_avec_pouvoir' => $defaultPowerId,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            if (null !== $attendee) {
                $ok = $generalMeetingRepository->editAttendee(
                    $user->getUsername(),
                    $latestDate,
                    $data['presence'],
                    (int) $data['id_personne_avec_pouvoir']
                );
            } else {
                $ok = $generalMeetingRepository->addAttendee(
                    $user->getId(),
                    $latestDate,
                    $data['presence'],
                    (int) $data['id_personne_avec_pouvoir']
                );
            }

            if ($ok) {
                $this->log('Modification de la présence et du pouvoir de la personne physique');
                $this->addFlash('success', 'La présence et le pouvoir ont été modifiés');

                return $this->redirectToRoute('member_general_meeting');
            }
            $this->addFlash('error', 'Une erreur est survenue lors de la modification de la présence et du pouvoir');
        }

        $attendeesWithPower = $generalMeetingRepository->getAttendees($latestDate, 'nom', 'asc', $user->getId());

        $generalMeetingQuestionRepository = $this->get(GeneralMeetingQuestionRepository::class);
        $generalMeetingVoteRepository = $this->get(GeneralMeetingVoteRepository::class);

        $currentQuestion = $generalMeetingQuestionRepository->loadNextOpenedQuestion($latestDate);

        $voteForCurrentQuestion = null;
        if (null !== $currentQuestion) {
            $voteForCurrentQuestion = $generalMeetingVoteRepository->loadByQuestionIdAndUserId($currentQuestion->getId(), $this->getUserId());
        }

        $questionResults = [];
        foreach ($generalMeetingQuestionRepository->loadClosedQuestions($latestDate) as $question) {
            $results = $generalMeetingVoteRepository->getResultsForQuestionId($question->getId());

            $questionResults[] = [
                'question' => $question,
                'count_oui' => $results[GeneralMeetingVote::VALUE_YES],
                'count_non' => $results[GeneralMeetingVote::VALUE_NO],
                'count_abstention' => $results[GeneralMeetingVote::VALUE_ABSTENTION],
            ];
        }

        return $this->render('admin/association/membership/generalmeeting.html.twig', [
            'question_results' => $questionResults,
            'question' => $currentQuestion,
            'vote_for_current_question' => $voteForCurrentQuestion,
            'vote_labels_by_values' => GeneralMeetingVote::getVoteLabelsByValue(),
            'title' => $title,
            'latest_date' => $latestDate,
            'form' => $form->createView(),
            'reports' => $this->prepareGeneralMeetingsReportsList(),
            'general_meeting_planned' => $generalMeetingPlanned,
            'last_general_meeting_description' => $lastGeneralMeetingDescription,
            'personnes_avec_pouvoir' => $attendeesWithPower,
        ]);
    }

    public function generalMeetingVoteAction(Request $request)
    {
        $generalMeetingRepository = $this->get(GeneralMeetingRepository::class);
        $generalMeetingQuestionRepository = $this->get(GeneralMeetingQuestionRepository::class);
        $generalMeetingVoteRepository = $this->get(GeneralMeetingVoteRepository::class);

        if (null === ($questionId = $request->get('questionId'))) {
            throw $this->createNotFoundException('QuestionId manquant');
        }

        if (false === GeneralMeetingVote::isValueAllowed($vote = $request->query->getAlpha('vote'))) {
            throw $this->createNotFoundException('Vote manquant');
        }

        /** @var GeneralMeetingQuestion $question */
        $question = $generalMeetingQuestionRepository->get($questionId);

        if (null === $question) {
            throw $this->createNotFoundException('QuestionId missing');
        }

        $redirection = $this->redirectToRoute('member_general_meeting');

        if (false === $question->hasStatusOpened()) {
            $this->addFlash('error', "Ce vote n'est pas ouvert");
            return $redirection;
        }

        $userId = $this->getUserId();

        if (null !== $generalMeetingVoteRepository->loadByQuestionIdAndUserId($questionId, $userId)) {
            $this->addFlash('error', 'Vous avez déjà voté pour cette question');
            return $redirection;
        }

        $weight = 1 + count($generalMeetingRepository->getAttendees($question->getDate(), 'nom', 'asc', $userId));

        $generalMeetingVote = new GeneralMeetingVote();
        $generalMeetingVote
            ->setQuestionId($question->getId())
            ->setUserId($this->getUserId())
            ->setWeight($weight)
            ->setValue($vote)
            ->setCreatedAt(new \DateTime())
        ;

        $generalMeetingVoteRepository->save($generalMeetingVote);

        $this->addFlash('notice', 'Votre vote a été pris en compte');

        return $redirection;
    }

    public function generalMettingDownloadReportAction($filename)
    {
        $reports = $this->prepareGeneralMeetingsReportsList();

        if (!isset($reports[$filename])) {
            throw $this->createNotFoundException();
        }

        if ($this->getUser()->hasRole('ROLE_MEMBER_EXPIRED')) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($reports[$filename]['path']);
    }

    private function prepareGeneralMeetingsReportsList()
    {
        $dir = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . '/htdocs/uploads/general_meetings_reports';
        if (!is_dir($dir)) {
            return [];
        }

        $finder = new Finder();
        $files = $finder->name("*.pdf")->in($dir);

        $reports = [];
        foreach ($files as $file) {
            $reports[$file->getFilename()] = [
                'date' => substr($file->getFilename(), 0, 10),
                'label' => substr($file->getFilename(), 11, -4),
                'filename' => $file->getFilename(),
                'path' => $file->getRealPath(),
            ];
        }

        krsort($reports);

        return $reports;
    }

    public function techletterAction()
    {
        return $this->render(':site/member:techletter.html.twig', [
            'subscribed' => $this->get('ting')->get(TechletterSubscriptionsRepository::class)->hasUserSubscribed($this->getUser()),
            'feeUpToDate' => ($this->getUser() !== null and $this->getUser()->getLastSubscription() > new \DateTime()),
            'token' => $this->get('security.csrf.token_manager')->getToken('techletter_subscription'),
            'techletter_history' => $this->get('ting')->get(SendingRepository::class)->getAllPastSent(),
        ]);
    }

    public function techletterSubscribeAction(Request $request)
    {
        $user = $this->getUser();
        $token = $this->get('security.csrf.token_manager')->getToken('techletter_subscription');

        if (
            $user === null
            || $user->getLastSubscription() < new \DateTime()
            || $request->request->has('_csrf_token') === false
            || $request->request->get('_csrf_token') !== $token->getValue()
        ) {
            throw $this->createAccessDeniedException('You cannot subscribe to the techletter');
        }

        $this->addFlash('success', "Vous êtes maintenant abonné à la veille de l'AFUP");

        $this->get('ting')->get(TechletterSubscriptionsRepository::class)->subscribe($user);

        return $this->redirectToRoute('member_techletter');
    }

    public function techletterUnsubscribeAction(Request $request)
    {
        $techletterUnsubscriptionRepository = $this->get('ting')->get(TechletterUnsubscriptionsRepository::class);
        $techletterUnsubscription = $techletterUnsubscriptionRepository->createFromUser($this->getUser());
        $techletterUnsubscriptionRepository->save($techletterUnsubscription);

        $this->addFlash('success', "Vous êtes maintenant désabonné à la veille de l'AFUP");

        return $this->redirectToRoute('member_techletter');
    }
}
