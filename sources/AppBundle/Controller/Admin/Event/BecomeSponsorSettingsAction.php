<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Entity\Repository\ParametrageSponsoringRepository;
use AppBundle\Event\Entity\ParametrageSponsoring;
use AppBundle\Event\Form\ParametrageSponsoringType;
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
    public function __construct(private readonly ParametrageSponsoringRepository $parametrageSponsoringRepository) {}

    public function __invoke(Request $request): Response
    {
        $parametrage = $this->parametrageSponsoringRepository->get();

        $form = $this->createForm(ParametrageSponsoringType::class, $parametrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach (SponsorshipLeadMail::LANGUAGES as $language) {
                $this->moveSponsorshipFile($form, $language);
            }

            $this->parametrageSponsoringRepository->save($parametrage);

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
     * @param FormInterface<ParametrageSponsoring> $form
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
