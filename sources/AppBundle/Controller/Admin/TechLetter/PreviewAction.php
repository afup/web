<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\TechLetter\Model\TechLetterFactory;
use AppBundle\Veille\Entity\Repository\EnvoiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PreviewAction extends AbstractController
{
    public function __construct(
        private readonly EnvoiRepository $envoiRepository,
        private readonly TechLetterFactory $techLetterFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        $sendingId = $request->request->getInt('techletterId');
        $envoi = $this->envoiRepository->find($sendingId);

        if ($envoi === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }
        if ($envoi->envoyeMailchimp === true) {
            throw $this->createAccessDeniedException('You cannot edit a sent techletter');
        }
        if ($this->isCsrfTokenValid('techletterPreview', $request->request->get('_csrf_token')) === false) {
            throw $this->createAccessDeniedException('You cannot edit this techletter');
        }

        $techletter = $this->techLetterFactory->createTechLetterFromJson($request->request->get('techletter'));
        // @todo could be better elsewhere
        $envoi->contenu = json_encode($techletter->jsonSerialize());
        $this->envoiRepository->save($envoi);

        return $this->render('admin/techletter/mail_template.html.twig', [
            'preview' => true,
            'tech_letter' => $techletter,
        ]);
    }
}
