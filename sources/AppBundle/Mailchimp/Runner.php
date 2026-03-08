<?php

declare(strict_types=1);

namespace AppBundle\Mailchimp;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class Runner
{
    public function __construct(
        #[Autowire('@app.mailchimp_api')]
        private Mailchimp $mailchimp,
        private UserRepository $userRepository,
        #[Autowire('%mailchimp_members_list%')]
        private string $membersListId,
    ) {}

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
        $users = $this->userRepository->getActiveMembers();
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
        $users = $this->userRepository->getUsersByEndOfMembership($dateUnsubscribe);
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
        $users = $this->userRepository->getUsersByEndOfMembership($dateNextYear);
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
