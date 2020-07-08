<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Association\Model\User;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\TicketSpecialPriceType;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\TicketSpecialPrice;
use Assert\Assertion;
use DateTime;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class SpecialPriceAction
{
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var TicketSpecialPriceRepository */
    private $ticketSpecialPriceRepository;
    /** @var Security */
    private $security;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var Environment */
    private $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        Security $security,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->ticketSpecialPriceRepository = $ticketSpecialPriceRepository;
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->get('id');
        $event = null;
        $form = null;
        if ($id !== null) {
            $event = $this->eventActionHelper->getEventById($id);
            /** @var User $user */
            $user = $this->security->getUser();
            Assertion::isInstanceOf($user, User::class);

            $specialPrice = new TicketSpecialPrice();
            $specialPrice
                ->setToken(base64_encode(random_bytes(30)))
                ->setEventId($event->getId())
                ->setDateStart(new DateTime())
                ->setDateEnd($event->getDateEndSales())
                ->setCreatedOn(new DateTime())
                ->setCreatorId($user->getId());

            $form = $this->formFactory->create(TicketSpecialPriceType::class, $specialPrice);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->ticketSpecialPriceRepository->save($form->getData());
                $this->flashBag->add('notice', 'Le token a été enregistré');

                return new RedirectResponse($this->urlGenerator->generate('admin_event_special_price',
                    ['id' => $event->getId()]));
            }
        }

        return new Response($this->twig->render('admin/event/special_price.html.twig', [
            'special_prices' => $event === null ? [] : $this->ticketSpecialPriceRepository->getByEvent($event),
            'event' => $event,
            'title' => 'Gestion des prix custom',
            'form' => $form === null ? null : $form->createView(),
            'event_select_form' => $this->formFactory->create(EventSelectType::class, $event)->createView(),
        ]));
    }
}
