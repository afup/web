<?php

declare(strict_types=1);

namespace AppBundle\Mailchimp;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;

class Runner
{
    /**
     * Runner constructor.
     * @param $membersListId
     * @param string $membersListId
     */
    public function __construct(
        private readonly Mailchimp $mailchimp,
        private readonly UserRepository $userRepository,
        /**
         * @var string id of the mailchimp list to use
         */
        private $membersListId,
    ) {
    }

    /**
     * Add all active members to the list
     * @return array list of errors
     */
    public function initList(): array
    {
        $errors = [];
        /**
         * @var User[] $users
         */
        $users = $this->userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL);
        foreach ($users as $user) {
            // Add to members list
            try {
                $this->mailchimp->subscribeAddress($this->membersListId, $user->getEmail());
            } catch (\Exception $e) {
                $errors[] = [$user->getEmail(), $e->getMessage()];
            }
        }

        return $errors;
    }

    /**
     * Add new users and remove old users
     * @return array list of errors
     */
    public function updateList(): array
    {
        $errors = [];
        // First - delete expired members
        $dateUnsubscribe = new \DateTimeImmutable('-15 day');
        /**
         * @var User[] $users
         */
        $users = $this->userRepository->getUsersByEndOfMembership($dateUnsubscribe, UserRepository::USER_TYPE_ALL);
        foreach ($users as $user) {
            // Delete from members list
            try {
                $this->mailchimp->unSubscribeAddress($this->membersListId, $user->getEmail());
            } catch (\Exception $e) {
                $errors[] = [$user->getEmail(), $e->getMessage()];
            }
        }
        // Then - add new members
        $dateNextYear = new \DateTimeImmutable('+1 year - 1 day');
        $users = $this->userRepository->getUsersByEndOfMembership($dateNextYear, UserRepository::USER_TYPE_ALL);
        foreach ($users as $user) {
            // Add to the members list
            try {
                $this->mailchimp->subscribeAddress($this->membersListId, $user->getEmail());
            } catch (\Exception $e) {
                $errors[] = [$user->getEmail(), $e->getMessage()];
            }
        }

        return $errors;
    }
}
