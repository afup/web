<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members\GeneralMeeting;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\GeneralMeeting\PrepareFormType;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EditAction
{
    use DbLoggerTrait;

    private FormFactoryInterface $formFactory;
    private FlashBagInterface $flashBag;
    private Environment $twig;
    private GeneralMeetingRepository $generalMeetingRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        GeneralMeetingRepository $generalMeetingRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
    {
        $date = new DateTime('@' . $request->query->get('date'));

        $generaleMeeting = $this->generalMeetingRepository->findOneByDate($date);
        if (null === $generaleMeeting) {
            throw new NotFoundHttpException(sprintf('General meeting with date "%d" not found', $date->getTimestamp()));
        }
        $form = $this->formFactory->create(PrepareFormType::class, $generaleMeeting, ['without_date' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->generalMeetingRepository->save($generaleMeeting['date'], $data['description']);

            $this->flashBag->add('success', 'Description enregistrée');
            return new RedirectResponse($this->urlGenerator->generate('admin_members_general_meeting_edit', [
                'date' => $date->getTimestamp()
            ]));
        }

        return new Response($this->twig->render('admin/members/general_meeting/edit.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
