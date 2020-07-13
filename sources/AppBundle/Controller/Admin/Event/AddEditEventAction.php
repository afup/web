<?php

namespace AppBundle\Controller\Admin\Event;


use AppBundle\Event\Form\EventType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class AddEditEventAction
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        EventRepository $eventRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request, $id)
    {
        $event = new Event();
        if ($id !== null) {
            $event = $this->eventRepository->get($id);
            if ($event === null) {
                $this->flashBag->add('error', 'Evénement introuvable');
                return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
            }
        }

        $form = $this->formFactory->create(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventRepository->save($event);
        }
        return new Response($this->twig->render('admin/event/form.html.twig', ['form' => $form->createView()]));
    }
}
