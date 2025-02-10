<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingQuestionFormType;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AddAction
{
    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;
    private GeneralMeetingRepository $generalMeetingRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private Environment $twig;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        GeneralMeetingRepository $generalMeetingRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, $date)
    {
        $date = \DateTimeImmutable::createFromFormat('U', $date);
        $generalMeeting = $this->generalMeetingRepository->findOneByDate($date);
        if (!$generalMeeting) {
            throw new NotFoundHttpException(sprintf('L\'assemblée générale en date du %s n\'a pas été trouvée', $date->format('d/m/Y')));
        }

        $question = new GeneralMeetingQuestion();
        $question->setDate($generalMeeting['date']);
        $question->setCreatedAt(new \DateTime());

        $form = $this->formFactory->create(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->generalMeetingQuestionRepository->save($question);
            $this->flashBag->add('notice', 'La question a été ajoutée');

            return new RedirectResponse($this->urlGenerator->generate('admin_members_general_vote_list', [
                'date' =>  $question->getDate()->format('U')
            ]));
        }

        return new Response($this->twig->render('admin/members/general_meeting_question/add.html.twig', [
            'general_meeting' => $generalMeeting,
            'form' => $form->createView(),
        ]));
    }
}
