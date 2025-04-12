<?php

declare(strict_types=1);


namespace AppBundle\Security;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TalkVoter extends Voter
{
    private SpeakerRepository $speakerRepository;

    public function __construct(SpeakerRepository $speakerRepository)
    {
        $this->speakerRepository = $speakerRepository;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool
    {
        if ($attribute !== 'edit' || !$subject instanceof Talk) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof GithubUser) {
            return false;
        }

        $speakers = $this->speakerRepository->getSpeakersByTalk($subject);

        // All speakers associated to a talk can edit the talk
        foreach ($speakers as $speaker) {
            if ($speaker->getUser() === $user->getId()) {
                return true;
            }
        }
        return false;
    }
}
