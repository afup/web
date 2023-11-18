<?php

namespace AppBundle\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MailchimpSynchronizer
{
    /**
     * @var \AppBundle\Mailchimp\Mailchimp
     */
    private $mailchimp;

    /**
     * @var TechletterSubscriptionsRepository
     */
    private $subscriptionsRepository;

    /**
     * @var string
     */
    private $listId;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \AppBundle\Mailchimp\Mailchimp $mailchimp
     * @param TechletterSubscriptionsRepository $subscriptionsRepository
     * @param string $listId
     */
    public function __construct(\AppBundle\Mailchimp\Mailchimp $mailchimp, TechletterSubscriptionsRepository $subscriptionsRepository, $listId)
    {
        $this->mailchimp = $mailchimp;
        $this->subscriptionsRepository = $subscriptionsRepository;
        $this->listId = $listId;
        $this->logger = new NullLogger();
    }

    public function synchronize()
    {
        $subscribdedEmailsOnMailchimp = $this->getSubscribedEmailsOnMailchimp();
        $subscribdedEmailsOnWebsite = $this->getSubscribedEmailsOnWebsite();

        $this->unsubscribeAddresses(array_diff($subscribdedEmailsOnMailchimp, $subscribdedEmailsOnWebsite));
        $this->subscribeAddresses(array_diff($subscribdedEmailsOnWebsite, $subscribdedEmailsOnMailchimp));
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param array $emails
     */
    private function unsubscribeAddresses(array $emails)
    {
        foreach ($emails as $email) {
            $this->logger->info('Unsubscribe {address} to techletter', ['address' => $email]);
            $this->mailchimp->unSubscribeAddress($this->listId, $email);
        }
    }

    /**
     * @param array $emails
     */
    private function subscribeAddresses(array $emails)
    {
        foreach ($emails as $email) {
            $this->logger->info('Subscribe {address} to techletter', ['address' => $email]);
            $this->mailchimp->subscribeAddressWithoutConfirmation($this->listId, $email);
        }
    }

    /**
     * @return array
     */
    private function getSubscribedEmailsOnWebsite()
    {
        $subscribdedEmails =  [];
        foreach ($this->subscriptionsRepository->getSubscribedEmails() as $row) {
            $subscribdedEmails[] = $row['email'];
        }

        return $subscribdedEmails;
    }

    /**
     * @return array
     */
    private function getSubscribedEmailsOnMailchimp()
    {
        $mailsOnMailchimp = $this->mailchimp->getAllSubscribedMembersAddresses($this->listId);

        return $mailsOnMailchimp;
    }
}
