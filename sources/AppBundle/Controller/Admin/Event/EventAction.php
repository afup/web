<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Form\EventType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventCouponRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventCouponRepository $couponRepository,
    ) {
    }

    public function __invoke(Request $request, $id): Response
    {
        $event = new Event();

        if ($id) {
            $event = $this->eventRepository->get($id);
            if ($event === null) {
                $this->addFlash('error', 'Évènement non trouvé');
                return $this->redirectToRoute('admin_event_list');
            }
        }

        $form = $this->createForm(EventType::class, $event);

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
                $eventCoupons = explode(',', (string) $form->get('coupons')->getData());
                $this->couponRepository->changeCouponForEvent($event, $eventCoupons);
            }

            $this->addFlash('notice', 'Évènement ' . ($id ? 'modifié' : 'ajouté'));
            return $this->redirectToRoute('admin_event_list');
        }

        $sponsorFilePathFR = Event::hasSponsorFile($event->getPath(), 'fr') ? Event::getSponsorFilePublicPath($event->getPath(), 'fr') : null;
        $sponsorFilePathEN = Event::hasSponsorFile($event->getPath(), 'en') ? Event::getSponsorFilePublicPath($event->getPath(), 'en') : null;
        $registrationEmailFilePath = Event::hasInscriptionAttachment($event->getPath()) ? Event::getInscriptionAttachmentPublicPath($event->getPath()) : null;

        return $this->render('admin/event/' . ($id ? 'edit' : 'add') . '.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'sponsor_file_path_fr' => $sponsorFilePathFR,
            'sponsor_file_path_en' => $sponsorFilePathEN,
            'registration_email_file_path' => $registrationEmailFilePath,
        ]);
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
