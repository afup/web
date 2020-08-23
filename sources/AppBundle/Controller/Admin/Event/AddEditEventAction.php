<?php

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Form\EventType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventCouponRepository;
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
    /**
     * @var EventCouponRepository
     */
    private $couponRepository;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $twig,
        EventRepository $eventRepository,
        EventCouponRepository $couponRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->eventRepository = $eventRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->couponRepository = $couponRepository;
    }

    public function __invoke(Request $request, $id)
    {
        $event = new Event();
        $couponTxt = null;
        $action = 'ajouté';
        if ($id !== null) {
            $event = $this->eventRepository->get($id);
            if ($event === null) {
                $this->flashBag->add('error', 'Evénement introuvable');
                return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
            }
            $coupons = $this->couponRepository->couponsListForEvent($event);
            $couponTxt = [];
            foreach ($coupons as $coupon) {
                if ($coupon->getText() === null || empty($coupon->getText()) === true) {
                    continue;
                }
                $couponTxt[] = $coupon->getText();
            }
            $couponTxt = implode(',', $couponTxt);
            $action = 'modifié';
        }

        $form = $this->formFactory->create(EventType::class, $event);
        $form->get('coupons')->setData($couponTxt);
//        $form->get('cFP')->setData($event->getCFP());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->get('cFP')->getData(), $event);
//            $event->setCFP($form->get('cFP')->getData());

            $this->eventRepository->save($event);

            $couponsPost = explode(',', $form->get('coupons')->getData());
            $couponsPost = array_map('trim', $couponsPost);
            $this->couponRepository->changeCouponForEvent($couponsPost, $event);

            $this->flashBag->add('notice', 'Evénement ' . $action);
            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }
        return new Response($this->twig->render('admin/event/form.html.twig', ['form' => $form->createView()]));
    }
}
