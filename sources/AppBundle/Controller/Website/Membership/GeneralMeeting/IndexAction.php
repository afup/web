<?php

declare(strict_types=1);

namespace AppBundle\Controller\Website\Membership\GeneralMeeting;

use Afup\Site\Droits;
use AppBundle\Association\Model\GeneralMeetingVote;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\Association\Model\Repository\GeneralMeetingVoteRepository;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\AuditLog\Audit;
use AppBundle\GeneralMeeting\Attendee;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\GeneralMeeting\ReportListBuilder;
use AppBundle\Security\Authentication;
use AppBundle\Twig\ViewRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly ViewRenderer $view,
        private readonly UserService $userService,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        private readonly GeneralMeetingVoteRepository $generalMeetingVoteRepository,
        private readonly ReportListBuilder $reportListBuilder,
        private readonly Droits $droits,
        private readonly Audit $audit,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(Request $request): Response
    {
        $userService = $this->userService;
        $user = $this->authentication->getAfupUser();
        $title = 'Présence prochaine AG';
        $generalMeetingRepository = $this->generalMeetingRepository;
        $latestDate = $generalMeetingRepository->getLatestAttendanceDate();
        Assert::notNull($latestDate);
        $generalMeetingPlanned = $generalMeetingRepository->hasGeneralMeetingPlanned();

        $cotisation = $userService->getLastSubscription($user);
        $needsMembersheepFeePayment = $latestDate->getTimestamp() > strtotime("+14 day", (int) $cotisation['date_fin']);

        if ($needsMembersheepFeePayment) {
            return $this->view->render('admin/association/membership/generalmeeting_membersheepfee.html.twig', [
                'title' => $title,
                'latest_date' => $latestDate,
            ]);
        }

        $attendee = $generalMeetingRepository->getAttendee($user->getUsername(), $latestDate);
        $lastGeneralMeetingDescription = $generalMeetingRepository->obtenirDescription($latestDate);

        $data = [
            'presence' => 0,
            'id_personne_avec_pouvoir' => null,
        ];
        if ($attendee instanceof Attendee) {
            $data['presence'] = $attendee->getPresence();
            $data['id_personne_avec_pouvoir'] = $attendee->getPowerId();
        }

        $form = $this->createFormBuilder($data, [
            'constraints' => [
                new Constraints\Callback(callback: static function (array $data, ExecutionContextInterface $context): void {
                    if ($data['presence'] === 1 && $data['id_personne_avec_pouvoir']) {
                        $context
                            ->buildViolation("Vous ne pouvez pas donner votre pouvoir et indiquer que vous participez en même temps.")
                            ->atPath('[id_personne_avec_pouvoir]')
                            ->addViolation()
                        ;
                    }
                },
                ),
            ],
        ])
            ->add('presence', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'Je participe' => 1,
                    'Je ne participe pas' => 2,
                ],
            ])
            ->add('id_personne_avec_pouvoir', ChoiceType::class, [
                'choices' => array_flip($generalMeetingRepository->getPowerSelectionList($latestDate, $user->getUsername())),
                'label' => 'Je donne mon pouvoir à',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Confirmer',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($attendee instanceof Attendee) {
                $ok = $generalMeetingRepository->editAttendee(
                    $user->getUsername(),
                    $latestDate,
                    $data['presence'],
                    (int) $data['id_personne_avec_pouvoir'],
                );
            } else {
                $ok = $generalMeetingRepository->addAttendee(
                    $user->getId(),
                    $latestDate,
                    $data['presence'],
                    (int) $data['id_personne_avec_pouvoir'],
                );
            }

            if ($ok) {
                $this->audit->log('Modification de la présence et du pouvoir de la personne physique');
                $this->addFlash('success', 'La présence et le pouvoir ont été modifiés');

                return $this->redirectToRoute('member_general_meeting');
            }
            $this->addFlash('error', 'Une erreur est survenue lors de la modification de la présence et du pouvoir');
        }

        $attendeesWithPower = $generalMeetingRepository->getAttendees($latestDate, 'nom', 'asc', $user->getId());

        $generalMeetingQuestionRepository = $this->generalMeetingQuestionRepository;
        $generalMeetingVoteRepository = $this->generalMeetingVoteRepository;

        $currentQuestion = $generalMeetingQuestionRepository->loadNextOpenedQuestion($latestDate);

        $voteForCurrentQuestion = null;
        if (null !== $currentQuestion) {
            $voteForCurrentQuestion = $generalMeetingVoteRepository->loadByQuestionIdAndUserId($currentQuestion->getId(), $this->droits->obtenirIdentifiant());
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

        return $this->view->render('admin/association/membership/generalmeeting.html.twig', [
            'question_results' => $questionResults,
            'question' => $currentQuestion,
            'vote_for_current_question' => $voteForCurrentQuestion,
            'vote_labels_by_values' => GeneralMeetingVote::getVoteLabelsByValue(),
            'title' => $title,
            'latest_date' => $latestDate,
            'form' => $form->createView(),
            'reports' => $this->reportListBuilder->prepareGeneralMeetingsReportsList(),
            'general_meeting_planned' => $generalMeetingPlanned,
            'last_general_meeting_description' => $lastGeneralMeetingDescription,
            'personnes_avec_pouvoir' => $attendeesWithPower,
        ]);
    }
}
