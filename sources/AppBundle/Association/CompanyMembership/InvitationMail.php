<?php


namespace AppBundle\Association\CompanyMembership;

use Afup\Site\Utils\Mail;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\CompanyMemberInvitation;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class InvitationMail
{
    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(Mail $mail, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->mail = $mail;
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

        return $this->mail->send(
            'message-transactionnel-afup-org',
            ['email' => $invitation->getEmail()],
            [
                'content' => $text,
                'title' => sprintf("%s vous invite à profiter de son compte \"Membre AFUP\"", $companyMember->getCompanyName())
            ],
            [
                'subject' => sprintf("%s vous invite à profiter de son compte \"Membre AFUP\"", $companyMember->getCompanyName()),
                'force_bcc' => true,
            ]
        );
    }
}
