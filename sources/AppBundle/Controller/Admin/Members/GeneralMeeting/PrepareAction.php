<?php

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
use Twig\Environment;

class PrepareAction
{
    use DbLoggerTrait;

    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var Environment */
    private $twig;
    /** @var GeneralMeetingRepository */
    private $generalMeetingRepository;

    public function __construct(
        GeneralMeetingRepository $generalMeetingRepository,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $form = $this->formFactory->create(PrepareFormType::class, ['date' => new DateTime()]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->generalMeetingRepository->prepare($data['date'], $data['description'])) {
                $this->log('Ajout de la préparation des personnes physiques à l\'assemblée générale');
                $this->flashBag->add('notice', 'La préparation des personnes physiques a été ajoutée');

                return new RedirectResponse($request->getRequestUri());
            }
            $this->flashBag->add('erreur', 'Une erreur est survenue lors de la préparation des personnes physiques');
        }

        return new Response($this->twig->render('admin/members/general_meeting/prepare.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
