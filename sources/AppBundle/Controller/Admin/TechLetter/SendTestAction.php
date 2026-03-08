<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\TechLetter;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use AppBundle\TechLetter\Model\Repository\SendingRepository;
use AppBundle\TechLetter\Model\TechLetterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class SendTestAction extends AbstractController
{
    public function __construct(
        private readonly SendingRepository $sendingRepository,
        private readonly Mailer $mailer,
        private readonly TechLetterFactory $techLetterFactory,
        #[Autowire('%techletter_test_email_address%')]
        private readonly string $techletterTestEmailAddress,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $sendingId = $request->query->getInt('techletterId');
        $sending = $this->sendingRepository->get($sendingId);

        if ($sending === null) {
            throw $this->createNotFoundException('Could not find this techletter');
        }

        if ($sending->getSentToMailchimp() === true) {
            throw $this->createAccessDeniedException('You send a test on a sent techletter');
        }

        $subject = sprintf("[Test] Veille de l'AFUP du %s", $sending->getSendingDate()->format('d/m/Y'));

        $techLetter = $this->techLetterFactory->createTechLetterFromJson($sending->getTechletter());

        $message = new Message($subject, null, new MailUser($this->techletterTestEmailAddress));
        $this->mailer->renderTemplate($message,'admin/techletter/mail_template.html.twig', [
            'tech_letter' => $techLetter,
            'preview' => false,
        ]);
        $this->mailer->send($message);

        $this->addFlash('notice', 'Le mail de test a été envoyé');

        return $this->redirectToRoute('admin_techletter_generate', ['techletterId' => $sendingId]);
    }
}
