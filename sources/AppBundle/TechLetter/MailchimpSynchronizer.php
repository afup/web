<?php

declare(strict_types=1);

namespace AppBundle\TechLetter;

use AppBundle\Association\Model\Repository\TechletterSubscriptionsRepository;
use AppBundle\Mailchimp\Mailchimp;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MailchimpSynchronizer
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly Mailchimp $mailchimp,
        private readonly TechletterSubscriptionsRepository $subscriptionsRepository,
        private readonly string $listId,
    ) {
        $this->logger = new NullLogger();
    }

    public function synchronize(): void
    {
        $subscribdedEmailsOnMailchimp = $this->getSubscribedEmailsOnMailchimp();
        $subscribdedEmailsOnWebsite = $this->getSubscribedEmailsOnWebsite();

        $this->unsubscribeAddresses(array_diff($subscribdedEmailsOnMailchimp, $subscribdedEmailsOnWebsite));
        $this->subscribeAddresses(array_diff($subscribdedEmailsOnWebsite, $subscribdedEmailsOnMailchimp));
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    private function unsubscribeAddresses(array $emails): void
    {
        foreach ($emails as $email) {
            $this->logger->info('Unsubscribe {address} to techletter', ['address' => $email]);
            try {
                $this->mailchimp->unSubscribeAddress($this->listId, $email);
            } catch (\Exception $e) {
                $this->logger->error('Failed with message: {message}', ['message' => $e->getMessage()]);
            }
        }
    }

    private function subscribeAddresses(array $emails): void
    {
        foreach ($emails as $email) {
            $this->logger->info('Subscribe {address} to techletter', ['address' => $email]);
            try {
                $this->mailchimp->subscribeAddressWithoutConfirmation($this->listId, $email);
            } catch (\Exception $e) {
                $this->logger->error('Failed with message: {message}', ['message' => $e->getMessage()]);
            }
        }
    }

    private function getSubscribedEmailsOnWebsite(): array
    {
        $emails =  [];
        foreach ($this->subscriptionsRepository->getSubscribedEmails() as $row) {
            $emails[] = $row['email'];
        }

        return $emails;
    }

    private function getSubscribedEmailsOnMailchimp(): array
    {
        return $this->mailchimp->getAllSubscribedMembersAddresses($this->listId);
    }
}
