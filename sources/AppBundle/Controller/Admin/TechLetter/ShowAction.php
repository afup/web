<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\TechLetter\Model\TechLetterFactory;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VEILLE_READER')]
final class ShowAction extends AbstractController
{
    public function __construct(
        private readonly EnvoiRepository $envoiRepository,
        private readonly TechLetterFactory $techLetterFactory,
    ) {}

    public function __invoke(int $techletterId): Response
    {
        $envoi = $this->envoiRepository->find($techletterId);
        if ($envoi === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }

        $mailContent = $this->renderView('admin/techletter/mail_template.html.twig', [
            'tech_letter' => $this->techLetterFactory->createTechLetterFromJson($envoi->contenu),
            'preview' => false,
        ]);

        return $this->render('admin/techletter/show.html.twig', [
            'title' => "Veille de l'AFUP",
            'envoi' => $envoi,
            'mail_content' => $mailContent,
        ]);
    }
}
