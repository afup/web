<?php


namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InvitationMail
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(Mailer $mailer, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * Send mail to please a user to join a company as a member
     *
     * @param CompanyMember $companyMember The company who sends the invitation
     * @param CompanyMemberInvitation $invitation The invitation to send
     * @return bool
     */
    public function sendInvitation(CompanyMember $companyMember, CompanyMemberInvitation $invitation)
    {
        $text = $this->translator->trans('mail.invitationMembership.text',
            [
                '%firstname%' => $companyMember->getFirstName(),
                '%lastname%' => $companyMember->getLastName(),
                '%link%' =>$this->router->generate(
                    'company_invitation',
                    ['invitationId' => $invitation->getId(), 'token' => $invitation->getToken()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        );

        return $this->mailer->sendTransactional(new Message(
            sprintf('%s vous invite à profiter de son compte "Membre AFUP"', $companyMember->getCompanyName()),
            MailUserFactory::sponsors(),
            new MailUser($invitation->getEmail())
        ), $text);
    }
}
