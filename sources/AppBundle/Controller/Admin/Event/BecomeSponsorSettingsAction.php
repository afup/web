<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Entity\Repository\SponsorshipSettingsRepository;
use AppBundle\Event\Entity\SponsorshipSettings;
use AppBundle\Event\Form\SponsorshipSettingsType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Sponsorship\SponsorshipLeadMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Réglages de la page publique /become-sponsor.
 */
final class BecomeSponsorSettingsAction extends AbstractController
{
    public function __construct(private readonly SponsorshipSettingsRepository $sponsorshipSettingsRepository) {}

    public function __invoke(Request $request): Response
    {
        $settings = $this->sponsorshipSettingsRepository->get();

        $form = $this->createForm(SponsorshipSettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach (SponsorshipLeadMail::LANGUAGES as $language) {
                $this->moveSponsorshipFile($form, $language);
            }

            $this->sponsorshipSettingsRepository->save($settings);

            $this->addFlash('notice', 'La page devenir sponsor a été modifiée');

            return $this->redirectToRoute('admin_event_become_sponsor');
        }

        $sponsorshipFilePaths = [];
        foreach (SponsorshipLeadMail::LANGUAGES as $language) {
            $sponsorshipFilePaths[$language] = SponsorshipLeadMail::hasSponsorshipFileForLanguage($language)
                ? SponsorshipLeadMail::getSponsorshipFilePublicPath($language)
                : null;
        }

        return $this->render('admin/event/become_sponsor.html.twig', [
            'form' => $form->createView(),
            'sponsorship_file_paths' => $sponsorshipFilePaths,
        ]);
    }

    /**
     * @param FormInterface<SponsorshipSettings> $form
     */
    private function moveSponsorshipFile(FormInterface $form, string $language): void
    {
        $uploadedFile = $form->get('sponsor_file_' . $language)->getData();
        if ($uploadedFile instanceof UploadedFile) {
            $dir = Event::getSponsorFileDir();
            if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
            $uploadedFile->move($dir, SponsorshipLeadMail::getSponsorshipFileName($language));
        }
    }
}
