<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Talk;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        //TODO : à supprimer quand les actions via le formulaire auront été migrées
        if (isset($_SESSION['flash']['message'])) {
            $this->addFlash('notice', $_SESSION['flash']['message']);
        }
        if (isset($_SESSION['flash']['erreur'])) {
            $this->addFlash('error', $_SESSION['flash']['erreur']);
        }
        unset($_SESSION['flash']);

        $event = $eventSelection->event;

        $data = [
            'id' => $event->getId(),
        ];
        $filterForm = $this->filterForm($data);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();
            $data = array_filter($data);
        }

        $data['sort_key'] ??= 'talk.date_soumission';
        $data['sort_direction'] ??= 'asc';

        $sessions = $this->talkRepository->getByEventWithSpeakersAndVotes(
            event: $event,
            search: $data['q'] ?? '',
            orderBy: $data['sort_key'] . ' ' . $data['sort_direction'],
            planned: $data['planned'] ?? false,
            needMentoring: $data['needs_mentoring'] ?? false,
        );

        return $this->render('admin/talk/index.html.twig', [
            'event' => $event,
            'filter_form' => $filterForm,
            'filter' => $data,
            'event_select_form' => $eventSelection->selectForm(),
            'sessions' => $sessions,
        ]);
    }

    public function filterForm(array $data): FormInterface
    {
        return $this->formFactory->createNamedBuilder('', FormType::class, $data, [
            'csrf_protection' => false,
        ])
            ->setMethod('GET')
            ->add('q', TextType::class, ['required' => false])
            ->add('needs_mentoring', CheckboxType::class, ['required' => false])
            ->add('planned', CheckboxType::class, ['required' => false])
            ->add('id', HiddenType::class, ['required' => false])
            ->add('sort_key', HiddenType::class, ['required' => false])
            ->add('sort_direction', HiddenType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            ->getForm();
    }
}
