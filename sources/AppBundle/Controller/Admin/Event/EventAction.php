<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Form\EventType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventCouponRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class EventAction
{
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private EventRepository $eventRepository;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;
    private EventCouponRepository $couponRepository;

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

        if ($id) {
            $event = $this->eventRepository->get($id);
            if ($event === null) {
                $this->flashBag->add('error', 'Évènement non trouvé');
                return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
            }
        }

        $form = $this->formFactory->create(EventType::class, $event);

        if ($event->getId() && $couponsImploded = $this->couponRepository->couponsListForEventImploded($event)) {
            $form->get('coupons')->setData($couponsImploded);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->moveSponsorFile($event, $form, 'fr');
            $this->moveSponsorFile($event, $form, 'en');
            $this->moveRegistrationEmailFile($event, $form);

            $this->eventRepository->save($event);

            if ($form->get('coupons')->getData()) {
                $eventCoupons = explode(',', $form->get('coupons')->getData());
                $this->couponRepository->changeCouponForEvent($event, $eventCoupons);
            }

            $this->flashBag->add('notice', 'Évènement ' . ($id ? 'modifié' : 'ajouté'));
            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }

        $sponsorFilePathFR = Event::hasSponsorFile($event->getPath(), 'fr') ? Event::getSponsorFilePublicPath($event->getPath(), 'fr') : null;
        $sponsorFilePathEN = Event::hasSponsorFile($event->getPath(), 'en') ? Event::getSponsorFilePublicPath($event->getPath(), 'en') : null;
        $registrationEmailFilePath = Event::hasInscriptionAttachment($event->getPath()) ? Event::getInscriptionAttachmentPublicPath($event->getPath()) : null;

        return new Response($this->twig->render('admin/event/' . ($id ? 'edit' : 'add') . '.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'sponsor_file_path_fr' => $sponsorFilePathFR,
            'sponsor_file_path_en' => $sponsorFilePathEN,
            'registration_email_file_path' => $registrationEmailFilePath,
        ]));
    }

    private function moveSponsorFile(Event $event, FormInterface $form, string $language): void
    {
        $uploadedFile = $form->get('sponsor_file_' . $language)->getData();
        if ($uploadedFile instanceof UploadedFile) {
            $dir = Event::getSponsorFileDir();
            if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
            $filename = Event::getSponsorFilePath($event->getPath(), $language);
            $uploadedFile->move($dir, basename($filename));
        }
    }

    private function moveRegistrationEmailFile(Event $event, FormInterface $form): void
    {
        $uploadedFile = $form->get('registration_email_file')->getData();
        if ($uploadedFile instanceof UploadedFile) {
            $dir = Event::getInscriptionAttachmentDir();
            if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
            $uploadedFile->move($dir, $event->getPath() . '.pdf');
        }
    }
}
