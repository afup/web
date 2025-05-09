<?php

declare(strict_types=1);

namespace AppBundle\Mailchimp;

use AppBundle\Association\Model\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class MailchimpMembersAutoListSynchronizer
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly Mailchimp $mailchimp,
        private readonly UserRepository $userRepository,
        private readonly string $listId,
    ) {
        $this->logger = new NullLogger();
    }

    public function synchronize(): void
    {
        $subscribedEmailsOnMailchimp = array_map('strtolower', $this->mailchimp->getAllSubscribedMembersAddresses($this->listId));
        $unSubscribedEmailsOnMailchimp = array_map('strtolower', $this->mailchimp->getAllUnSubscribedMembersAddresses($this->listId));
        $cleanedEmailsOnMailchimp = array_map('strtolower', $this->mailchimp->getAllCleanedMembersAddresses($this->listId));
        $subscribedEmailsOnWebsite = array_map('strtolower', $this->getSubscribedEmailsOnWebsite());

        $addressesToArchive = array_diff($subscribedEmailsOnMailchimp, $subscribedEmailsOnWebsite);
        // Vu qu'on archive les personnes qui ne sont plus à jour de cotisation, les adresses unsubscribed sont seulemnt les personnes
        // qui ont optout. On ne peux techniquement pas les ajouter et fonctionnellelement il faudrait fournir les infos sur leur optin
        // on ne cherche donc pas à ajouter de nouveaux ces personnes dans les subscribers
        $addressesToSubscribe = array_diff($subscribedEmailsOnWebsite, $subscribedEmailsOnMailchimp, $unSubscribedEmailsOnMailchimp);

        // Les adresses cleaned sont par exemple des hard bounces : on ne peux pas les passer en subscribred dans mailchimp
        // Il peuvent tout de même être des membres à jour de cotisation, on va ici éviter des erreurs lors de la synchro en les ignornant
        $addressesToSubscribe = array_diff($addressesToSubscribe, $cleanedEmailsOnMailchimp);

        $this->archiveAddresses($addressesToArchive);
        $this->subscribeAddresses($addressesToSubscribe);
    }

    /**
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    private function archiveAddresses(array $emails): void
    {
        // Ici on archive les contacts pour deux raisons :
        // - Cela permet de distinguer les personnes qui ont réellement voulu unsubscriber de personnes qu'on en enlevées nous même de la liste
        // - Les contacts archivés ne sont pas comptés dans la facturation et permet de limiter le coût de Mailchimp
        foreach ($emails as $email) {
            $this->logger->info('Unsubscribe {address} to techletter', ['address' => $email]);
            $this->mailchimp->archiveAddress($this->listId, $email);
        }
    }

    private function subscribeAddresses(array $emails): void
    {
        $errors = [];

        foreach ($emails as $email) {
            $this->logger->info('Subscribe {address} to techletter', ['address' => $email]);

            try {
                $this->mailchimp->subscribeAddressWithoutConfirmation($this->listId, $email);
            } catch (\Exception $e) {
                $this->logger->error('Error subscribing {address} to techletter', ['address' => $email, 'message' => $e->getMessage()]);
                $errors[$email] = $email . ' : ' . $e->getMessage();
            }
        }

        if ($errors !== []) {
            throw new \RuntimeException('Errors during subscribeAddresses : ' . implode(PHP_EOL, $errors));
        }
    }

    private function getSubscribedEmailsOnWebsite(): array
    {
        $subscribdedEmails =  [];

        foreach ($this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL) as $user) {
            $subscribdedEmails[] = $user->getEmail();
        }

        return $subscribdedEmails;
    }
}
