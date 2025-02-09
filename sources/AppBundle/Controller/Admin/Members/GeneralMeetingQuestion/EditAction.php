<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeetingQuestion;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use AppBundle\Association\Model\Repository\GeneralMeetingQuestionRepository;
use AppBundle\GeneralMeeting\GeneralMeetingQuestionFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EditAction
{
    private GeneralMeetingQuestionRepository $generalMeetingQuestionRepository;
    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(
        GeneralMeetingQuestionRepository $generalMeetingQuestionRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->generalMeetingQuestionRepository = $generalMeetingQuestionRepository;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request, $id)
    {
        /** @var GeneralMeetingQuestion $question */
        $question = $this->generalMeetingQuestionRepository->get($id);

        if (null === $question) {
            throw new NotFoundHttpException(sprintf('Question %d not found', $id));
        }

        if (true !== $question->hasStatusWaiting()) {
            throw new AccessDeniedHttpException('Seules les questions en attente peuvent être modifiées');
        }

        $form = $this->formFactory->create(GeneralMeetingQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->generalMeetingQuestionRepository->save($question);
            $this->flashBag->add('notice', 'La question a été modifiée');

            return new RedirectResponse($this->urlGenerator->generate('admin_members_general_vote_list', [
                'date' => $question->getDate()->format('U')
            ]));
        }

        return new Response($this->twig->render('admin/members/general_meeting_question/edit.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
